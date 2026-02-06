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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            $table->string('booking_code', 20)->unique();
            
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->foreignId('venue_court_id')->constrained('venue_courts')->cascadeOnDelete();
            
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            
            $table->string('status', 20)->index(); // HOLD, CONFIRMED, CANCELLED, EXPIRED
            
            $table->unsignedInteger('total_amount')->default(0);
            $table->unsignedInteger('paid_amount')->default(0);
            $table->unsignedInteger('dp_required_amount')->default(0);
            $table->unsignedInteger('dp_paid_amount')->default(0);
            
            $table->timestamp('expires_at')->nullable(); // untuk HOLD
            
            $table->string('customer_name', 100);
            $table->string('customer_email', 100);
            $table->string('customer_phone', 20);
            
            $table->text('notes')->nullable();
            
            $table->json('slot_snapshot')->nullable(); // untuk audit setelah slot dibebaskan
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['venue_court_id', 'booking_date']);
            $table->index(['status', 'expires_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
