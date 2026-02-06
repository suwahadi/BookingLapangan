<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_court_id')->constrained('venue_courts')->cascadeOnDelete();
            
            $table->unsignedTinyInteger('day_of_week'); // 1=Senin, 7=Minggu (ISO 8601)
            $table->time('start_time'); // 06:00:00
            $table->time('end_time');   // 23:00:00
            $table->unsignedInteger('price_per_hour'); // Harga per jam dalam IDR
            
            $table->timestamps();
            
            $table->index(['venue_court_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_pricings');
    }
};
