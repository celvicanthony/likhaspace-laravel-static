<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'auth.admin_clearance_pin' => 'test-master-pin',
            'demo.credentials.admin_password' => 'test-admin-password',
            'demo.credentials.artist_password' => 'test-artist-password',
            'demo.credentials.client_password' => 'test-client-password',
        ]);
    }

    public function test_public_auth_pages_have_no_role_preview_or_admin_portal_link(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('login'))
            ->assertSee(route('register'))
            ->assertDontSee('preview.role')
            ->assertDontSee('access-gate');

        $this->get(route('login'))->assertOk()->assertViewIs('auth.login');
        $this->get(route('register'))->assertOk()->assertViewIs('auth.register');
        $this->get(route('admin.login'))->assertOk()->assertViewIs('auth.admin-login');
        $this->actingAs($this->makeUser('client', null, 'portal-visitor@example.test'))
            ->get(route('admin.login'))
            ->assertRedirect(route('explore'));
        $this->assertSame(url('/admin/access-gate'), route('admin.login'));
        $this->assertSame(url('/admin/super-vault-access'), route('admin.vault.gate'));
    }

    public function test_unauthenticated_users_are_redirected_to_the_correct_sign_in_portal(): void
    {
        $this->get(route('profile'))->assertRedirect(route('login'));
        $this->get(route('artist.dashboard'))->assertRedirect(route('login'));
        $this->get(route('cart'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.users'))->assertRedirect(route('admin.login'));
    }

    public function test_seeded_default_accounts_sign_in_through_their_correct_portals(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post(route('login.submit'), [
            'email' => 'client@likhaspace.ph',
            'password' => 'test-client-password',
        ])->assertRedirect(route('explore'));
        $this->assertAuthenticatedAs(User::where('email', 'client@likhaspace.ph')->first());

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->post(route('login.submit'), [
            'email' => 'artist@likhaspace.ph',
            'password' => 'test-artist-password',
        ])->assertRedirect(route('artist.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'artist@likhaspace.ph')->first());

        $this->post(route('logout'));
        $this->post(route('login.submit'), [
            'email' => 'admin@likhaspace.ph',
            'password' => 'test-admin-password',
        ])->assertSessionHasErrors('email');
        $this->post(route('admin.login.submit'), [
            'email' => 'admin@likhaspace.ph',
            'password' => 'test-admin-password',
        ])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'admin@likhaspace.ph')->first());
    }

    public function test_public_registration_creates_only_client_or_artist_accounts(): void
    {
        $this->post(route('register.submit'), [
            'name' => 'New Collector',
            'email' => 'new-client@example.test',
            'role' => 'client',
            'password' => 'collectorpass',
            'password_confirmation' => 'collectorpass',
        ])->assertRedirect(route('explore'));
        $this->assertDatabaseHas('users', ['email' => 'new-client@example.test', 'role' => 'client', 'status' => 'active']);

        $this->post(route('logout'));
        $this->post(route('register.submit'), [
            'name' => 'New Artist',
            'email' => 'new-artist@example.test',
            'role' => 'artist',
            'password' => 'artistpass123',
            'password_confirmation' => 'artistpass123',
        ])->assertRedirect(route('artist.dashboard'));
        $artist = User::where('email', 'new-artist@example.test')->firstOrFail();
        $this->assertSame('artist', $artist->role);
        $this->assertNotEmpty($artist->artist_id);

        $this->post(route('logout'));
        $this->from(route('register'))->post(route('register.submit'), [
            'name' => 'Pretend Admin',
            'email' => 'pretend-admin@example.test',
            'role' => 'admin',
            'password' => 'adminpass123',
            'password_confirmation' => 'adminpass123',
        ])->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'pretend-admin@example.test']);
    }

    public function test_clients_and_artists_are_isolated_by_database_role(): void
    {
        $client = $this->makeUser('client', null, 'client@example.test');
        $artist = $this->makeUser('artist', 'heather-mcrae', 'artist@example.test');
        $artist->update(['name' => 'Heather McRae']);

        $this->actingAs($client)->get(route('explore'))->assertOk()->assertSee('My Favorites');
        $this->get(route('artist.dashboard'))->assertForbidden();
        $this->get(route('admin.dashboard'))->assertForbidden();

        $this->post(route('logout'));
        $this->actingAs($artist)->get(route('artist.dashboard'))
            ->assertOk()
            ->assertSee('Heather McRae')
            ->assertSee('The Kelpie of Loch Ness')
            ->assertDontSee('The Foxfire Promise');
        $this->get(route('artist.inventory'))->assertOk()->assertSee('The Kelpie of Loch Ness');
        $this->get(route('cart'))->assertForbidden();
        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_client_profile_updates_credentials_and_persisted_details(): void
    {
        $client = $this->makeUser('client', null, 'client-profile@example.test', 'oldpassword');
        $this->actingAs($client)->get(route('profile'))->assertOk()->assertViewIs('profile');

        $this->post(route('profile.update'), [
            'name' => 'Updated Collector',
            'email' => 'updated-collector@example.test',
            'bio' => 'A collector profile.',
            'contact' => '+63 900 000 0000',
            'address' => '18 Mabini Street',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'postal' => '1100',
            'notifications' => ['artwork_updates' => '1'],
            'current_password' => 'oldpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ])->assertRedirect(route('profile'))->assertSessionHas('status');

        $client->refresh();
        $this->assertSame('Updated Collector', $client->name);
        $this->assertSame('18 Mabini Street', $client->profile['address']);
        $this->post(route('logout'));
        $this->post(route('login.submit'), [
            'email' => 'updated-collector@example.test',
            'password' => 'newpassword123',
        ])->assertRedirect(route('explore'));
    }

    public function test_artist_profile_and_password_changes_are_saved_to_account(): void
    {
        $artist = $this->makeUser('artist', 'heather-mcrae', 'artist-profile@example.test', 'artist-old-pass');

        $this->actingAs($artist)->post(route('artist.profile.update'), [
            'name' => 'Heather Studio',
            'email' => 'heather-studio@example.test',
            'country' => 'Scotland',
            'studio' => 'Loch and Light',
            'bio' => 'Updated studio bio.',
            'notifications' => ['sales_activity' => '1'],
            'current_password' => 'artist-old-pass',
            'new_password' => 'artist-new-pass',
            'new_password_confirmation' => 'artist-new-pass',
        ])->assertRedirect(route('artist.profile'))->assertSessionHas('status');

        $artist->refresh();
        $this->assertSame('Heather Studio', $artist->name);
        $this->assertSame('Loch and Light', $artist->profile['studio']);
        $this->post(route('logout'));
        $this->post(route('login.submit'), [
            'email' => 'heather-studio@example.test',
            'password' => 'artist-new-pass',
        ])->assertRedirect(route('artist.dashboard'));
    }

    public function test_client_favorites_remain_session_backed_under_real_authentication(): void
    {
        $this->actingAs($this->makeUser('client', null, 'favorites@example.test'))
            ->from(route('product.show', 4))
            ->post(route('favorites.toggle', 4))
            ->assertRedirect(route('product.show', 4))
            ->assertSessionHas('favorites', ['4']);

        $this->get(route('favorites'))->assertOk()->assertSee('The Foxfire Promise');
        $this->post(route('favorites.toggle', 4))->assertSessionHas('favorites', []);
        $this->post(route('favorites.toggle', 999))->assertNotFound();
    }

    public function test_admin_requires_password_login_then_vault_clearance_for_user_management(): void
    {
        $admin = $this->makeUser('admin', null, 'admin@example.test', 'admin-password');
        $client = $this->makeUser('client', null, 'client-admin-test@example.test');

        $this->post(route('admin.login.submit'), [
            'email' => $client->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.approvals'))->assertOk();
        $this->get(route('admin.users'))->assertRedirect(route('admin.vault.gate'));
        $this->post(route('admin.vault.verify'), ['pin' => 'wrong-pin'])->assertSessionHasErrors('pin');
        $this->post(route('admin.vault.verify'), ['pin' => 'test-master-pin'])
            ->assertRedirect(route('admin.users'));
        $this->get(route('admin.users'))->assertOk()->assertSee($client->email);

        $this->post(route('admin.users.action', $client->id), ['action' => 'status'])
            ->assertSessionHas('status');
        $this->assertDatabaseHas('users', ['id' => $client->id, 'status' => 'suspended']);
        $this->post(route('admin.users.action', $admin->id), ['action' => 'delete'])
            ->assertSessionHasErrors('user');

        $this->post(route('logout'));
        $this->post(route('login.submit'), [
            'email' => $client->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }

    public function test_admin_clearance_expires_and_cannot_be_set_by_client(): void
    {
        $client = $this->makeUser('client', null, 'clearance-client@example.test');
        $this->actingAs($client)->post(route('admin.vault.verify'), ['pin' => 'test-master-pin'])->assertForbidden();

        $admin = $this->makeUser('admin', null, 'clearance-admin@example.test');
        $this->actingAs($admin)->withSession(['admin_clearance_at' => now()->subMinutes(31)->timestamp])
            ->get(route('admin.users'))
            ->assertRedirect(route('admin.vault.gate'));
    }

    public function test_artist_submissions_are_still_scoped_and_admin_review_routes_work(): void
    {
        Storage::fake('local');
        $artist = $this->makeUser('artist', 'heather-mcrae', 'submission@example.test');
        $admin = $this->makeUser('admin', null, 'review-admin@example.test');

        $submissionId = null;
        $this->actingAs($artist)->post(route('artist.inventory.submit'), [
            'title' => 'Moonlit Selkie',
            'country' => 'Scotland',
            'price' => '1850',
            'artwork_file' => UploadedFile::fake()->create('selkie.png', 10, 'image/png'),
        ])->assertRedirect(route('artist.inventory'))
            ->assertSessionHas('artist_submissions', function (array $submissions) use (&$submissionId, $artist) {
                $submissionId = array_key_first($submissions);

                return $submissions[$submissionId]['artist_id'] === $artist->artist_id
                    && $submissions[$submissionId]['status'] === 'pending';
            });

        $this->actingAs($admin)->post(route('admin.approvals.action', $submissionId), ['status' => 'approved'])
            ->assertSessionHas('artwork_reviews.'.$submissionId, 'approved');
    }

    private function makeUser(string $role, ?string $artistId, string $email, string $password = 'password'): User
    {
        return User::factory()->create([
            'role' => $role,
            'artist_id' => $artistId,
            'email' => $email,
            'password' => $password,
            'status' => 'active',
        ]);
    }
}
