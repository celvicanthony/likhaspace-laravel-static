<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Default LikhaSpace demo accounts may only be seeded in local or testing environments.');
        }

        foreach (['admin_password', 'artist_password', 'client_password'] as $key) {
            $password = config("demo.credentials.$key");
            if (! is_string($password) || strlen($password) < 8) {
                throw new RuntimeException("Set a local demo credential of at least 8 characters for demo.credentials.$key before seeding.");
            }
        }

        User::query()->where('role', 'admin')->update(['role' => 'client']);

        User::updateOrCreate(
            ['email' => 'admin@likhaspace.ph'],
            ['name' => 'LikhaSpace Admin', 'role' => 'admin', 'artist_id' => null, 'status' => 'active', 'password' => config('demo.credentials.admin_password')]
        );

        foreach (config('demo.artists') as $artistId => $artist) {
            if ($artistId === 'maria-makiling-studio') {
                $legacyAccount = User::where('email', 'maria@likhaspace.test')->first();
                $defaultAccount = User::where('email', $artist['email'])->first();

                if ($legacyAccount && $defaultAccount && $legacyAccount->id !== $defaultAccount->id) {
                    $legacyAccount->update(['role' => 'client', 'artist_id' => null]);
                } elseif ($legacyAccount) {
                    $legacyAccount->update(['email' => $artist['email']]);
                }
            }

            User::updateOrCreate(
                ['email' => $artist['email']],
                [
                    'name' => $artist['name'],
                    'role' => 'artist',
                    'artist_id' => $artistId,
                    'status' => 'active',
                    'password' => $artistId === 'maria-makiling-studio'
                        ? config('demo.credentials.artist_password')
                        : bcrypt(Str::random(40)),
                ]
            );
        }

        foreach ([
            ['name' => 'LikhaSpace Client', 'email' => 'client@likhaspace.ph', 'password' => config('demo.credentials.client_password')],
            ['name' => 'Luna Reyes', 'email' => 'luna@likhaspace.test'],
            ['name' => 'Andre Cruz', 'email' => 'andre@likhaspace.test'],
            ['name' => 'Tala Garcia', 'email' => 'tala@likhaspace.test'],
        ] as $client) {
            User::updateOrCreate(
                ['email' => $client['email']],
                [
                    'name' => $client['name'],
                    'role' => 'client',
                    'artist_id' => null,
                    'status' => 'active',
                    'password' => $client['password'] ?? bcrypt(Str::random(40)),
                ]
            );
        }
    }
}
