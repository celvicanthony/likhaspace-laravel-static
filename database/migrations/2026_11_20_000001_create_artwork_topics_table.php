<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artwork_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->string('name', 40);
            $table->string('normalized_name', 40);
            $table->timestamps();

            $table->unique(['artwork_id', 'normalized_name']);
            $table->index('normalized_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_topics');
    }
};
