<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['users', 'artworks', 'orders', 'artist_commission_payments'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('public_id')->nullable()->unique();
            });
        }

        Schema::create('public_id_sequences', function (Blueprint $table): void {
            $table->string('prefix')->primary();
            $table->unsignedBigInteger('next_value');
        });

        $nextValues = [];
        DB::table('users')->orderBy('id')->chunkById(500, function ($users) use (&$nextValues): void {
            foreach ($users as $user) {
                $prefix = match ($user->role) {
                    'artist' => 'ARTIST',
                    'client' => 'CLIENT',
                    'admin' => 'ADMIN',
                    default => 'USER',
                };
                $nextValues[$prefix] ??= 11;
                DB::table('users')->where('id', $user->id)->update([
                    'public_id' => $prefix.'-'.str_pad((string) $nextValues[$prefix]++, 5, '0', STR_PAD_LEFT),
                ]);
            }
        });

        foreach ($nextValues as $prefix => $nextValue) {
            DB::table('public_id_sequences')->insert([
                'prefix' => $prefix,
                'next_value' => $nextValue,
            ]);
        }

        foreach ([
            'artworks' => 'ARTWORK',
            'orders' => 'ORDER',
            'artist_commission_payments' => 'COMMISSION',
        ] as $tableName => $prefix) {
            $nextValue = 11;
            DB::table($tableName)->orderBy('id')->chunkById(500, function ($records) use ($tableName, $prefix, &$nextValue): void {
                foreach ($records as $record) {
                    DB::table($tableName)->where('id', $record->id)->update([
                        'public_id' => $prefix.'-'.str_pad((string) $nextValue++, 5, '0', STR_PAD_LEFT),
                    ]);
                }
            });

            DB::table('public_id_sequences')->insert([
                'prefix' => $prefix,
                'next_value' => $nextValue,
            ]);
        }

        foreach (['ARTIST', 'CLIENT', 'ADMIN', 'USER'] as $prefix) {
            DB::table('public_id_sequences')->insertOrIgnore([
                'prefix' => $prefix,
                'next_value' => 11,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('public_id_sequences');

        foreach (['artist_commission_payments', 'orders', 'artworks', 'users'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $table->dropUnique($tableName.'_public_id_unique');
                $table->dropColumn('public_id');
            });
        }
    }
};
