<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('discount_type', 20);
            $table->integer('discount_value');
            $table->integer('max_discount_amount')->nullable();
            $table->integer('min_order_amount')->default(0);
            $table->string('scope', 20)->default('all');
            $table->unsignedBigInteger('venue_id')->nullable();
            $table->unsignedBigInteger('venue_court_id')->nullable();
            $table->integer('max_usage_total')->nullable();
            $table->integer('max_usage_per_user')->default(1);
            $table->integer('usage_count_total')->default(0);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('venue_id')->references('id')->on('venues')->nullOnDelete();
            $table->foreign('venue_court_id')->references('id')->on('venue_courts')->nullOnDelete();
            $table->index(['is_active', 'valid_from', 'valid_until'], 'idx_vouchers_active_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
