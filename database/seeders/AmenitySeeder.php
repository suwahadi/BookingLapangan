<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('amenities')->truncate();

        $now = now();
        $amenities = [
            ['name' => 'Parkir Mobil', 'icon' => 'car', 'category' => 'parking', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Parkir Motor', 'icon' => 'motorcycle', 'category' => 'parking', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Parkir Gratis', 'icon' => 'parking', 'category' => 'parking', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Toilet', 'icon' => 'toilet', 'category' => 'facility', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mushola', 'icon' => 'mosque', 'category' => 'facility', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ruang Ganti', 'icon' => 'door', 'category' => 'facility', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Shower/Kamar Mandi', 'icon' => 'shower', 'category' => 'facility', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Loker', 'icon' => 'locker', 'category' => 'facility', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'AC/Kipas Angin', 'icon' => 'fan', 'category' => 'comfort', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Wi-Fi Gratis', 'icon' => 'wifi', 'category' => 'comfort', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tribun Penonton', 'icon' => 'users', 'category' => 'comfort', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ruang Tunggu', 'icon' => 'sofa', 'category' => 'comfort', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kantin/Cafe', 'icon' => 'coffee', 'category' => 'food', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vending Machine', 'icon' => 'vending', 'category' => 'food', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Dispenser Air', 'icon' => 'water', 'category' => 'food', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sewa Raket', 'icon' => 'racket', 'category' => 'equipment', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sewa Bola', 'icon' => 'ball', 'category' => 'equipment', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sewa Sepatu', 'icon' => 'shoe', 'category' => 'equipment', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'CCTV', 'icon' => 'camera', 'category' => 'security', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Satpam 24 Jam', 'icon' => 'shield', 'category' => 'security', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Akses Disabilitas', 'icon' => 'wheelchair', 'category' => 'accessibility', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('amenities')->insert($amenities);
    }
}
