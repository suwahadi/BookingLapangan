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
        // Safely add indexes, skip if already exists
        try {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index(['booking_date', 'status'], 'bookings_booking_date_status_index');
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index('booking_code', 'bookings_booking_code_index');
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('payments', function (Blueprint $table) {
                $table->index('status', 'payments_status_index');
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('payments', function (Blueprint $table) {
                $table->index('provider_order_id', 'payments_provider_order_id_index');
            });
        } catch (\Throwable $e) {}

        try {
            Schema::table('booking_slots', function (Blueprint $table) {
                $table->index(['slot_date', 'venue_court_id'], 'booking_slots_slot_date_venue_court_id_index');
            });
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_date', 'status']);
            $table->dropIndex(['booking_code']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['provider_order_id']);
            $table->dropIndex(['paid_at']);
        });

        Schema::table('booking_slots', function (Blueprint $table) {
            $table->dropIndex(['slot_date', 'venue_court_id']);
        });
    }
};
