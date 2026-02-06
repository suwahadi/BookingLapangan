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
        Schema::create('venue_operating_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            
            $table->unsignedTinyInteger('day_of_week'); // 0=Minggu, 1=Senin, ..., 6=Sabtu
            $table->time('open_time');
            $table->time('close_time');
            $table->boolean('is_closed')->default(false); // true jika venue tutup di hari ini
            
            $table->timestamps();
            
            $table->unique(['venue_id', 'day_of_week']);
            $table->index(['venue_id', 'is_closed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_operating_hours');
    }
};
