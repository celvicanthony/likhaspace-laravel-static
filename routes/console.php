<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:import-sqlite', function () {
    if (config('database.connections.mysql.driver') !== 'mysql') {
        throw new \RuntimeException('The MySQL connection is not configured.');
    }

    $sourcePath = database_path('database.sqlite');
    if (! is_file($sourcePath)) {
        throw new \RuntimeException("SQLite source database not found at {$sourcePath}.");
    }

    config([
        'database.connections.sqlite_import' => [
            'driver' => 'sqlite',
            'database' => $sourcePath,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ]);
    DB::purge('sqlite_import');

    $source = DB::connection('sqlite_import');
    $target = DB::connection('mysql');
    $tables = [
        'users',
        'user_profiles',
        'artist_payment_settings',
        'artworks',
        'favorites',
        'orders',
        'order_items',
        'artwork_reviews',
    ];

    foreach ($tables as $table) {
        if (! Schema::connection('mysql')->hasTable($table)) {
            throw new \RuntimeException("The MySQL table {$table} is missing; run migrations before importing.");
        }

        if ($target->table($table)->exists()) {
            throw new \RuntimeException("The MySQL table {$table} is not empty. Import stopped to avoid overwriting or merging data.");
        }
    }

    $sourceHas = static fn (string $table): bool => Schema::connection('sqlite_import')->hasTable($table);

    if (! $sourceHas('users') || ! $sourceHas('artworks')) {
        throw new \RuntimeException('The SQLite database is missing required users or artworks tables.');
    }

    $sourceColumns = [];
    foreach (['users', 'user_profiles', 'artist_payment_settings', ...array_slice($tables, 3)] as $table) {
        $sourceColumns[$table] = $sourceHas($table)
            ? Schema::connection('sqlite_import')->getColumnListing($table)
            : [];
    }

    $identityFields = [
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'display_name',
        'birthdate',
        'gender',
    ];
    $paymentFields = [
        'payment_method',
        'payment_account_name',
        'payment_account_identifier',
        'payment_qr_path',
    ];

    $target->transaction(function () use (
        $source,
        $target,
        $sourceHas,
        $sourceColumns,
        $identityFields,
        $paymentFields
    ): void {
        foreach (['users', 'artworks', 'favorites', 'orders', 'order_items', 'artwork_reviews'] as $table) {
            if (! $sourceHas($table)) {
                continue;
            }

            $targetColumns = Schema::connection('mysql')->getColumnListing($table);
            foreach ($source->table($table)->orderBy('id')->get()->chunk(500) as $chunk) {
                $rows = $chunk->map(static function (object $row) use ($targetColumns): array {
                    return array_intersect_key((array) $row, array_flip($targetColumns));
                })->all();

                if ($rows !== []) {
                    $target->table($table)->insert($rows);
                }
            }
        }

        if ($sourceHas('user_profiles')) {
            $targetColumns = Schema::connection('mysql')->getColumnListing('user_profiles');
            foreach ($source->table('user_profiles')->orderBy('id')->get()->chunk(500) as $chunk) {
                $rows = $chunk->map(static fn (object $row): array => array_intersect_key(
                    (array) $row,
                    array_flip($targetColumns)
                ))->all();
                if ($rows !== []) {
                    $target->table('user_profiles')->insert($rows);
                }
            }
        } elseif (in_array('first_name', $sourceColumns['users'], true)) {
            foreach ($source->table('users')->orderBy('id')->get() as $user) {
                $profile = array_intersect_key((array) $user, array_flip($identityFields));
                if (array_filter($profile, static fn ($value): bool => $value !== null) === []) {
                    continue;
                }

                $target->table('user_profiles')->insert([
                    'user_id' => $user->id,
                    ...$profile,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }
        }

        if ($sourceHas('artist_payment_settings')) {
            $targetColumns = Schema::connection('mysql')->getColumnListing('artist_payment_settings');
            foreach ($source->table('artist_payment_settings')->orderBy('id')->get()->chunk(500) as $chunk) {
                $rows = $chunk->map(static fn (object $row): array => array_intersect_key(
                    (array) $row,
                    array_flip($targetColumns)
                ))->all();
                if ($rows !== []) {
                    $target->table('artist_payment_settings')->insert($rows);
                }
            }
        } elseif (in_array('payment_method', $sourceColumns['users'], true)) {
            foreach ($source->table('users')->orderBy('id')->get() as $user) {
                $settings = array_intersect_key((array) $user, array_flip($paymentFields));
                if (array_filter($settings, static fn ($value): bool => $value !== null) === []) {
                    continue;
                }

                $target->table('artist_payment_settings')->insert([
                    'user_id' => $user->id,
                    ...$settings,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }
        }
    });

    foreach ($tables as $table) {
        $this->info("Imported {$target->table($table)->count()} rows into {$table}.");
    }
})->purpose('Copy business data from database/database.sqlite into an empty MySQL database');
