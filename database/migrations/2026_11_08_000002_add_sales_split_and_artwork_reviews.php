<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('platform_commission', 10, 2)->default(0);
            $table->decimal('artist_earnings', 10, 2)->default(0);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->timestamp('downloaded_at')->nullable();
        });

        DB::table('orders')->where('payment_status', 'paid')->update([
            'platform_commission' => DB::raw('ROUND(total_amount * 0.10, 2)'),
            'artist_earnings' => DB::raw('total_amount - ROUND(total_amount * 0.10, 2)'),
        ]);

        Schema::create('artwork_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->unique()->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('artwork_id')->constrained('artworks')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index(['artwork_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_reviews');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('downloaded_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['platform_commission', 'artist_earnings']);
        });
    }
};
