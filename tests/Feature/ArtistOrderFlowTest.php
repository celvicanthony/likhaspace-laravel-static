<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtistOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_mixed_artist_cart_creates_separate_orders_for_each_artist(): void
    {
        Storage::fake('local');
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $artistOne = $this->makeArtist('one@example.test', 'GCASH', 'Artist One');
        $artistTwo = $this->makeArtist('two@example.test', 'INSTAPAY', 'Artist Two');
        Storage::disk('local')->put("artist-payments/{$artistOne->id}/sample.png", 'private-qr');
        $artistOne->payment_qr_path = "artist-payments/{$artistOne->id}/sample.png";
        $artistOne->save();
        $firstArtwork = $this->makeArtwork($artistOne, 'First artwork');
        $secondArtwork = $this->makeArtwork($artistTwo, 'Second artwork');

        $this->actingAs($buyer)->withSession([
            'cart' => [$firstArtwork->id => 1, $secondArtwork->id => 2],
        ])->get(route('checkout'))->assertOk()->assertDontSee('+639123456789');

        $this->post(route('checkout.submit'), [
            'name' => $buyer->name,
            'email' => $buyer->email,
        ])->assertRedirect(route('transactions'));

        $orders = Order::with('items')->where('user_id', $buyer->id)->get();
        $this->assertCount(2, $orders);
        $this->assertSame(
            [$artistOne->id, $artistTwo->id],
            $orders->pluck('seller_id')->sort()->values()->all()
        );
        $this->assertSame(
            ['Awaiting payment'],
            $orders->pluck('status')->unique()->values()->all()
        );
        $artistOrder = $orders->firstWhere('seller_id', $artistOne->id);
        $this->assertSame('Artist One', $artistOrder->payment_recipient_name);
        $this->assertSame('+639123456789', $artistOrder->payment_recipient_identifier);
        $this->assertNotSame(
            '+639123456789',
            $artistOrder->getRawOriginal('payment_recipient_identifier')
        );
        $this->assertArrayNotHasKey('payment_recipient_identifier', $artistOrder->toArray());
        $this->actingAs($buyer)
            ->get(route('transactions.show', $artistOrder->order_code))
            ->assertOk()
            ->assertSee('+639123456789');
        $this->get(route('transactions.payment.qr', $artistOrder->order_code))->assertOk();
        $otherBuyer = $this->makeUser('client', 'other-buyer@example.test');
        $this->actingAs($otherBuyer)
            ->get(route('transactions.payment.qr', $artistOrder->order_code))
            ->assertNotFound();
    }

    public function test_payment_review_and_private_delivery_unlocks_buyer_download(): void
    {
        Storage::fake('local');
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $artist = $this->makeArtist('artist@example.test');
        $order = Order::create([
            'order_code' => 'LS-20261108-ABC12345',
            'user_id' => $buyer->id,
            'seller_id' => $artist->id,
            'buyer_name' => $buyer->name,
            'buyer_email' => $buyer->email,
            'shipping_address' => 'Digital delivery',
            'payment_method' => 'GCASH',
            'payment_status' => 'unpaid',
            'status' => 'Awaiting payment',
            'total_amount' => 100,
        ]);
        $artwork = Artwork::create([
            'user_id' => $artist->id,
            'title' => 'Sample artwork',
            'country' => 'Philippines',
            'price' => 100,
            'status' => 'approved',
        ]);
        $item = $order->items()->create([
            'artwork_id' => $artwork->id,
            'artwork_title' => 'Sample artwork',
            'artist_name' => $artist->name,
            'quantity' => 1,
            'unit_price' => 100,
            'subtotal' => 100,
        ]);

        $this->actingAs($buyer)->post(route('transactions.payment.submit', $order->order_code), [
            'payment_reference' => 'REF-001',
            'payment_proof' => $this->fakePng('receipt.png'),
        ])->assertRedirect();
        $order->refresh();
        $this->assertSame('submitted', $order->payment_status);
        Storage::disk('local')->assertExists($order->payment_proof_path);
        $this->get(route('transactions.show', $order->order_code))->assertOk();

        $this->actingAs($artist)->post(route('artist.orders.payment-review', $order->order_code), [
            'decision' => 'reject',
            'payment_review_note' => 'The transfer is not visible yet.',
        ])->assertRedirect();
        $order->refresh();
        $this->assertSame('rejected', $order->payment_status);

        $oldProofPath = $order->payment_proof_path;
        $this->actingAs($buyer)->post(route('transactions.payment.submit', $order->order_code), [
            'payment_reference' => 'REF-002',
            'payment_proof' => $this->fakePng('receipt-corrected.png'),
        ])->assertRedirect();
        $order->refresh();
        $this->assertSame('submitted', $order->payment_status);
        $this->assertNotSame($oldProofPath, $order->payment_proof_path);
        Storage::disk('local')->assertMissing($oldProofPath);

        $this->actingAs($artist)->post(route('artist.orders.payment-review', $order->order_code), [
            'decision' => 'approve',
        ])->assertRedirect();
        $this->get(route('artist.orders'))->assertOk();
        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('Processing', $order->status);
        $this->assertSame('10.00', $order->platform_commission);
        $this->assertSame('90.00', $order->artist_earnings);

        $this->actingAs($artist)->post(route('artist.orders.items.delivery', [$order->order_code, $item->id]), [
            'delivery_file' => $this->fakePng('artwork.png'),
        ])->assertRedirect();
        $order->refresh();
        $item->refresh();
        $this->assertSame('Ready for download', $order->status);
        Storage::disk('local')->assertExists($item->delivery_path);

        $this->actingAs($buyer)->post(route('transactions.items.review', [$order->order_code, $item->id]), [
            'rating' => 5,
            'comment' => 'Excellent digital artwork.',
        ])->assertForbidden();

        $this->actingAs($buyer)->get(route('transactions.items.download', [$order->order_code, $item->id]))
            ->assertOk();
        $this->post(route('transactions.items.review', [$order->order_code, $item->id]), [
            'rating' => 5,
            'comment' => 'Excellent digital artwork.',
        ])->assertRedirect();
        $this->assertDatabaseHas('artwork_reviews', [
            'order_item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'rating' => 5,
        ]);
        $this->get(route('artwork.show', $artwork->id))
            ->assertOk()
            ->assertSee('5.0/5')
            ->assertSee('Excellent digital artwork.');
        $this->post(route('transactions.items.review', [$order->order_code, $item->id]), [
            'rating' => 3,
        ])->assertStatus(409);

        $this->actingAs($artist)->get(route('artist.dashboard'))
            ->assertOk()
            ->assertSee('90.00')
            ->assertSee('10.00');

        $otherBuyer = $this->makeUser('client', 'other@example.test');
        $this->actingAs($otherBuyer)->get(route('transactions.items.download', [$order->order_code, $item->id]))
            ->assertNotFound();
    }

    public function test_artist_cannot_review_another_artists_payment(): void
    {
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $seller = $this->makeArtist('seller@example.test');
        $otherArtist = $this->makeArtist('other@example.test');
        $order = Order::create([
            'order_code' => 'LS-20261108-DEF67890',
            'user_id' => $buyer->id,
            'seller_id' => $seller->id,
            'buyer_name' => $buyer->name,
            'buyer_email' => $buyer->email,
            'shipping_address' => 'Digital delivery',
            'payment_method' => 'GCASH',
            'payment_status' => 'submitted',
            'status' => 'Payment submitted',
            'total_amount' => 100,
        ]);

        $this->actingAs($otherArtist)->post(route('artist.orders.payment-review', $order->order_code), [
            'decision' => 'approve',
        ])->assertNotFound();
        $this->assertSame('submitted', $order->fresh()->payment_status);
    }

    private function makeUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
            'profile' => [],
        ]);
    }

    private function makeArtist(string $email, string $method = 'gcash', string $name = 'Artist'): User
    {
        $artist = $this->makeUser('artist', $email);
        $artist->payment_method = $method;
        $artist->payment_account_name = $name;
        $artist->payment_account_identifier = '+639123456789';
        $artist->save();

        return $artist;
    }

    private function makeArtwork(User $artist, string $title): Artwork
    {
        return Artwork::create([
            'user_id' => $artist->id,
            'title' => $title,
            'country' => 'Philippines',
            'price' => 100,
            'status' => 'approved',
        ]);
    }

    private function fakePng(string $name): UploadedFile
    {
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=',
            true
        );

        return UploadedFile::fake()->createWithContent($name, $png);
    }
}
