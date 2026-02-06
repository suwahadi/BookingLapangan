<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->unique()->constrained('venues')->cascadeOnDelete();
            
            // DP Policy
            $table->boolean('allow_dp')->default(false);
            $table->unsignedTinyInteger('dp_min_percent')->default(0); // 0-100
            
            // Reschedule Policy
            $table->boolean('reschedule_allowed')->default(false);
            $table->unsignedSmallInteger('reschedule_deadline_hours')->default(0); // H-24, H-48, dll
            
            // Refund Policy
            $table->boolean('refund_allowed')->default(false);
            $table->json('refund_rules')->nullable(); // {"h_minus_72": 100, "h_minus_24": 50, "below_24": 0}
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_policies');
    }
};
