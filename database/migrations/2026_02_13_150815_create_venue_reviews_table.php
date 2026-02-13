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
        Schema::create('venue_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('venue_court_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['venue_id', 'is_approved']);
            $table->index('user_id');
            $table->unique(['user_id', 'booking_id']);
        });

        Schema::table('venues', function (Blueprint $table) {
            // Adding aggregate columns to venues
            $table->decimal('rating_avg', 3, 2)->default(0.00)->after('is_active');
            $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['rating_avg', 'rating_count']);
        });

        Schema::dropIfExists('venue_reviews');
    }
};
