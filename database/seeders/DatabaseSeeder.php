<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueCourt;
use App\Models\VenueMedia;
use App\Models\VenueOperatingHour;
use App\Models\VenuePolicy;
use App\Models\VenuePricing;
use App\Models\VenueSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\CarbonImmutable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $permissions = ['booking.view', 'booking.confirm', 'booking.cancel', 'venue.view', 'venue.create', 'venue.update', 'venue.delete', 'refund.view', 'refund.approve', 'refund.execute', 'finance.view', 'finance.export', 'settlement.create', 'user.view', 'user.manage', 'audit.view'];
        foreach ($permissions as $p) { Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']); }

        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web'])->syncPermissions(Permission::all());
        Role::firstOrCreate(['name' => 'admin-finance', 'guard_name' => 'web'])->syncPermissions(['booking.view', 'refund.view', 'refund.approve', 'refund.execute', 'finance.view', 'finance.export', 'settlement.create']);
        Role::firstOrCreate(['name' => 'admin-operator', 'guard_name' => 'web'])->syncPermissions(['booking.view', 'booking.confirm', 'booking.cancel', 'venue.view', 'venue.create', 'venue.update', 'refund.view']);
        Role::firstOrCreate(['name' => 'admin-viewer', 'guard_name' => 'web'])->syncPermissions(['booking.view', 'venue.view', 'refund.view', 'finance.view', 'audit.view']);

        // 2. Amenities
        $amenitiesList = [
            ['name' => 'Parkir Mobil', 'icon' => 'car', 'category' => 'parking'],
            ['name' => 'Parkir Motor', 'icon' => 'motorcycle', 'category' => 'parking'],
            ['name' => 'Toilet', 'icon' => 'toilet', 'category' => 'facility'],
            ['name' => 'Mushola', 'icon' => 'mosque', 'category' => 'facility'],
            ['name' => 'Ruang Ganti', 'icon' => 'door', 'category' => 'facility'],
            ['name' => 'Shower', 'icon' => 'shower', 'category' => 'facility'],
            ['name' => 'Wi-Fi', 'icon' => 'wifi', 'category' => 'comfort'],
            ['name' => 'Kantin', 'icon' => 'coffee', 'category' => 'food'],
            ['name' => 'CCTV', 'icon' => 'camera', 'category' => 'security'],
        ];
        foreach ($amenitiesList as $a) { Amenity::updateOrCreate(['name' => $a['name']], $a); }
        $allAmenities = Amenity::all();

        // 3. Admin Users
        $password = Hash::make('admin123');
        $users = [
            ['email' => 'admin@booking.com', 'name' => 'Super Admin', 'role' => 'super-admin'],
            ['email' => 'finance@booking.com', 'name' => 'Finance Admin', 'role' => 'admin-finance'],
            ['email' => 'operator@booking.com', 'name' => 'Operator Lapangan', 'role' => 'admin-operator'],
            ['email' => 'office@booking.com', 'name' => 'Back Office', 'role' => 'admin-viewer'],
        ];
        foreach ($users as $u) {
            $user = User::updateOrCreate(['email' => $u['email']], ['name' => $u['name'], 'password' => $password, 'is_admin' => true]);
            $user->syncRoles([$u['role']]);
        }

        // 4. Regular Users
        for ($i = 1; $i <= 10; $i++) {
            User::updateOrCreate(['email' => "user{$i}@gmail.com"], ['name' => "Pelanggan {$i}", 'password' => Hash::make('password'), 'is_admin' => false]);
        }

        // 5. Venues in Jakarta
        $jgData = [
            ['name' => 'Elite Kuningan Arena', 'loc' => 'Jakarta Selatan', 'sports' => ['Badminton', 'Futsal'], 'keyword' => 'futsal'],
            ['name' => 'Senayan Sport Center', 'loc' => 'Jakarta Pusat', 'sports' => ['Tennis', 'Padel'], 'keyword' => 'tennis'],
            ['name' => 'Sunter Lake Hub', 'loc' => 'Jakarta Utara', 'sports' => ['Futsal', 'Basketball'], 'keyword' => 'basketball'],
            ['name' => 'Kelapa Gading Hall', 'loc' => 'Jakarta Utara', 'sports' => ['Badminton', 'Table Tennis'], 'keyword' => 'badminton'],
            ['name' => 'Puri Sports Square', 'loc' => 'Jakarta Barat', 'sports' => ['Basketball', 'Futsal'], 'keyword' => 'stadium'],
            ['name' => 'Cilandak Athletic', 'loc' => 'Jakarta Selatan', 'sports' => ['Mini Soccer', 'Tennis'], 'keyword' => 'soccer'],
            ['name' => 'Kemang Fusion Ground', 'loc' => 'Jakarta Selatan', 'sports' => ['Padel', 'Futsal'], 'keyword' => 'padel'],
            ['name' => 'Tebet Green Field', 'loc' => 'Jakarta Selatan', 'sports' => ['Badminton', 'Soccer'], 'keyword' => 'sports'],
            ['name' => 'Pluit Samudra Center', 'loc' => 'Jakarta Utara', 'sports' => ['Tennis', 'Badminton'], 'keyword' => 'gym'],
            ['name' => 'Blok M Sporty', 'loc' => 'Jakarta Selatan', 'sports' => ['Basketball', 'Volleyball'], 'keyword' => 'volleyball'],
        ];

        foreach ($jgData as $venueInfo) {
            $v = Venue::create([
                'name' => $venueInfo['name'],
                'slug' => Str::slug($venueInfo['name']),
                'sport_type' => $venueInfo['sports'][0],
                'address' => "Jalan " . $venueInfo['name'] . " No. " . rand(1, 99),
                'city' => $venueInfo['loc'],
                'province' => 'DKI Jakarta',
                'postal_code' => '12' . rand(1,9) . rand(1,9) . rand(1,9),
                'phone' => '021' . rand(1111111, 8888888),
                'email' => 'contact@' . Str::slug($venueInfo['name']) . '.com',
                'is_active' => true,
            ]);

            // Venue Media (3 items per venue)
            for ($m = 1; $m <= 3; $m++) {
                VenueMedia::create([
                    'venue_id' => $v->id,
                    'file_path' => "https://source.unsplash.com/featured/400x300/?{$venueInfo['keyword']},sport&sig=" . rand(1, 1000),
                    'is_cover' => ($m === 1),
                    'order_column' => $m,
                ]);
            }

            $v->amenities()->attach($allAmenities->random(rand(3, 6))->pluck('id')->all());
            
            VenueSetting::create(['venue_id' => $v->id, 'slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 180]);
            
            VenuePolicy::create([
                'venue_id' => $v->id, 
                'allow_dp' => true, 
                'dp_min_percent' => 50, 
                'reschedule_allowed' => true, 
                'reschedule_deadline_hours' => 24,
                'refund_allowed' => true,
                'refund_rules' => ['h_minus_7' => 100, 'h_minus_3' => 50, 'below_3' => 0]
            ]);

            for ($d_of_w = 0; $d_of_w <= 6; $d_of_w++) {
                VenueOperatingHour::create(['venue_id' => $v->id, 'day_of_week' => $d_of_w, 'is_closed' => false, 'open_time' => '07:00:00', 'close_time' => '22:00:00']);
            }

            foreach ($venueInfo['sports'] as $sport) {
                $c = VenueCourt::create(['venue_id' => $v->id, 'name' => "$sport Court " . rand(1, 3), 'sport' => $sport, 'is_active' => true]);
                
                $prices = [];
                for ($day = 1; $day <= 7; $day++) {
                    $isWeekend = ($day >= 6);
                    for ($hour = 7; $hour <= 21; $hour++) {
                        $price = $isWeekend ? 120000 : 75000;
                        if ($hour >= 17) $price += 25000;
                        
                        $prices[] = [
                            'venue_court_id' => $c->id,
                            'day_of_week' => $day,
                            'start_time' => str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00',
                            'end_time' => str_pad($hour+1, 2, '0', STR_PAD_LEFT) . ':00:00',
                            'price_per_hour' => $price,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                VenuePricing::insert($prices);
            }
        }
    }
}
