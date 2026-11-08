<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name', 80)->nullable();
            $table->string('middle_name', 80)->nullable();
            $table->string('last_name', 80)->nullable();
            $table->string('name_extension', 20)->nullable();
            $table->string('display_name', 120)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('artist_payment_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('payment_method')->nullable();
            $table->text('payment_account_name')->nullable();
            $table->text('payment_account_identifier')->nullable();
            $table->string('payment_qr_path')->nullable();
            $table->timestamps();
        });

        foreach (DB::table('users')->orderBy('id')->get() as $user) {
            $identity = [
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'name_extension' => $user->name_extension,
                'display_name' => $user->display_name,
                'birthdate' => $user->birthdate,
                'gender' => $user->gender,
            ];

            if (array_filter($identity, static fn ($value): bool => $value !== null) !== []) {
                DB::table('user_profiles')->insert([
                    'user_id' => $user->id,
                    ...$identity,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }

            $paymentSettings = [
                'payment_method' => $user->payment_method,
                'payment_account_name' => $user->payment_account_name,
                'payment_account_identifier' => $user->payment_account_identifier,
                'payment_qr_path' => $user->payment_qr_path,
            ];

            if (array_filter($paymentSettings, static fn ($value): bool => $value !== null) !== []) {
                DB::table('artist_payment_settings')->insert([
                    'user_id' => $user->id,
                    ...$paymentSettings,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'name_extension',
                'display_name',
                'birthdate',
                'gender',
                'payment_method',
                'payment_account_name',
                'payment_account_identifier',
                'payment_qr_path',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 80)->nullable();
            $table->string('middle_name', 80)->nullable();
            $table->string('last_name', 80)->nullable();
            $table->string('name_extension', 20)->nullable();
            $table->string('display_name', 120)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender', 40)->nullable();
            $table->text('payment_method')->nullable();
            $table->text('payment_account_name')->nullable();
            $table->text('payment_account_identifier')->nullable();
            $table->string('payment_qr_path')->nullable();
        });

        foreach (DB::table('user_profiles')->get() as $profile) {
            DB::table('users')->where('id', $profile->user_id)->update([
                'first_name' => $profile->first_name,
                'middle_name' => $profile->middle_name,
                'last_name' => $profile->last_name,
                'name_extension' => $profile->name_extension,
                'display_name' => $profile->display_name,
                'birthdate' => $profile->birthdate,
                'gender' => $profile->gender,
            ]);
        }

        foreach (DB::table('artist_payment_settings')->get() as $settings) {
            DB::table('users')->where('id', $settings->user_id)->update([
                'payment_method' => $settings->payment_method,
                'payment_account_name' => $settings->payment_account_name,
                'payment_account_identifier' => $settings->payment_account_identifier,
                'payment_qr_path' => $settings->payment_qr_path,
            ]);
        }

        Schema::dropIfExists('artist_payment_settings');
        Schema::dropIfExists('user_profiles');
    }
};
