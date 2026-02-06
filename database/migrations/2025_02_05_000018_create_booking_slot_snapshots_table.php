<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_slot_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

            $table->string('event', 30); // CREATED, EXPIRED, CANCELLED, RESCHEDULED_FROM, RESCHEDULED_TO, CONFIRMED
            $table->json('payload');     // snapshot: date, start, end, slots[], venue_court_id
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['booking_id', 'event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_slot_snapshots');
    }
};
