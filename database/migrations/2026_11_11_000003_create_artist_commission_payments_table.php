<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artist_commission_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount_sent', 10, 2);
            $table->decimal('amount_received', 10, 2)->default(0);
            $table->string('payment_method', 40);
            $table->string('payment_reference', 120);
            $table->text('artist_note')->nullable();
            $table->string('proof_path');
            $table->string('status', 20)->default('submitted');
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['artist_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artist_commission_payments');
    }
};
