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
            'display_name' => 'Test Artist',
            'birthdate' => '1990-01-02',
            'gender' => 'prefer_not_to_say',
            'contact' => '+639123456789',
            'email' => 'test@example.com',
            'role' => 'client',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('explore'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'middle_name' => 'Middle',
            'last_name' => 'User',
            'name_extension' => 'Jr.',
            'display_name' => 'Test Artist',
            'birthdate' => '1990-01-02',
            'gender' => 'prefer_not_to_say',
        ]);
    }
}
