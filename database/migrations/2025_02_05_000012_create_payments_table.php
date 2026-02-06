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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            
            $table->string('type', 20); // FULL, DP, REMAINING
            $table->string('status', 20)->index(); // PENDING, SETTLEMENT, FAILED, EXPIRED, CANCELLED
            
            $table->unsignedInteger('amount');
            
            $table->string('provider', 50)->default('MIDTRANS');
            $table->string('provider_order_id', 100)->unique();
            $table->string('provider_transaction_id', 100)->nullable();
            
            $table->string('payment_method', 50)->nullable(); // bank_transfer, gopay, dll
            $table->string('method_fingerprint', 80)->nullable(); // untuk idempotency
            
            $table->json('payload_request')->nullable();
            $table->json('payload_response')->nullable();
            
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['booking_id', 'status', 'type']);
            $table->index(['booking_id', 'type', 'method_fingerprint']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
