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
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('venue_court_id')->constrained('venue_courts')->cascadeOnDelete();
            
            $table->date('slot_date');
            $table->time('slot_start_time');
            $table->time('slot_end_time');
            
            $table->timestamps();
            
            // UNIQUE constraint untuk anti double booking
            $table->unique(['venue_court_id', 'slot_date', 'slot_start_time', 'slot_end_time'], 'unique_slot_lock');
            
            $table->index(['booking_id']);
            $table->index(['venue_court_id', 'slot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_slots');
    }
};
