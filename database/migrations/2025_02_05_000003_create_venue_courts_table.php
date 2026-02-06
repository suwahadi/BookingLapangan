<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_courts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            
            $table->string('name');
            $table->string('sport', 50)->nullable(); // Futsal, Badminton, Tennis, dll
            $table->string('floor_type', 50)->nullable(); // Vinyl, Sintetis, Parquette, dll
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['venue_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_courts');
    }
};
