<?php

namespace Database\Seeders;

use App\Models\Voucher;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $firstVenue = Venue::first();

        Voucher::updateOrCreate(['code' => 'DISKON10'], [
            'name' => 'Diskon 10%',
            'description' => 'Potongan 10% untuk semua venue, maks Rp 50.000',
            'discount_type' => 'PERCENTAGE',
            'discount_value' => 10,
            'max_discount_amount' => 50000,
            'min_order_amount' => 100000,
            'scope' => 'all',
            'max_usage_total' => 100,
            'max_usage_per_user' => 2,
            'usage_count_total' => 0,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Voucher::updateOrCreate(['code' => 'HEMAT25K'], [
            'name' => 'Hemat 25 Ribu',
            'description' => 'Potongan langsung Rp 25.000',
            'discount_type' => 'FIXED',
            'discount_value' => 25000,
            'max_discount_amount' => null,
            'min_order_amount' => 75000,
            'scope' => 'all',
            'max_usage_total' => 50,
            'max_usage_per_user' => 1,
            'usage_count_total' => 0,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonths(2),
            'is_active' => true,
        ]);

        Voucher::updateOrCreate(['code' => 'GRATIS50K'], [
            'name' => 'Gratis 50 Ribu',
            'description' => 'Potongan langsung Rp 50.000 untuk minimum order Rp 200.000',
            'discount_type' => 'FIXED',
            'discount_value' => 50000,
            'max_discount_amount' => null,
            'min_order_amount' => 200000,
            'scope' => 'all',
            'max_usage_total' => 20,
            'max_usage_per_user' => 1,
            'usage_count_total' => 0,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonth(),
            'is_active' => true,
        ]);

        if ($firstVenue) {
            Voucher::updateOrCreate(['code' => 'VENUE20'], [
                'name' => 'Diskon 20% Venue ' . $firstVenue->name,
                'description' => 'Potongan 20% khusus venue ' . $firstVenue->name . ', maks Rp 100.000',
                'discount_type' => 'PERCENTAGE',
                'discount_value' => 20,
                'max_discount_amount' => 100000,
                'min_order_amount' => 0,
                'scope' => 'venue',
                'venue_id' => $firstVenue->id,
                'max_usage_total' => 30,
                'max_usage_per_user' => 1,
                'usage_count_total' => 0,
                'valid_from' => now()->subDay(),
                'valid_until' => now()->addMonths(2),
                'is_active' => true,
            ]);
        }

        Voucher::updateOrCreate(['code' => 'EXPIRED01'], [
            'name' => 'Voucher Expired',
            'description' => 'Voucher ini sudah tidak berlaku',
            'discount_type' => 'FIXED',
            'discount_value' => 10000,
            'scope' => 'all',
            'max_usage_total' => 10,
            'max_usage_per_user' => 1,
            'usage_count_total' => 0,
            'valid_from' => now()->subMonths(2),
            'valid_until' => now()->subDay(),
            'is_active' => true,
        ]);

        Voucher::updateOrCreate(['code' => 'HABIS01'], [
            'name' => 'Voucher Kuota Habis',
            'description' => 'Voucher ini kuotanya habis',
            'discount_type' => 'FIXED',
            'discount_value' => 10000,
            'scope' => 'all',
            'max_usage_total' => 1,
            'max_usage_per_user' => 1,
            'usage_count_total' => 1,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addMonth(),
            'is_active' => true,
        ]);
    }
}
