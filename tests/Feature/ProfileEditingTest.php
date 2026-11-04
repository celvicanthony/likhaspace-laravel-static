<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_edit_routes_are_limited_to_the_matching_role(): void
    {
        $client = $this->makeUser('client', 'client@example.test');
        $this->actingAs($client)
            ->get(route('client.profile.edit'))
            ->assertOk()
            ->assertViewIs('client.profile.edit');

        $this->get(route('artist.profile.edit'))->assertForbidden();
        $this->put(route('artist.profile.update'), [])->assertForbidden();

        $artist = $this->makeUser('artist', 'artist@example.test', 'artist-1');
        $this->actingAs($artist)
            ->get(route('artist.profile.edit'))
            ->assertOk()
            ->assertViewIs('artist.profile.edit');

        $this->get(route('client.profile.edit'))->assertForbidden();
        $this->put(route('client.profile.update'), [])->assertForbidden();
    }

    public function test_client_can_update_own_profile_and_avatar(): void
    {
        Storage::fake('public');
        $client = $this->makeUser('client', 'client@example.test');
        $this->actingAs($client);

        $this->put(route('client.profile.update'), [
            'name' => 'Updated Client',
            'email' => $client->email,
            'contact' => '+63 900 000 0000',
            'address' => '18 Mabini Street',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'postal' => '1100',
            'avatar' => $this->fakePng('avatar.png'),
        ])->assertRedirect(route('client.profile.edit'))
            ->assertSessionHas('status', 'Profile updated successfully!');

        $client->refresh();
        $this->assertSame('Updated Client', $client->name);
        $this->assertSame('18 Mabini Street', $client->profile['address']);
        $this->assertSame('+63 900 000 0000', $client->profile['contact']);
        Storage::disk('public')->assertExists($client->profile['avatar']);
    }

    public function test_artist_can_update_own_profile_and_cover_image(): void
    {
        Storage::fake('public');
        $artist = $this->makeUser('artist', 'artist@example.test', 'artist-1');

        $this->actingAs($artist)->post(route('artist.profile.update'), [
            'name' => 'Artist Studio',
            'email' => $artist->email,
            'contact' => '+63 911 000 0000',
            'country' => 'Philippines',
            'studio' => 'Studio One',
            'bio' => 'Artist statement.',
            'portfolio_links' => ['https://portfolio.example.test', ''],
            'avatar' => $this->fakePng('avatar.png'),
            'cover_image' => $this->fakePng('cover.png'),
        ])->assertRedirect(route('artist.profile.edit'))
            ->assertSessionHas('status', 'Profile updated successfully!');

        $artist->refresh();
        $this->assertSame('+63 911 000 0000', $artist->profile['contact']);
        $this->assertSame(['https://portfolio.example.test'], $artist->profile['portfolio_links']);
        Storage::disk('public')->assertExists($artist->profile['avatar']);
        Storage::disk('public')->assertExists($artist->profile['cover_image']);
    }

    public function test_profile_email_must_be_unique_except_for_the_current_user(): void
    {
        $client = $this->makeUser('client', 'client@example.test');
        $this->makeUser('artist', 'taken@example.test', 'artist-1');

        $this->actingAs($client)->put(route('client.profile.update'), [
            'name' => 'Client',
            'email' => 'taken@example.test',
        ])->assertSessionHasErrors('email');
    }

    private function makeUser(string $role, string $email, ?string $artistId = null): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'artist_id' => $artistId,
            'status' => 'active',
            'profile' => [],
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
