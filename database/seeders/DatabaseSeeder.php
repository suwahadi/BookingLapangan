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
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = ['booking.view', 'booking.confirm', 'booking.cancel', 'venue.view', 'venue.create', 'venue.update', 'venue.delete', 'refund.view', 'refund.approve', 'refund.execute', 'finance.view', 'finance.export', 'settlement.create', 'user.view', 'user.manage', 'audit.view'];
        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web'])->syncPermissions(Permission::all());
        Role::firstOrCreate(['name' => 'admin-finance', 'guard_name' => 'web'])->syncPermissions(['booking.view', 'refund.view', 'refund.approve', 'refund.execute', 'finance.view', 'finance.export', 'settlement.create']);
        Role::firstOrCreate(['name' => 'admin-operator', 'guard_name' => 'web'])->syncPermissions(['booking.view', 'booking.confirm', 'booking.cancel', 'venue.view', 'venue.create', 'venue.update', 'refund.view']);
        Role::firstOrCreate(['name' => 'admin-viewer', 'guard_name' => 'web'])->syncPermissions(['booking.view', 'venue.view', 'refund.view', 'finance.view', 'audit.view']);

        $amenitiesList = [
            ['name' => 'Parkir Mobil', 'icon' => 'local_parking', 'category' => 'parking'],
            ['name' => 'Parkir Motor', 'icon' => 'two_wheeler', 'category' => 'parking'],
            ['name' => 'Toilet', 'icon' => 'wc', 'category' => 'facility'],
            ['name' => 'Mushola', 'icon' => 'mosque', 'category' => 'facility'],
            ['name' => 'Ruang Ganti', 'icon' => 'checkroom', 'category' => 'facility'],
            ['name' => 'Shower', 'icon' => 'shower', 'category' => 'facility'],
            ['name' => 'Wi-Fi', 'icon' => 'wifi', 'category' => 'comfort'],
            ['name' => 'Kantin', 'icon' => 'restaurant', 'category' => 'food'],
            ['name' => 'CCTV', 'icon' => 'videocam', 'category' => 'security'],
            ['name' => 'Tribun Penonton', 'icon' => 'groups', 'category' => 'comfort'],
            ['name' => 'AC', 'icon' => 'ac_unit', 'category' => 'comfort'],
            ['name' => 'Sewa Raket', 'icon' => 'sports_tennis', 'category' => 'equipment'],
        ];
        foreach ($amenitiesList as $a) {
            Amenity::updateOrCreate(['name' => $a['name']], $a + ['is_active' => true]);
        }
        $allAmenities = Amenity::all();

        $adminPassword = Hash::make('admin123');
        $admins = [
            ['email' => 'admin@booking.com', 'name' => 'Super Admin', 'role' => 'super-admin'],
            ['email' => 'finance@booking.com', 'name' => 'Finance Admin', 'role' => 'admin-finance'],
            ['email' => 'operator@booking.com', 'name' => 'Operator Lapangan', 'role' => 'admin-operator'],
            ['email' => 'office@booking.com', 'name' => 'Back Office', 'role' => 'admin-viewer'],
        ];
        foreach ($admins as $u) {
            $user = User::updateOrCreate(['email' => $u['email']], ['name' => $u['name'], 'password' => $adminPassword, 'is_admin' => true]);
            $user->syncRoles([$u['role']]);
        }

        $userPassword = Hash::make('password');
        for ($i = 1; $i <= 10; $i++) {
            User::updateOrCreate(
                ['email' => "user{$i}@gmail.com"],
                ['name' => "Pelanggan {$i}", 'password' => $userPassword, 'is_admin' => false]
            );
        }

        $venues = [
            [
                'name' => 'Elite Kuningan Arena',
                'sport_type' => 'Badminton',
                'address' => 'Jl. HR Rasuna Said Kav. C-22, Kuningan',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12940',
                'phone' => '02152893456',
                'email' => 'info@elitekuningan.com',
                'description' => 'Arena badminton premium di kawasan Kuningan dengan 4 lapangan berstandar BWF. Lantai karpet impor, pencahayaan LED profesional, dan AC sentral untuk kenyamanan bermain.',
                'courts' => [
                    ['name' => 'Lapangan A (Karpet)', 'sport' => 'Badminton', 'floor_type' => 'Karpet'],
                    ['name' => 'Lapangan B (Karpet)', 'sport' => 'Badminton', 'floor_type' => 'Karpet'],
                    ['name' => 'Lapangan C (Parket)', 'sport' => 'Badminton', 'floor_type' => 'Parket'],
                    ['name' => 'Lapangan D (Parket)', 'sport' => 'Badminton', 'floor_type' => 'Parket'],
                ],
                'images' => ['venues/badminton_1_1.jpg', 'venues/badminton_1_2.jpg', 'venues/badminton_1_3.jpg', 'venues/facility_1_1.jpg'],
                'weekday_price' => 80000,
                'weekend_price' => 120000,
                'prime_extra' => 30000,
                'setting' => ['slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 180],
                'policy' => ['allow_dp' => true, 'dp_min_percent' => 50, 'reschedule_allowed' => true, 'reschedule_deadline_hours' => 24, 'refund_allowed' => true],
                'hours' => ['open' => '07:00:00', 'close' => '23:00:00'],
                'amenities' => ['Parkir Mobil', 'Parkir Motor', 'Toilet', 'Mushola', 'Ruang Ganti', 'Shower', 'Wi-Fi', 'AC', 'CCTV', 'Sewa Raket'],
            ],
            [
                'name' => 'Senayan Sport Center',
                'sport_type' => 'Tennis',
                'address' => 'Jl. Asia Afrika No. 8, Senayan',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postal_code' => '10270',
                'phone' => '02157941234',
                'email' => 'booking@senayansport.id',
                'description' => 'Pusat olahraga tenis & padel di jantung Senayan. Lapangan outdoor berstandar ITF dengan pencahayaan malam hari dan tribun penonton.',
                'courts' => [
                    ['name' => 'Tennis Court 1 (Hard)', 'sport' => 'Tennis', 'floor_type' => 'Hard Court'],
                    ['name' => 'Tennis Court 2 (Clay)', 'sport' => 'Tennis', 'floor_type' => 'Clay'],
                    ['name' => 'Padel Court 1', 'sport' => 'Padel', 'floor_type' => 'Artificial Turf'],
                ],
                'images' => ['venues/tennis_1_1.jpg', 'venues/tennis_1_2.jpg', 'venues/padel_1_1.jpg', 'venues/venue_1_1.jpg'],
                'weekday_price' => 100000,
                'weekend_price' => 150000,
                'prime_extra' => 50000,
                'setting' => ['slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 120],
                'policy' => ['allow_dp' => true, 'dp_min_percent' => 50, 'reschedule_allowed' => true, 'reschedule_deadline_hours' => 48, 'refund_allowed' => true],
                'hours' => ['open' => '06:00:00', 'close' => '22:00:00'],
                'amenities' => ['Parkir Mobil', 'Parkir Motor', 'Toilet', 'Shower', 'Wi-Fi', 'Kantin', 'Tribun Penonton', 'CCTV'],
            ],
            [
                'name' => 'Sunter Lake Hub',
                'sport_type' => 'Futsal',
                'address' => 'Jl. Danau Sunter Utara Blok G-3',
                'city' => 'Jakarta Utara',
                'province' => 'DKI Jakarta',
                'postal_code' => '14350',
                'phone' => '02165311888',
                'email' => 'hello@sunterlakehub.com',
                'description' => 'Kompleks futsal & basket modern di kawasan Sunter. Lapangan futsal rumput sintetis FIFA Quality dan lapangan basket indoor full-size.',
                'courts' => [
                    ['name' => 'Futsal Field A', 'sport' => 'Futsal', 'floor_type' => 'Rumput Sintetis'],
                    ['name' => 'Futsal Field B', 'sport' => 'Futsal', 'floor_type' => 'Rumput Sintetis'],
                    ['name' => 'Basketball Court 1', 'sport' => 'Basketball', 'floor_type' => 'Vinyl'],
                ],
                'images' => ['venues/futsal_1_1.jpg', 'venues/futsal_1_2.jpg', 'venues/basketball_1_1.jpg', 'venues/venue_1_2.jpg'],
                'weekday_price' => 150000,
                'weekend_price' => 250000,
                'prime_extra' => 50000,
                'setting' => ['slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 180],
                'policy' => ['allow_dp' => true, 'dp_min_percent' => 50, 'reschedule_allowed' => true, 'reschedule_deadline_hours' => 12, 'refund_allowed' => true],
                'hours' => ['open' => '08:00:00', 'close' => '23:00:00'],
                'amenities' => ['Parkir Mobil', 'Parkir Motor', 'Toilet', 'Ruang Ganti', 'Shower', 'Wi-Fi', 'Kantin', 'CCTV'],
            ],
            [
                'name' => 'Kelapa Gading Hall',
                'sport_type' => 'Badminton',
                'address' => 'Jl. Boulevard Raya Blok QJ-5, Kelapa Gading',
                'city' => 'Jakarta Utara',
                'province' => 'DKI Jakarta',
                'postal_code' => '14240',
                'phone' => '02145861234',
                'email' => 'info@kghall.id',
                'description' => 'GOR badminton terbesar di Kelapa Gading dengan 6 lapangan. Tersedia juga meja pingpong. Fasilitas lengkap termasuk kantin, mushola, dan parkir luas.',
                'courts' => [
                    ['name' => 'Badminton 1 (Vinyl)', 'sport' => 'Badminton', 'floor_type' => 'Vinyl'],
                    ['name' => 'Badminton 2 (Vinyl)', 'sport' => 'Badminton', 'floor_type' => 'Vinyl'],
                    ['name' => 'Badminton 3 (Karpet)', 'sport' => 'Badminton', 'floor_type' => 'Karpet'],
                    ['name' => 'Table Tennis Area', 'sport' => 'Table Tennis', 'floor_type' => 'Vinyl'],
                ],
                'images' => ['venues/badminton_1_2.jpg', 'venues/badminton_1_3.jpg', 'venues/tabletennis_1_1.jpg', 'venues/facility_1_2.jpg'],
                'weekday_price' => 60000,
                'weekend_price' => 90000,
                'prime_extra' => 20000,
                'setting' => ['slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 240],
                'policy' => ['allow_dp' => false, 'dp_min_percent' => 100, 'reschedule_allowed' => true, 'reschedule_deadline_hours' => 24, 'refund_allowed' => false],
                'hours' => ['open' => '07:00:00', 'close' => '22:00:00'],
                'amenities' => ['Parkir Mobil', 'Parkir Motor', 'Toilet', 'Mushola', 'Ruang Ganti', 'Kantin', 'CCTV', 'Sewa Raket'],
            ],
            [
                'name' => 'Puri Sports Square',
                'sport_type' => 'Basketball',
                'address' => 'Jl. Puri Indah Raya Blok A-1, Kembangan',
                'city' => 'Jakarta Barat',
                'province' => 'DKI Jakarta',
                'postal_code' => '11610',
                'phone' => '02158302222',
                'email' => 'booking@purisports.com',
                'description' => 'Arena basket dan futsal premium di Puri Indah. Lapangan basket berstandar FIBA dengan lantai maple dan futsal FIFA Quality Pro.',
                'courts' => [
                    ['name' => 'Basket Full Court', 'sport' => 'Basketball', 'floor_type' => 'Maple'],
                    ['name' => 'Basket Half Court A', 'sport' => 'Basketball', 'floor_type' => 'Maple'],
                    ['name' => 'Futsal Indoor 1', 'sport' => 'Futsal', 'floor_type' => 'Rumput Sintetis'],
                ],
                'images' => ['venues/basketball_1_2.jpg', 'venues/basketball_1_3.jpg', 'venues/futsal_1_3.jpg', 'venues/venue_1_3.jpg'],
                'weekday_price' => 200000,
                'weekend_price' => 350000,
                'prime_extra' => 75000,
                'setting' => ['slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 180],
                'policy' => ['allow_dp' => true, 'dp_min_percent' => 50, 'reschedule_allowed' => true, 'reschedule_deadline_hours' => 24, 'refund_allowed' => true],
                'hours' => ['open' => '08:00:00', 'close' => '22:00:00'],
                'amenities' => ['Parkir Mobil', 'Toilet', 'Ruang Ganti', 'Shower', 'Wi-Fi', 'Kantin', 'AC', 'Tribun Penonton', 'CCTV'],
            ],
            [
                'name' => 'Cilandak Athletic',
                'sport_type' => 'Mini Soccer',
                'address' => 'Jl. TB Simatupang No. 38, Cilandak',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12430',
                'phone' => '02178884567',
                'email' => 'info@cilandakathletic.id',
                'description' => 'Lapangan mini soccer outdoor premium dengan rumput sintetis FIFA Quality. Dilengkapi pencahayaan malam dan lapangan tenis dengan surface hard court.',
                'courts' => [
                    ['name' => 'Mini Soccer Field A', 'sport' => 'Mini Soccer', 'floor_type' => 'Rumput Sintetis'],
                    ['name' => 'Mini Soccer Field B', 'sport' => 'Mini Soccer', 'floor_type' => 'Rumput Sintetis'],
                    ['name' => 'Tennis Outdoor', 'sport' => 'Tennis', 'floor_type' => 'Hard Court'],
                ],
                'images' => ['venues/soccer_1_1.jpg', 'venues/soccer_1_2.jpg', 'venues/tennis_1_3.jpg', 'venues/facility_1_3.jpg'],
                'weekday_price' => 250000,
                'weekend_price' => 400000,
                'prime_extra' => 100000,
                'setting' => ['slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 120],
                'policy' => ['allow_dp' => true, 'dp_min_percent' => 50, 'reschedule_allowed' => false, 'reschedule_deadline_hours' => 0, 'refund_allowed' => true],
                'hours' => ['open' => '06:00:00', 'close' => '23:00:00'],
                'amenities' => ['Parkir Mobil', 'Parkir Motor', 'Toilet', 'Ruang Ganti', 'Shower', 'CCTV'],
            ],
            [
                'name' => 'Kemang Fusion Ground',
                'sport_type' => 'Padel',
                'address' => 'Jl. Kemang Raya No. 72, Bangka',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12730',
                'phone' => '02171893456',
                'email' => 'play@kemangfusion.com',
                'description' => 'Padel & futsal club lifestyle di Kemang. Padel court berstandar internasional dengan kaca tempered dan turf premium. Suasana tropis modern.',
                'courts' => [
                    ['name' => 'Padel Court A', 'sport' => 'Padel', 'floor_type' => 'Artificial Turf'],
                    ['name' => 'Padel Court B', 'sport' => 'Padel', 'floor_type' => 'Artificial Turf'],
                    ['name' => 'Futsal Premium', 'sport' => 'Futsal', 'floor_type' => 'Rumput Sintetis'],
                ],
                'images' => ['venues/padel_1_2.jpg', 'venues/padel_1_3.jpg', 'venues/futsal_1_1.jpg', 'venues/facility_1_1.jpg'],
                'weekday_price' => 180000,
                'weekend_price' => 280000,
                'prime_extra' => 70000,
                'setting' => ['slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 120],
                'policy' => ['allow_dp' => true, 'dp_min_percent' => 50, 'reschedule_allowed' => true, 'reschedule_deadline_hours' => 12, 'refund_allowed' => true],
                'hours' => ['open' => '07:00:00', 'close' => '23:00:00'],
                'amenities' => ['Parkir Mobil', 'Parkir Motor', 'Toilet', 'Shower', 'Wi-Fi', 'Kantin', 'AC', 'CCTV'],
            ],
            [
                'name' => 'Tebet Green Field',
                'sport_type' => 'Futsal',
                'address' => 'Jl. Tebet Raya No. 15, Tebet',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12810',
                'phone' => '02183712345',
                'email' => 'cs@tebetgreenfield.id',
                'description' => 'Lapangan futsal dan badminton populer di Tebet. Harga terjangkau dengan fasilitas lengkap. Cocok untuk komunitas dan pertandingan amatir.',
                'courts' => [
                    ['name' => 'Futsal Green 1', 'sport' => 'Futsal', 'floor_type' => 'Rumput Sintetis'],
                    ['name' => 'Futsal Green 2', 'sport' => 'Futsal', 'floor_type' => 'Rumput Sintetis'],
                    ['name' => 'Badminton Tebet 1', 'sport' => 'Badminton', 'floor_type' => 'Karpet'],
                ],
                'images' => ['venues/futsal_1_2.jpg', 'venues/futsal_1_3.jpg', 'venues/badminton_1_1.jpg', 'venues/venue_1_1.jpg'],
                'weekday_price' => 100000,
                'weekend_price' => 175000,
                'prime_extra' => 25000,
                'setting' => ['slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 180],
                'policy' => ['allow_dp' => true, 'dp_min_percent' => 50, 'reschedule_allowed' => true, 'reschedule_deadline_hours' => 6, 'refund_allowed' => false],
                'hours' => ['open' => '08:00:00', 'close' => '24:00:00'],
                'amenities' => ['Parkir Motor', 'Toilet', 'Mushola', 'Ruang Ganti', 'Wi-Fi', 'Kantin', 'CCTV'],
            ],
            [
                'name' => 'Pluit Samudra Center',
                'sport_type' => 'Tennis',
                'address' => 'Jl. Pluit Samudra IV No. 88, Penjaringan',
                'city' => 'Jakarta Utara',
                'province' => 'DKI Jakarta',
                'postal_code' => '14450',
                'phone' => '02166611888',
                'email' => 'info@pluitsamudra.com',
                'description' => 'Pusat tenis dan badminton di kawasan Pluit. Lapangan tenis outdoor dengan pencahayaan LED dan badminton indoor ber-AC.',
                'courts' => [
                    ['name' => 'Tennis Hard Court 1', 'sport' => 'Tennis', 'floor_type' => 'Hard Court'],
                    ['name' => 'Tennis Hard Court 2', 'sport' => 'Tennis', 'floor_type' => 'Hard Court'],
                    ['name' => 'Badminton Indoor 1', 'sport' => 'Badminton', 'floor_type' => 'Vinyl'],
                    ['name' => 'Badminton Indoor 2', 'sport' => 'Badminton', 'floor_type' => 'Vinyl'],
                ],
                'images' => ['venues/tennis_1_2.jpg', 'venues/tennis_1_3.jpg', 'venues/badminton_1_3.jpg', 'venues/venue_1_3.jpg'],
                'weekday_price' => 75000,
                'weekend_price' => 110000,
                'prime_extra' => 25000,
                'setting' => ['slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 180],
                'policy' => ['allow_dp' => true, 'dp_min_percent' => 50, 'reschedule_allowed' => true, 'reschedule_deadline_hours' => 24, 'refund_allowed' => true],
                'hours' => ['open' => '06:00:00', 'close' => '22:00:00'],
                'amenities' => ['Parkir Mobil', 'Parkir Motor', 'Toilet', 'Ruang Ganti', 'Shower', 'Wi-Fi', 'AC', 'CCTV', 'Sewa Raket'],
            ],
            [
                'name' => 'Blok M Sporty',
                'sport_type' => 'Volleyball',
                'address' => 'Jl. Melawai Raya No. 55, Blok M',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12160',
                'phone' => '02172305678',
                'email' => 'play@blokmsporty.com',
                'description' => 'Arena voli dan basket di kawasan Blok M. Lapangan voli indoor berstandar FIVB dan basket half court. Lokasi strategis dekat transportasi publik.',
                'courts' => [
                    ['name' => 'Volleyball Court 1', 'sport' => 'Volleyball', 'floor_type' => 'Vinyl'],
                    ['name' => 'Volleyball Court 2', 'sport' => 'Volleyball', 'floor_type' => 'Vinyl'],
                    ['name' => 'Basketball Half Court', 'sport' => 'Basketball', 'floor_type' => 'Vinyl'],
                ],
                'images' => ['venues/volleyball_1_1.jpg', 'venues/volleyball_1_2.jpg', 'venues/basketball_1_1.jpg', 'venues/venue_1_2.jpg'],
                'weekday_price' => 120000,
                'weekend_price' => 200000,
                'prime_extra' => 50000,
                'setting' => ['slot_minutes' => 60, 'min_duration_minutes' => 60, 'max_duration_minutes' => 180],
                'policy' => ['allow_dp' => true, 'dp_min_percent' => 50, 'reschedule_allowed' => true, 'reschedule_deadline_hours' => 24, 'refund_allowed' => true],
                'hours' => ['open' => '07:00:00', 'close' => '22:00:00'],
                'amenities' => ['Parkir Motor', 'Toilet', 'Mushola', 'Ruang Ganti', 'Wi-Fi', 'Kantin', 'Tribun Penonton', 'CCTV'],
            ],
        ];

        foreach ($venues as $vData) {
            $v = Venue::updateOrCreate(['slug' => Str::slug($vData['name'])], [
                'name' => $vData['name'],
                'slug' => Str::slug($vData['name']),
                'sport_type' => $vData['sport_type'],
                'address' => $vData['address'],
                'city' => $vData['city'],
                'province' => $vData['province'],
                'postal_code' => $vData['postal_code'],
                'phone' => $vData['phone'],
                'email' => $vData['email'],
                'description' => $vData['description'],
                'is_active' => true,
            ]);

            $v->media()->delete();
            foreach ($vData['images'] as $i => $imgPath) {
                VenueMedia::create([
                    'venue_id' => $v->id,
                    'file_path' => $imgPath,
                    'is_cover' => ($i === 0),
                    'order_column' => $i + 1,
                ]);
            }

            $amenityIds = $allAmenities->whereIn('name', $vData['amenities'])->pluck('id')->all();
            $v->amenities()->sync($amenityIds);

            VenueSetting::updateOrCreate(['venue_id' => $v->id], $vData['setting']);

            VenuePolicy::updateOrCreate(['venue_id' => $v->id], array_merge($vData['policy'], [
                'refund_rules' => ['h_minus_7' => 100, 'h_minus_3' => 50, 'below_3' => 0],
            ]));

            VenueOperatingHour::where('venue_id', $v->id)->delete();
            for ($d = 0; $d <= 6; $d++) {
                VenueOperatingHour::create([
                    'venue_id' => $v->id,
                    'day_of_week' => $d,
                    'is_closed' => false,
                    'open_time' => $vData['hours']['open'],
                    'close_time' => $vData['hours']['close'],
                ]);
            }

            $openHour = (int) substr($vData['hours']['open'], 0, 2);
            $closeHour = (int) substr($vData['hours']['close'], 0, 2);
            if ($closeHour === 0) $closeHour = 24;

            foreach ($vData['courts'] as $cData) {
                $court = VenueCourt::updateOrCreate(
                    ['venue_id' => $v->id, 'name' => $cData['name']],
                    ['sport' => $cData['sport'], 'floor_type' => $cData['floor_type'] ?? null, 'is_active' => true]
                );

                VenuePricing::where('venue_court_id', $court->id)->delete();
                $prices = [];
                for ($day = 1; $day <= 7; $day++) {
                    $isWeekend = ($day >= 6);
                    $basePrice = $isWeekend ? $vData['weekend_price'] : $vData['weekday_price'];

                    for ($hour = $openHour; $hour < $closeHour; $hour++) {
                        $price = $basePrice;
                        if ($hour >= 17 && $hour <= 21) {
                            $price += $vData['prime_extra'];
                        }

                        $prices[] = [
                            'venue_court_id' => $court->id,
                            'day_of_week' => $day,
                            'start_time' => str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00',
                            'end_time' => str_pad($hour + 1, 2, '0', STR_PAD_LEFT) . ':00:00',
                            'price_per_hour' => $price,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                VenuePricing::insert($prices);
            }
        }

        $this->call(VoucherSeeder::class);
    }
}
