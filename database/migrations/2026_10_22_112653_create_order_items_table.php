
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('artwork_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('artwork_title');
            $table->string('artist_name');
            $table->decimal('unit_price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('downloads_used')->default(0);
            $table->timestamps();

            $table->unique(['order_id', 'artwork_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
