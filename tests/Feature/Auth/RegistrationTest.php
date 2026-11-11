<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'middle_name' => 'Middle',
            'last_name' => 'User',
            'name_extension' => 'Jr.',
            'email' => 'test@example.com',
            'role' => 'client',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('explore'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test Middle User Jr.',
        ]);
        $this->assertDatabaseHas('user_profiles', [
            'first_name' => 'Test',
            'middle_name' => 'Middle',
            'last_name' => 'User',
            'name_extension' => 'Jr.',
        ]);
    }

    public function test_middle_name_and_extension_are_optional_during_registration(): void
    {
        $this->post('/register', [
            'first_name' => 'Ana',
            'last_name' => 'Santos',
            'email' => 'ana@example.com',
            'role' => 'artist',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('artist.dashboard'));

        $this->assertDatabaseHas('user_profiles', [
            'first_name' => 'Ana',
            'middle_name' => null,
            'last_name' => 'Santos',
            'name_extension' => null,
        ]);
    }
}
