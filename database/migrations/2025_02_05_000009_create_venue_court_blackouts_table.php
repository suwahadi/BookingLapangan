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
        Schema::create('venue_court_blackouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_court_id')->constrained('venue_courts')->cascadeOnDelete();
            
            $table->date('date');
            $table->string('reason', 255)->nullable();
            
            $table->timestamps();
            
            $table->unique(['venue_court_id', 'date']);
            $table->index(['venue_court_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_court_blackouts');
    }
};
