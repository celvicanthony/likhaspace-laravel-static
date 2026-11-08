<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('payment_method')->nullable();
            $table->text('payment_account_name')->nullable();
            $table->text('payment_account_identifier')->nullable();
            $table->string('payment_qr_path')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->after('user_id')
                ->constrained('users')->nullOnDelete();
            $table->index(['seller_id', 'created_at']);
            $table->string('payment_proof_path')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('payment_submitted_at')->nullable();
            $table->text('payment_review_note')->nullable();
            $table->timestamp('payment_reviewed_at')->nullable();
            $table->text('payment_recipient_name')->nullable();
            $table->text('payment_recipient_identifier')->nullable();
            $table->string('payment_qr_path')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('delivery_path')->nullable();
            $table->string('delivery_filename')->nullable();
            $table->timestamp('delivered_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['delivery_path', 'delivery_filename', 'delivered_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['seller_id', 'created_at']);
            $table->dropConstrainedForeignId('seller_id');
            $table->dropColumn([
                'payment_proof_path',
                'payment_reference',
                'payment_submitted_at',
                'payment_review_note',
                'payment_reviewed_at',
                'payment_recipient_name',
                'payment_recipient_identifier',
                'payment_qr_path',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_account_name',
                'payment_account_identifier',
                'payment_qr_path',
            ]);
        });
    }
};
