<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_artist_can_search_only_their_own_orders(): void
    {
        $artist = $this->makeUser('artist', 'artist@example.test');
        $otherArtist = $this->makeUser('artist', 'other-artist@example.test');
        $this->makeOrder($artist, 'LS-ARTIST-001', 'Mina Buyer');
        $this->makeOrder($otherArtist, 'LS-OTHER-001', 'Mina Buyer');

        $this->actingAs($artist)
            ->get(route('artist.orders', ['search' => 'LS-ARTIST']))
            ->assertOk()
            ->assertSee('LS-ARTIST-001')
            ->assertDontSee('LS-OTHER-001')
            ->assertSee('name="search"', false);
    }

    public function test_admin_can_search_sales_and_preserve_search_across_pages(): void
    {
        $admin = $this->makeUser('admin', 'admin@example.test');
        $artist = $this->makeUser('artist', 'artist@example.test');
        for ($index = 1; $index <= 16; $index++) {
            $this->makeOrder(
                $artist,
                sprintf('LS-SALE-%03d', $index),
                $index === 16 ? 'Target Buyer' : "Buyer {$index}"
            );
        }

        $this->actingAs($admin)
            ->get(route('admin.transactions', ['search' => 'Target Buyer']))
            ->assertOk()
            ->assertSee('LS-SALE-016')
            ->assertDontSee('LS-SALE-001')
            ->assertSee('name="search"', false);

        $this->actingAs($admin)
            ->get(route('admin.transactions', ['search' => 'LS-SALE-']))
            ->assertOk()
            ->assertSee('page=2', false)
            ->assertSee('search=LS-SALE-', false);

        $this->actingAs($admin)
            ->get(route('admin.transactions', ['search' => 'LS-SALE-', 'page' => 2]))
            ->assertOk()
            ->assertSee('LS-SALE-016');
    }

    public function test_customer_can_search_own_order_history(): void
    {
        $buyer = $this->makeUser('client', 'buyer@example.test');
        $artist = $this->makeUser('artist', 'artist@example.test');
        $this->makeOrder($artist, 'LS-CUSTOMER-001', 'Customer', $buyer);
        $this->makeOrder($artist, 'LS-CUSTOMER-002', 'Customer');

        $this->actingAs($buyer)
            ->get(route('transactions', ['search' => 'LS-CUSTOMER-001']))
            ->assertOk()
            ->assertSee('LS-CUSTOMER-001')
            ->assertDontSee('LS-CUSTOMER-002');
    }

    private function makeUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'artist_id' => $role === 'artist' ? str_replace('@example.test', '', $email) : null,
            'status' => 'active',
            'profile' => [],
        ]);
    }

    private function makeOrder(User $artist, string $orderCode, string $buyerName, ?User $buyer = null): Order
    {
        $buyer ??= $this->makeUser('client', strtolower(str_replace(' ', '-', $buyerName))."-$orderCode@example.test");

        return Order::create([
            'order_code' => $orderCode,
            'user_id' => $buyer->id,
            'seller_id' => $artist->id,
            'buyer_name' => $buyerName,
            'buyer_email' => $buyer->email,
            'shipping_address' => 'Digital delivery',
            'payment_method' => 'GCASH',
            'payment_status' => 'unpaid',
            'status' => 'Awaiting payment',
            'total_amount' => 100,
        ]);
    }
}
