<?php

namespace Tests\Feature;

use App\Models\ArtistCommissionPayment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CommissionSettlementTest extends TestCase
{
    use RefreshDatabase;

    public function test_artist_submits_private_commission_receipt_for_admin_review(): void
    {
        Storage::fake('local');
        [$artist, $admin] = $this->makeUsers();
        $this->makePaidOrder($artist, 'LS-SETTLE-001', 100);

        $this->actingAs($artist)
            ->get(route('artist.payout-details'))
            ->assertOk()
            ->assertSee('Commission due')
            ->assertSee('10.00')
            ->assertSee('Submit commission payment');

        $this->post(route('artist.commission-settlements.submit'), [
            'amount_sent' => '6.50',
            'payment_method' => 'instapay',
            'payment_reference' => 'TRANSFER-001',
            'artist_note' => 'June commission',
            'payment_proof' => $this->fakePng('receipt.png'),
        ])->assertRedirect(route('artist.payout-details'))
            ->assertSessionHas('status');

        $submission = ArtistCommissionPayment::firstOrFail();
        $this->assertSame('submitted', $submission->status);
        $this->assertSame('0.00', $submission->amount_received);
        Storage::disk('local')->assertExists($submission->proof_path);

        $proofContents = Storage::disk('local')->get($submission->proof_path);
        $this->get(route('artist.commission-settlements.proof', $submission))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Cache-Control', 'no-store, private')
            ->assertContent($proofContents);
        $this->actingAs($admin)
            ->get(route('admin.commission-settlements'))
            ->assertOk()
            ->assertSee('TRANSFER-001')
            ->assertSee($artist->name)
            ->assertSee(request()->getBaseUrl().route('admin.commission-settlements.proof', $submission, false), false);
        $this->get(route('admin.commission-settlements.proof', $submission))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertContent($proofContents);
    }

    public function test_admin_confirms_partial_received_amount_and_artist_balance_updates(): void
    {
        Storage::fake('local');
        [$artist, $admin] = $this->makeUsers();
        $this->makePaidOrder($artist, 'LS-SETTLE-002', 100);
        $submission = $this->submitCommissionPayment($artist, '8.00');

        $this->actingAs($admin)
            ->get(route('admin.commission-settlements'))
            ->assertOk()
            ->assertSee('Commission generated')
            ->assertSee('Pending review')
            ->assertSee('Outstanding commission')
            ->assertSee('₱10.00')
            ->assertSee('₱8.00');

        $this->actingAs($admin)
            ->post(route('admin.commission-settlements.review', $submission), [
                'decision' => 'approve',
                'amount_received' => '3.25',
            ])->assertRedirect(route('admin.commission-settlements'));

        $submission->refresh();
        $this->assertSame('approved', $submission->status);
        $this->assertSame('3.25', $submission->amount_received);
        $this->assertSame($admin->id, $submission->reviewed_by);

        $this->actingAs($artist)
            ->get(route('artist.payout-details'))
            ->assertOk()
            ->assertSee('Confirmed received')
            ->assertSee('3.25')
            ->assertSee('Outstanding balance')
            ->assertSee('6.75');
        $this->get(route('artist.dashboard'))
            ->assertOk()
            ->assertSee('Outstanding commission')
            ->assertSee('₱6.75')
            ->assertSee('₱3.25');
        $this->actingAs($admin)
            ->get(route('admin.commission-settlements'))
            ->assertOk()
            ->assertSee('Outstanding commission')
            ->assertSee('₱6.75')
            ->assertSee('₱3.25');
    }

    public function test_artist_cannot_submit_more_than_outstanding_commission(): void
    {
        Storage::fake('local');
        [$artist] = $this->makeUsers();
        $this->makePaidOrder($artist, 'LS-SETTLE-003', 100);

        $this->actingAs($artist)
            ->from(route('artist.payout-details'))
            ->post(route('artist.commission-settlements.submit'), [
                'amount_sent' => '10.01',
                'payment_method' => 'ewallet',
                'payment_reference' => 'OVER-BALANCE',
                'payment_proof' => $this->fakePng('receipt.png'),
            ])
            ->assertSessionHasErrors('amount_sent');
        $this->assertDatabaseCount('artist_commission_payments', 0);
    }

    public function test_admin_can_reject_submission_and_only_admin_can_review_settlements(): void
    {
        Storage::fake('local');
        [$artist, $admin, $otherArtist] = $this->makeUsers();
        $this->makePaidOrder($artist, 'LS-SETTLE-004', 100);
        $submission = $this->submitCommissionPayment($artist, '4.00');

        $this->actingAs($otherArtist)
            ->get(route('admin.commission-settlements'))
            ->assertForbidden();
        $this->post(route('admin.commission-settlements.review', $submission), [
            'decision' => 'reject',
            'admin_note' => 'Please provide a readable receipt.',
        ])->assertForbidden();

        $this->actingAs($admin)
            ->post(route('admin.commission-settlements.review', $submission), [
                'decision' => 'reject',
                'admin_note' => 'Please provide a readable receipt.',
            ])->assertRedirect(route('admin.commission-settlements'));

        $this->assertDatabaseHas('artist_commission_payments', [
            'id' => $submission->id,
            'status' => 'rejected',
            'amount_received' => 0,
            'admin_note' => 'Please provide a readable receipt.',
        ]);
        $this->actingAs($artist)
            ->get(route('artist.payout-details'))
            ->assertOk()
            ->assertSee('10.00');
    }

    private function makeUsers(): array
    {
        return [
            $this->makeUser('artist', 'artist@example.test'),
            $this->makeUser('admin', 'admin@example.test'),
            $this->makeUser('artist', 'other@example.test'),
        ];
    }

    private function makeUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role).' '.strstr($email, '@', true),
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'artist_id' => $role === 'artist' ? strstr($email, '@', true) : null,
            'status' => 'active',
            'profile' => [],
        ]);
    }

    private function makePaidOrder(User $artist, string $orderCode, float $total): Order
    {
        return Order::create([
            'order_code' => $orderCode,
            'user_id' => $this->makeUser('client', $orderCode.'@example.test')->id,
            'seller_id' => $artist->id,
            'buyer_name' => 'Client',
            'buyer_email' => $orderCode.'@example.test',
            'shipping_address' => 'Digital delivery',
            'payment_method' => 'GCASH',
            'payment_status' => 'paid',
            'status' => 'Processing',
            'total_amount' => $total,
            'platform_commission' => round($total * 0.1, 2),
            'artist_earnings' => round($total * 0.9, 2),
        ]);
    }

    private function submitCommissionPayment(User $artist, string $amount): ArtistCommissionPayment
    {
        $this->actingAs($artist)
            ->post(route('artist.commission-settlements.submit'), [
                'amount_sent' => $amount,
                'payment_method' => 'instapay',
                'payment_reference' => 'TEST-'.$amount,
                'payment_proof' => $this->fakePng('receipt.png'),
            ])->assertRedirect(route('artist.payout-details'));

        return ArtistCommissionPayment::latest('id')->firstOrFail();
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
