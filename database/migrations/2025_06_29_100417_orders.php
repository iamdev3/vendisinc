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
            $table->string('order_number')->unique();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->foreignId('retailer_id')->constrained('retailors')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // User who created the order

            // Customer information as JSON
            $table->json('customer_information')->nullable();

            // Order details
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            // Order status and tracking
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
                'refunded',
                'on_hold'
            ])->default('pending');

            $table->enum('payment_status', [
                'pending',
                'paid',
                'refunded'
            ])->default('pending');

            $table->enum('payment_method', [
                'cash',
                'card',
                'upi',
                'bank',
                'cheque',
                'other'
            ])->nullable();

            // Dates
            $table->timestamp('order_date')->useCurrent();
            $table->timestamp('expected_delivery_date')->nullable();
            $table->timestamp('delivered_at')->nullable();

            // Additional info
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable(); // For internal team use only

            $table->timestamps();

            // Indexes for better performance
            $table->index(['brand_id', 'status']);
            $table->index(['retailer_id', 'status']);
            $table->index(['user_id', 'order_date']);
            $table->index('order_date');
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
