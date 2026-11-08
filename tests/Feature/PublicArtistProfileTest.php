<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\ArtistPaymentSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PublicArtistProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_popular_artist_link_opens_public_profile_with_only_approved_artworks(): void
    {
        $artist = User::create([
            'name' => 'Mira Artist',
            'email' => 'mira@example.test',
            'password' => Hash::make('password'),
            'role' => 'artist',
            'artist_id' => 'mira-studio',
            'status' => 'active',
            'profile' => [
                'studio' => 'Mira Studio',
                'country' => 'Philippines',
                'bio' => 'Digital artist and illustrator.',
            ],
        ]);
        Artwork::create([
            'user_id' => $artist->id,
            'title' => 'Approved folklore art',
            'country' => 'Philippines',
            'price' => 1200,
            'status' => 'approved',
        ]);
        Artwork::create([
            'user_id' => $artist->id,
            'title' => 'Unreleased artwork',
            'country' => 'Philippines',
            'price' => 1500,
            'status' => 'pending',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('artists.show', $artist->id))
            ->assertSee('Mira Artist');

        $this->get(route('artists.show', $artist->id))
            ->assertOk()
            ->assertSee('Mira Studio')
            ->assertSee('Digital artist and illustrator.')
            ->assertSee('Approved folklore art')
            ->assertDontSee('Unreleased artwork')
            ->assertDontSee('mira@example.test');
    }

    public function test_public_artist_profile_does_not_expose_payment_details(): void
    {
        $artist = User::create([
            'name' => 'Private Artist',
            'email' => 'private@example.test',
            'password' => Hash::make('password'),
            'role' => 'artist',
            'artist_id' => 'private-studio',
            'status' => 'active',
            'profile' => ['bio' => 'Artist bio.'],
        ]);
        ArtistPaymentSetting::create([
            'user_id' => $artist->id,
            'payment_method' => 'gcash',
            'payment_account_name' => 'Private Recipient',
            'payment_account_identifier' => '09123456789',
        ]);

        $this->get(route('artists.show', $artist->id))
            ->assertOk()
            ->assertDontSee('Private Recipient')
            ->assertDontSee('09123456789');
    }

    public function test_user_names_are_saved_in_consistent_title_case(): void
    {
        $artist = User::create([
            'name' => '  mIRA   aRTIST  ',
            'email' => 'format@example.test',
            'password' => Hash::make('password'),
            'role' => 'artist',
            'status' => 'active',
            'profile' => [],
        ]);

        $this->assertSame('Mira Artist', $artist->name);
    }
}
