<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master amenities table
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable(); // SVG icon name or class
            $table->string('category')->nullable(); // parking, facility, etc
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table venue-amenity
        Schema::create('amenity_venue', function (Blueprint $table) {
            $table->foreignId('amenity_id')->constrained('amenities')->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->primary(['amenity_id', 'venue_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenity_venue');
        Schema::dropIfExists('amenities');
    }
};
