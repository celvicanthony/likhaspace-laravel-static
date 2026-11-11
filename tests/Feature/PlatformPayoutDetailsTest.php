<?php

namespace Tests\Feature;

use App\Models\PlatformPayoutSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class PlatformPayoutDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_encrypted_payout_details_for_artists(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $admin = $this->makeUser('admin', 'admin@example.test');
        $artist = $this->makeUser('artist', 'artist@example.test');

        $this->actingAs($admin)
            ->get('/admin/payout-details')
            ->assertOk()
            ->assertSee('Commission payout details');

        $this->post(route('admin.payout-details.update'), [
            'payment_method' => 'instapay',
            'provider_name' => 'Example Bank',
            'account_name' => 'LikhaSpace Admin',
            'account_identifier' => '1234567890',
            'payout_image' => $this->fakePng('admin-payout-image.png'),
        ])->assertRedirect(route('admin.payout-details'))
            ->assertSessionHas('status');

        $setting = PlatformPayoutSetting::findOrFail(1);
        $this->assertSame('instapay', $setting->payment_method);
        $this->assertNotSame('1234567890', $setting->getRawOriginal('account_identifier'));
        Storage::disk('public')->assertExists($setting->payout_qr_path);

        $this->actingAs($artist)
            ->get(route('artist.payout-details'))
            ->assertOk()
            ->assertSee('InstaPay')
            ->assertSee('Example Bank')
            ->assertSee('LikhaSpace Admin')
            ->assertSee('1234567890')
            ->assertSee(asset('storage/'.$setting->payout_qr_path), false);
        $this->get(route('artist.payout-details.image'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->actingAs($admin)
            ->get(route('admin.payout-details'))
            ->assertOk()
            ->assertSee(asset('storage/'.$setting->payout_qr_path), false);
        $this->get(route('admin.payout-details.image'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_admin_can_replace_private_payout_image_with_public_image(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $admin = $this->makeUser('admin', 'admin@example.test');
        $oldPath = $this->fakePng('old-image.png')->store('platform-payouts', 'local');
        PlatformPayoutSetting::create(['payout_qr_path' => $oldPath]);

        $this->actingAs($admin)
            ->get(route('admin.payout-details.image'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->actingAs($admin)
            ->post(route('admin.payout-details.update'), [
                'payout_image' => $this->fakePng('new-image.png'),
            ])
            ->assertRedirect(route('admin.payout-details'));

        $newPath = PlatformPayoutSetting::findOrFail(1)->payout_qr_path;
        $this->assertNotSame($oldPath, $newPath);
        $this->assertStringStartsWith('platform-payout-images/', $newPath);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertMissing($newPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_payout_image_url_includes_application_subdirectory(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $path = $this->fakePng('admin-image.png')->store('platform-payout-images', 'public');
        PlatformPayoutSetting::create(['payout_qr_path' => $path]);

        \Illuminate\Support\Facades\URL::forceRootUrl('http://localhost/likhaspace-laravel-static/public');
        try {
            $imageUrl = asset('storage/'.$path);
            $html = view('admin.payout-details', [
                'settings' => PlatformPayoutSetting::findOrFail(1),
                'payoutImageUrl' => $imageUrl,
            ])->with('errors', new ViewErrorBag)->render();

            $this->assertStringStartsWith('http://localhost/likhaspace-laravel-static/public/storage/', $imageUrl);
            $this->assertStringContainsString('src="'.$imageUrl.'"', $html);
        } finally {
            \Illuminate\Support\Facades\URL::forceRootUrl(null);
        }
    }

    public function test_existing_private_payout_image_is_migrated_when_displayed(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        $admin = $this->makeUser('admin', 'admin@example.test');
        $path = $this->fakePng('legacy-payout.png')->store('platform-payout-images', 'local');
        PlatformPayoutSetting::create(['payout_qr_path' => $path]);

        $this->actingAs($admin)
            ->get(route('admin.payout-details'))
            ->assertOk()
            ->assertSee(asset('storage/'.$path), false);

        Storage::disk('public')->assertExists($path);
        Storage::disk('local')->assertMissing($path);
    }

    public function test_platform_payout_details_are_role_restricted_and_can_be_cleared(): void
    {
        $admin = $this->makeUser('admin', 'admin@example.test');
        $client = $this->makeUser('client', 'client@example.test');

        $this->actingAs($client)
            ->get(route('admin.payout-details'))
            ->assertForbidden();
        $this->get(route('artist.payout-details'))
            ->assertForbidden();

        $this->actingAs($admin)->post(route('admin.payout-details.update'), [
            'payment_method' => '',
            'provider_name' => '',
            'account_name' => '',
            'account_identifier' => '',
        ])->assertRedirect(route('admin.payout-details'));

        $this->assertNull(PlatformPayoutSetting::findOrFail(1)->payment_method);
    }

    private function makeUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
            'artist_id' => $role === 'artist' ? 'test-artist' : null,
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
