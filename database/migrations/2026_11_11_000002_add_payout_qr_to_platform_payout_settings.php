<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_payout_settings', function (Blueprint $table) {
            $table->string('payout_qr_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('platform_payout_settings', function (Blueprint $table) {
            $table->dropColumn('payout_qr_path');
        });
    }
};
