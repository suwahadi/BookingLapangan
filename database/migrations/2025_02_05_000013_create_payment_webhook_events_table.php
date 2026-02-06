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
        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id();
            
            $table->string('provider', 50)->default('MIDTRANS');
            $table->string('event_type', 50)->nullable();
            $table->string('order_id', 100)->index();
            $table->string('transaction_id', 100)->nullable();
            
            $table->json('payload');
            
            $table->string('status_code', 10)->nullable();
            $table->string('transaction_status', 50)->nullable();
            $table->string('fraud_status', 50)->nullable();
            
            $table->boolean('processed')->default(false)->index();
            $table->text('processing_error')->nullable();
            
            $table->timestamps();
            
            $table->index(['order_id', 'created_at']);
            $table->index(['processed', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_events');
    }
};
