<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->string('genre', 60)->nullable();
            $table->string('license_type', 30)->nullable();
            $table->string('file_format', 10)->nullable();
            $table->string('art_file_path')->nullable();
            $table->string('preview_image')->nullable();
            $table->string('stock_type', 30)->default('multi_copy');
            $table->unsignedInteger('stock_quantity')->default(1);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('buyer_contact', 40)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('buyer_contact');
        });

        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn([
                'genre',
                'license_type',
                'file_format',
                'art_file_path',
                'preview_image',
                'stock_type',
                'stock_quantity',
            ]);
        });
    }
};