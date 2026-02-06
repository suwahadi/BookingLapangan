<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Admin\VenueAdminService;
use App\Services\Admin\VenueCourtAdminService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VenueSeeder extends Seeder
{
    public function __construct(
        private readonly VenueAdminService $venueAdmin,
        private readonly VenueCourtAdminService $courtAdmin
    ) {}

    public function run(): void
    {
        // Pastikan ada user admin/system untuk actorUserId
        $admin = User::firstOrCreate(
            ['email' => 'admin@booking.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );

        $actorId = $admin->id;

        // 1. Create Venue
        // Service ini akan otomatis membuat default settings & operating hours
        $venue = $this->venueAdmin->create([
            'name' => 'Gor Badminton Juara',
            'city' => 'Jakarta Selatan',
            'address' => 'Jl. RS Fatmawati No. 10',
            'is_active' => true,
        ], $actorId);

        // Update venue menjadi active (karena default create false/inactive)
        $this->venueAdmin->update($venue, [
            'name' => 'Gor Badminton Juara',
            'city' => 'Jakarta Selatan',
            'address' => 'Jl. RS Fatmawati No. 10',
            'is_active' => true,
        ], $actorId);
        
        $this->command->info('Venue created: ' . $venue->name);

        // 2. Create Courts
        $courts = [
            ['name' => 'Lapangan A (Karpet)', 'sport' => 'Badminton', 'floor_type' => 'Karpet'],
            ['name' => 'Lapangan B (Karpet)', 'sport' => 'Badminton', 'floor_type' => 'Karpet'],
            ['name' => 'Lapangan C (Parket)', 'sport' => 'Badminton', 'floor_type' => 'Parket'],
        ];

        foreach ($courts as $c) {
            $court = $this->courtAdmin->create($venue, [
                'name' => $c['name'],
                'sport_type' => $c['sport'], // Map ke sport_type di service arg, tapi di DB 'sport'
                'floor_type' => $c['floor_type'],
            ], $actorId);
            
            $this->command->info(' - Court created: ' . $court->name);
        }

        // 3. Setup Pricing (Manual via Model for now, or via Service if exists)
        // VenuePricing belum punya admin service dedicated di specs, kita isi manual via model
        // Taruh harga default 50rb weekday, 70rb weekend
        // Kita isi untuk court pertama saja sebagai contoh

        $court1 = $venue->courts()->first();
        DB::table('venue_pricings')->insert([
            'venue_court_id' => $court1->id,
            'day_of_week' => 1, // Senin
            'start_time' => '08:00',
            'end_time' => '23:00',
            'price_per_hour' => 50000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Copy ke hari lain (simple logic)
        for ($d = 2; $d <= 5; $d++) { // Selasa-Jumat
            DB::table('venue_pricings')->insert([
                'venue_court_id' => $court1->id,
                'day_of_week' => $d,
                'start_time' => '08:00',
                'end_time' => '23:00',
                'price_per_hour' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        for ($d = 6; $d <= 7; $d++) { // Sabtu-Minggu
            DB::table('venue_pricings')->insert([
                'venue_court_id' => $court1->id,
                'day_of_week' => $d,
                'start_time' => '08:00',
                'end_time' => '23:00',
                'price_per_hour' => 75000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info('Pricing setup for: ' . $court1->name);
    }
}
