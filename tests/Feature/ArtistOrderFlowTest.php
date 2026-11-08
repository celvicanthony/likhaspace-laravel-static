<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\ArtistPaymentSetting;
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
        $artistOne->paymentSettings()->updateOrCreate([], [
            'payment_qr_path' => "artist-payments/{$artistOne->id}/sample.png",
        ]);
        $firstArtwork = $this->makeArtwork($artistOne, 'First artwork');
        $secondArtwork = $this->makeArtwork($artistTwo, 'Second artwork');

        $this->actingAs($buyer)->withSession([
            'cart' => [$firstArtwork->id => 1, $secondArtwork->id => 2],
        ])->get(route('checkout'))
            ->assertOk()
            ->assertSee('+639123456789')
            ->assertSee('Artist One')
            ->assertSee('100.00')
            ->assertSee(route('checkout.artist-payment-qr', $artistOne->id), false);
        $this->get(route('checkout.artist-payment-qr', $artistOne->id))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private');

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
            ->get(route('checkout.artist-payment-qr', $artistOne->id))
            ->assertNotFound();
        $this->actingAs($otherBuyer)
            ->get(route('transactions.payment.qr', $artistOrder->order_code))
            ->assertNotFound();
    }

    public function test_buy_now_adds_selected_artwork_and_opens_checkout(): void
    {
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $artist = $this->makeArtist('artist@example.test');
        $artwork = $this->makeArtwork($artist, 'Buy now artwork');

        $this->actingAs($buyer)
            ->get(route('artwork.show', $artwork))
            ->assertOk()
            ->assertSee(route('cart.buy-now', $artwork), false)
            ->assertSee('Buy Now');

        $this->post(route('cart.buy-now', $artwork))
            ->assertRedirect(route('checkout'))
            ->assertSessionHas('cart', [$artwork->id => 1]);

        $this->get(route('checkout'))
            ->assertOk()
            ->assertSee('Artwork added. Review your order and complete checkout.')
            ->assertSee('Buy now artwork')
            ->assertSee('100.00')
            ->assertSee('Artist');

        $this->post(route('checkout.submit'), [
            'name' => $buyer->name,
            'email' => $buyer->email,
        ])->assertRedirect(route('transactions'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'seller_id' => $artist->id,
            'total_amount' => 100,
        ]);
    }

    public function test_artist_uploaded_qr_is_saved_privately_and_shown_to_buyer(): void
    {
        Storage::fake('local');
        $artist = $this->makeUser('artist', 'artist@example.test');
        $buyer = $this->makeUser('client', 'buyer@example.test');

        $this->actingAs($artist)
            ->post(route('artist.profile.payment-qr.update'), [
                'payment_qr' => $this->fakePng('artist-qr.png'),
            ])
            ->assertRedirect(route('artist.profile.edit'))
            ->assertSessionHas('status');

        $paymentSettings = ArtistPaymentSetting::where('user_id', $artist->id)->firstOrFail();
        $qrPath = $paymentSettings->payment_qr_path;
        $this->assertNotEmpty($qrPath);
        Storage::disk('local')->assertExists($qrPath);
        $this->get(route('artist.profile.payment-qr'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
        $paymentSettings->update([
            'payment_method' => 'gcash',
            'payment_account_name' => 'Artist',
            'payment_account_identifier' => '+639123456789',
        ]);

        $artwork = $this->makeArtwork($artist, 'QR checkout artwork');
        $this->actingAs($buyer)->withSession(['cart' => [$artwork->id => 1]])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee(route('checkout.artist-payment-qr', $artist->id), false);
        $this->get(route('checkout.artist-payment-qr', $artist->id))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->post(route('checkout.submit'), [
            'name' => $buyer->name,
            'email' => $buyer->email,
        ])->assertRedirect(route('transactions'));
        $order = Order::where('user_id', $buyer->id)->firstOrFail();
        $this->get(route('transactions.show', $order->order_code))
            ->assertOk()
            ->assertSee(route('transactions.payment.qr', $order->order_code), false);
        $this->get(route('transactions.payment.qr', $order->order_code))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_explore_artwork_card_has_direct_product_navigation_without_capturing_heart(): void
    {
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $artist = $this->makeArtist('artist@example.test');
        $artwork = $this->makeArtwork($artist, 'Clickable artwork card');

        $this->actingAs($buyer)
            ->get(route('explore'))
            ->assertOk()
            ->assertSee('data-detail-url="'.route('artwork.show', $artwork->id).'"', false)
            ->assertSee('action="'.route('artwork.favorite', $artwork->id).'"', false);
    }

    public function test_saved_qr_image_is_displayed_during_checkout_and_order_payment(): void
    {
        Storage::fake('local');
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $artist = $this->makeArtist('artist@example.test');
        $qrFile = $this->fakePng('payment-qr.png');
        $qrPath = $qrFile->store("artist-payments/{$artist->id}", 'local');
        $artist->paymentSettings()->updateOrCreate([], ['payment_qr_path' => $qrPath]);
        $artwork = $this->makeArtwork($artist, 'Artwork with QR payment');

        $this->actingAs($buyer)->withSession(['cart' => [$artwork->id => 1]])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee(route('checkout.artist-payment-qr', $artist->id), false);

        $checkoutImage = $this->get(route('checkout.artist-payment-qr', $artist->id));
        $checkoutImage->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('Content-Type', 'image/png');
        Storage::disk('local')->assertExists($qrPath);

        $this->post(route('checkout.submit'), [
            'name' => $buyer->name,
            'email' => $buyer->email,
        ])->assertRedirect(route('transactions'));
        $order = Order::where('user_id', $buyer->id)->firstOrFail();

        $this->get(route('transactions.show', $order->order_code))
            ->assertOk()
            ->assertSee(route('transactions.payment.qr', $order->order_code), false);
        $orderImage = $this->get(route('transactions.payment.qr', $order->order_code));
        $orderImage->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_artist_can_preview_only_their_own_saved_payment_qr(): void
    {
        Storage::fake('local');
        $artist = $this->makeArtist('artist@example.test');
        $otherArtist = $this->makeArtist('other@example.test');
        $qrFile = $this->fakePng('payment-qr.png');
        $qrPath = $qrFile->store("artist-payments/{$artist->id}", 'local');
        $artist->paymentSettings()->updateOrCreate([], ['payment_qr_path' => $qrPath]);

        $this->actingAs($artist)
            ->get(route('artist.profile.edit'))
            ->assertOk()
            ->assertSee(route('artist.profile.payment-qr'), false);

        $this->get(route('artist.profile.payment-qr'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->actingAs($otherArtist)
            ->get(route('artist.profile.payment-qr'))
            ->assertNotFound();
    }

    public function test_database_artwork_favorite_syncs_with_favorites_page_and_can_be_removed(): void
    {
        Storage::fake('public');
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $artist = $this->makeArtist('artist@example.test');
        $artwork = $this->makeArtwork($artist, 'Saved database artwork');
        $artwork->update(['image_path' => 'artworks/saved-database-artwork.png']);
        Storage::disk('public')->put($artwork->image_path, 'artwork image');

        $this->actingAs($buyer)
            ->from(route('explore'))
            ->post(route('artwork.favorite', $artwork))
            ->assertRedirect(route('explore'))
            ->assertSessionHas('favorites', ['db-'.$artwork->id]);

        $this->get(route('favorites'))
            ->assertOk()
            ->assertSee('Saved database artwork')
            ->assertSee(route('artwork.show', $artwork), false)
            ->assertSee(asset('storage/'.$artwork->image_path), false)
            ->assertSee(route('artwork.favorite', $artwork), false);

        $this->from(route('favorites'))
            ->post(route('artwork.favorite', $artwork))
            ->assertRedirect(route('favorites'))
            ->assertSessionHas('favorites', []);

        $this->get(route('favorites'))
            ->assertOk()
            ->assertSee('No favorites yet');
    }

    public function test_missing_qr_file_shows_payment_details_without_a_broken_image(): void
    {
        Storage::fake('local');
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $artist = $this->makeArtist('artist@example.test');
        $missingQrPath = "artist-payments/{$artist->id}/missing.png";
        $artist->paymentSettings()->updateOrCreate([], ['payment_qr_path' => $missingQrPath]);
        $artwork = $this->makeArtwork($artist, 'Artwork without QR file');

        $this->actingAs($buyer)->withSession(['cart' => [$artwork->id => 1]])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('No QR image saved.')
            ->assertDontSee(route('checkout.artist-payment-qr', $artist->id), false);

        $order = $this->makeOrder($buyer, $artist, 'LS-20261108-NOQRFILE');
        $order->update(['payment_qr_path' => $missingQrPath]);
        $this->get(route('transactions.show', $order->order_code))
            ->assertOk()
            ->assertSee('No QR image is available.')
            ->assertDontSee(route('transactions.payment.qr', $order->order_code), false);
    }

    public function test_artist_can_edit_owned_artwork_and_replace_its_image(): void
    {
        Storage::fake('public');
        $artist = $this->makeArtist('artist@example.test');
        $artwork = $this->makeArtwork($artist, 'Original artwork');
        $artwork->image_path = 'artworks/original.png';
        $artwork->save();
        Storage::disk('public')->put($artwork->image_path, 'original-image');

        $this->actingAs($artist)
            ->get(route('artist.inventory.edit', $artwork))
            ->assertOk()
            ->assertSee('Edit artwork')
            ->assertSee('Original artwork')
            ->assertSee(asset('storage/' . $artwork->image_path));

        $oldImage = $artwork->image_path;
        $this->put(route('artist.inventory.update', $artwork), [
            'title' => 'Updated artwork',
            'country' => 'Philippines',
            'description' => 'Updated description',
            'price' => '250.00',
            'artwork_file' => $this->fakePng('updated-artwork.png'),
        ])->assertRedirect(route('artist.inventory'));

        $artwork->refresh();
        $this->assertSame('Updated artwork', $artwork->title);
        $this->assertSame('pending', $artwork->status);
        $this->assertNotSame($oldImage, $artwork->image_path);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($artwork->image_path);
    }

    public function test_client_can_cancel_only_their_own_order_before_payment_is_submitted(): void
    {
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $otherBuyer = $this->makeUser('client', 'other-buyer@example.test');
        $artist = $this->makeArtist('artist@example.test');
        $order = $this->makeOrder($buyer, $artist, 'LS-20261108-CANCEL01');

        $this->actingAs($otherBuyer)
            ->post(route('transactions.cancel', $order->order_code))
            ->assertNotFound();
        $this->assertSame('unpaid', $order->fresh()->payment_status);

        $this->actingAs($buyer)
            ->post(route('transactions.cancel', $order->order_code))
            ->assertRedirect(route('transactions.show', $order->order_code))
            ->assertSessionHas('status', 'Your order was cancelled.');

        $order->refresh();
        $this->assertSame('cancelled', $order->payment_status);
        $this->assertSame('Cancelled', $order->status);
        $this->assertNotNull($order->cancelled_at);
        $this->actingAs($buyer)
            ->get(route('transactions.payment.qr', $order->order_code))
            ->assertNotFound();
        $this->post(route('transactions.payment.submit', $order->order_code), [
            'payment_reference' => 'REF-CANCELLED',
            'payment_proof' => $this->fakePng('receipt.png'),
        ])->assertStatus(409);
    }

    public function test_client_cannot_cancel_order_after_payment_proof_submission_or_payment(): void
    {
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $artist = $this->makeArtist('artist@example.test');
        $order = $this->makeOrder($buyer, $artist, 'LS-20261108-CANCEL02');

        $order->update([
            'payment_status' => 'submitted',
            'status' => 'Payment submitted',
        ]);
        $this->actingAs($buyer)
            ->post(route('transactions.cancel', $order->order_code))
            ->assertStatus(409);
        $this->assertSame('submitted', $order->fresh()->payment_status);

        $order->update([
            'payment_status' => 'paid',
            'status' => 'Processing',
        ]);
        $this->post(route('transactions.cancel', $order->order_code))
            ->assertStatus(409);
        $this->assertSame('paid', $order->fresh()->payment_status);
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
        ArtistPaymentSetting::create([
            'user_id' => $artist->id,
            'payment_method' => $method,
            'payment_account_name' => $name,
            'payment_account_identifier' => '+639123456789',
        ]);

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

    private function makeOrder(User $buyer, User $artist, string $orderCode): Order
    {
        return Order::create([
            'order_code' => $orderCode,
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
