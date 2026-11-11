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
            ->assertViewIs('client.profile.edit')
            ->assertSee('name="first_name"', false)
            ->assertSee('name="middle_name"', false)
            ->assertSee('name="last_name"', false);

        $this->get(route('artist.profile.edit'))->assertForbidden();
        $this->put(route('artist.profile.update'), [])->assertForbidden();

        $artist = $this->makeUser('artist', 'artist@example.test', 'artist-1');
        $this->actingAs($artist)
            ->get(route('artist.profile.edit'))
            ->assertOk()
            ->assertViewIs('artist.profile.edit')
            ->assertSee('name="first_name"', false)
            ->assertSee('name="middle_name"', false)
            ->assertSee('name="last_name"', false);

        $this->get(route('client.profile.edit'))->assertForbidden();
        $this->put(route('client.profile.update'), [])->assertForbidden();
    }

    public function test_client_can_update_own_profile_and_avatar(): void
    {
        Storage::fake('public');
        $client = $this->makeUser('client', 'client@example.test');
        $this->actingAs($client);

        $this->put(route('client.profile.update'), [
            'first_name' => 'Updated',
            'middle_name' => 'Test',
            'last_name' => 'Client',
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
        $this->assertSame('Updated Test Client', $client->name);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $client->id,
            'first_name' => 'Updated',
            'middle_name' => 'Test',
            'last_name' => 'Client',
        ]);
        $this->assertSame('18 Mabini Street', $client->profile['address']);
        $this->assertSame('+63 900 000 0000', $client->profile['contact']);
        Storage::disk('public')->assertExists($client->profile['avatar']);

        $this->get(route('client.profile.edit'))
            ->assertOk()
            ->assertSee(asset('storage/' . $client->profile['avatar']));
    }

    public function test_profile_edit_does_not_require_or_change_password(): void
    {
        $client = $this->makeUser('client', 'client@example.test');
        $originalHash = $client->password;

        $this->actingAs($client)->put(route('client.profile.update'), [
            'first_name' => 'Updated',
            'last_name' => 'Client',
            'email' => $client->email,
            'current_password' => 'incorrect-autofilled-password',
            'new_password' => 'accidental-new-password',
            'new_password_confirmation' => 'accidental-new-password',
        ])->assertRedirect(route('client.profile.edit'))
            ->assertSessionHasNoErrors();

        $this->assertSame($originalHash, $client->fresh()->password);
    }

    public function test_password_change_is_separate_and_requires_the_current_password(): void
    {
        $client = $this->makeUser('client', 'client@example.test');
        $this->actingAs($client);

        $this->put(route('password.update'), [
            'current_password' => 'incorrect-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertSessionHasErrorsIn('updatePassword', 'current_password');

        $this->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect()
            ->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('new-secure-password', $client->fresh()->password));
    }

    public function test_artist_can_update_own_profile_and_cover_image(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $artist = $this->makeUser('artist', 'artist@example.test', 'artist-1');

        $this->actingAs($artist)->post(route('artist.profile.update'), [
            'name' => 'Artist Studio',
            'first_name' => 'Artist',
            'middle_name' => 'M',
            'last_name' => 'Name',
            'email' => $artist->email,
            'contact' => '+63 911 000 0000',
            'country' => 'Philippines',
            'studio' => 'Studio One',
            'bio' => 'Artist statement.',
            'payment_method' => 'gcash',
            'payment_account_name' => 'Artist Name',
            'payment_account_identifier' => '+639111112222',
            'portfolio_links' => ['https://portfolio.example.test', ''],
            'avatar' => $this->fakePng('avatar.png'),
            'cover_image' => $this->fakePng('cover.png'),
        ])->assertRedirect(route('artist.profile.edit'))
            ->assertSessionHas('status', 'Profile updated successfully!');

        $this->post(route('artist.profile.payment-qr.update'), [
            'payment_qr' => $this->fakePng('payment-qr.png'),
        ])->assertRedirect(route('artist.profile.edit'))
            ->assertSessionHas('status', 'Payment QR code saved. Buyers will see it during checkout and on their order payment page.');

        $artist->refresh();
        $this->assertSame('+63 911 000 0000', $artist->profile['contact']);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $artist->id,
            'first_name' => 'Artist',
            'middle_name' => 'M',
            'last_name' => 'Name',
        ]);
        $this->assertSame(['https://portfolio.example.test'], $artist->profile['portfolio_links']);
        $paymentSettings = $artist->paymentSettings;
        $this->assertSame('gcash', $paymentSettings->payment_method);
        $this->assertSame('Artist Name', $paymentSettings->payment_account_name);
        $this->assertSame('+639111112222', $paymentSettings->payment_account_identifier);
        $this->assertNotSame(
            '+639111112222',
            $paymentSettings->getRawOriginal('payment_account_identifier')
        );
        Storage::disk('public')->assertExists($paymentSettings->payment_qr_path);
        $this->assertStringNotContainsString('+639111112222', $artist->toJson());
        Storage::disk('public')->assertExists($artist->profile['avatar']);
        Storage::disk('public')->assertExists($artist->profile['cover_image']);

        $this->get(route('artist.profile.edit'))
            ->assertOk()
            ->assertSee(asset('storage/' . $artist->profile['avatar']))
            ->assertSee(asset('storage/' . $artist->profile['cover_image']))
            ->assertSee(asset('storage/'.$paymentSettings->payment_qr_path), false);
        $this->get(route('artist.profile.payment-qr'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
        $this->get(route('artists.show', $artist->id))
            ->assertOk()
            ->assertSee('/storage/' . $artist->profile['avatar'])
            ->assertSee('/storage/' . $artist->profile['cover_image']);
    }

    public function test_profile_email_must_be_unique_except_for_the_current_user(): void
    {
        $client = $this->makeUser('client', 'client@example.test');
        $this->makeUser('artist', 'taken@example.test', 'artist-1');

        $this->actingAs($client)->put(route('client.profile.update'), [
            'first_name' => 'Client',
            'last_name' => 'Collector',
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
