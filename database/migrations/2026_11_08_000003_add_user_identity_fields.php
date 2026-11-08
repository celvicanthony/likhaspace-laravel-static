<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 80)->nullable();
            $table->string('middle_name', 80)->nullable();
            $table->string('last_name', 80)->nullable();
            $table->string('name_extension', 20)->nullable();
            $table->string('display_name', 120)->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender', 40)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'name_extension',
                'display_name',
                'birthdate',
                'gender',
            ]);
        });
    }
};
