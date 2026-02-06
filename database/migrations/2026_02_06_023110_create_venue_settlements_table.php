<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->string('settlement_code')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('booking_count');
            $table->decimal('gross_revenue', 15, 2);
            $table->decimal('platform_fee', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->enum('status', ['PENDING', 'APPROVED', 'TRANSFERRED'])->default('PENDING');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Pivot table for tracking which bookings are in which settlement
        Schema::create('booking_settlement', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_settlement_id')->constrained()->cascadeOnDelete();
            $table->primary(['booking_id', 'venue_settlement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_settlement');
        Schema::dropIfExists('venue_settlements');
    }
};
