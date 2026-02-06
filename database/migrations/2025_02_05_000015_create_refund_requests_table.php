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
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            
            $table->unsignedInteger('amount');
            
            $table->string('status', 20)->default('PENDING')->index(); // PENDING, PROCESSED, REJECTED
            
            $table->text('reason')->nullable();
            $table->text('notes')->nullable(); // catatan admin
            
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            
            $table->string('refund_method', 50)->nullable(); // BANK_TRANSFER, CASH, dll
            $table->string('refund_reference', 100)->nullable(); // nomor referensi transfer
            
            $table->timestamps();
            
            $table->index(['booking_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};
