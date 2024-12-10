<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key tới bảng users
            $table->string('full_name');
            $table->string('email');
            $table->string('address');
            $table->enum('payment_method', ['creditCard', 'cod', 'vnpay', 'momo']);
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
