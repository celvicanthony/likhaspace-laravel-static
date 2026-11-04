
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_code')->unique();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->string('shipping_address');

            $table->string('payment_method');
            $table->string('payment_status')->default('unpaid');
            $table->string('status')->default('pending');

            $table->decimal('total_amount', 10, 2);

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
