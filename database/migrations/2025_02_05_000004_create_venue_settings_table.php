<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->unique()->constrained('venues')->cascadeOnDelete();
            
            $table->unsignedSmallInteger('slot_minutes')->default(60); // 15, 30, 60, dll
            $table->unsignedSmallInteger('min_duration_minutes')->default(60);
            $table->unsignedSmallInteger('max_duration_minutes')->default(240);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_settings');
    }
};
