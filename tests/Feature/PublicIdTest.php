<?php

namespace Tests\Feature;

use App\Models\ArtistCommissionPayment;
use App\Models\Artwork;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicIdTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_records_receive_sequential_public_ids_without_changing_primary_keys(): void
    {
        $artist = $this->makeUser('artist', 'artist@example.test');
        $secondArtist = $this->makeUser('artist', 'artist-two@example.test');
        $client = $this->makeUser('client', 'client@example.test');

        $this->assertSame('ARTIST-00011', $artist->public_id);
        $this->assertSame('ARTIST-00012', $secondArtist->public_id);
        $this->assertSame('CLIENT-00011', $client->public_id);

        $artwork = Artwork::create([
            'user_id' => $artist->id,
            'title' => 'Test artwork',
            'country' => 'Philippines',
            'price' => 100,
            'status' => 'approved',
        ]);
        $this->assertSame('ARTWORK-00011', $artwork->public_id);

        $order = Order::create([
            'order_code' => 'LS-PUBLIC-ID-001',
            'user_id' => $client->id,
            'seller_id' => $artist->id,
            'buyer_name' => $client->name,
            'buyer_email' => $client->email,
            'shipping_address' => 'Digital delivery',
            'payment_method' => 'GCASH',
            'total_amount' => 100,
        ]);
        $this->assertSame('ORDER-00011', $order->public_id);

        $payment = ArtistCommissionPayment::create([
            'artist_id' => $artist->id,
            'amount_sent' => 10,
            'payment_method' => 'gcash',
            'payment_reference' => 'TEST-001',
            'proof_path' => "commission-settlements/{$artist->id}/proof.png",
        ]);
        $this->assertSame('COMMISSION-00011', $payment->public_id);

        $this->assertIsInt($artist->id);
        $this->assertIsInt($artwork->id);
        $this->assertSame($artist->id, $artwork->artist->id);
        $this->assertSame($client->id, $order->customer->id);
        $this->assertSame($artist->id, $payment->artist->id);
        $this->assertSame(13, DB::table('public_id_sequences')->where('prefix', 'ARTIST')->value('next_value'));
    }

    private function makeUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'artist_id' => $role === 'artist' ? str_replace('@example.test', '', $email) : null,
            'status' => 'active',
            'profile' => [],
        ]);
    }
}
