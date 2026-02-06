<?php

namespace Database\Seeders;

use App\Enums\PaymentType;
use App\Models\Amenity;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueCourt;
use App\Models\VenueOperatingHour;
use App\Models\VenuePolicy;
use App\Models\VenuePricing;
use App\Models\VenueSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\CarbonImmutable;

class SystemTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $this->seedVenues();
    }

    private function seedUsers(): void
    {
        $adminPassword = Hash::make('admin123');

        // 1. Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@booking.com'],
            ['name' => 'Super Admin', 'password' => $adminPassword, 'is_admin' => true]
        );
        if (!$superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole('super-admin');
        }

        // 2. Finance
        $finance = User::updateOrCreate(
            ['email' => 'finance@booking.com'],
            ['name' => 'Finance Admin', 'password' => $adminPassword, 'is_admin' => true]
        );
        if (!$finance->hasRole('admin-finance')) {
            $finance->assignRole('admin-finance');
        }

        // 3. Operator
        $operator = User::updateOrCreate(
            ['email' => 'operator@booking.com'],
            ['name' => 'Operator Lapangan', 'password' => $adminPassword, 'is_admin' => true]
        );
        if (!$operator->hasRole('admin-operator')) {
            $operator->assignRole('admin-operator');
        }

        // 4. Back Office
        $backOffice = User::updateOrCreate(
            ['email' => 'office@booking.com'],
            ['name' => 'Back Office', 'password' => $adminPassword, 'is_admin' => true]
        );
        if (!$backOffice->hasRole('admin-viewer')) {
            $backOffice->assignRole('admin-viewer');
        }

        // 5. 10 Regular Users
        for ($i = 1; $i <= 10; $i++) {
            User::updateOrCreate(
                ['email' => "user{$i}@gmail.com"],
                [
                    'name' => "Member User {$i}",
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                    'phone' => '0812345678' . $i
                ]
            );
        }
    }

    private function seedVenues(): void
    {
        $venuesData = [
            [
                'name' => 'Elite Arena Kuningan',
                'address' => 'Jl. HR Rasuna Said No.10, Karet Kuningan',
                'city' => 'Jakarta Selatan',
                'courts' => [
                    ['name' => 'Court A (Badminton)', 'sport' => 'Badminton'],
                    ['name' => 'Court B (Badminton)', 'sport' => 'Badminton'],
                    ['name' => 'Main Field (Futsal)', 'sport' => 'Futsal'],
                ]
            ],
            [
                'name' => 'Gelora Bung Karno Sports Center',
                'address' => 'Jl. Pintu Satu Senayan, Gelora',
                'city' => 'Jakarta Pusat',
                'courts' => [
                    ['name' => 'Tennis Clay 1', 'sport' => 'Tennis'],
                    ['name' => 'Tennis Clay 2', 'sport' => 'Tennis'],
                    ['name' => 'Padel Court 1', 'sport' => 'Padel'],
                ]
            ],
            [
                'name' => 'Sunter Sport Hub',
                'address' => 'Jl. Danau Sunter Selatan No.1',
                'city' => 'Jakarta Utara',
                'courts' => [
                    ['name' => 'Futsal Vinyl 1', 'sport' => 'Futsal'],
                    ['name' => 'Basket Indoor 1', 'sport' => 'Basketball'],
                ]
            ],
            [
                'name' => 'Kelapa Gading Badminton Hall',
                'address' => 'Jl. Boulevard Barat Raya No.22',
                'city' => 'Jakarta Utara',
                'courts' => [
                    ['name' => 'Premium Court 1', 'sport' => 'Badminton'],
                    ['name' => 'Premium Court 2', 'sport' => 'Badminton'],
                ]
            ],
            [
                'name' => 'Senayan Padel Club',
                'address' => 'Kawasan Gelora Bung Karno, Senayan',
                'city' => 'Jakarta Pusat',
                'courts' => [
                    ['name' => 'Panoramic 1', 'sport' => 'Padel'],
                    ['name' => 'Panoramic 2', 'sport' => 'Padel'],
                    ['name' => 'Outdoor Padel', 'sport' => 'Padel'],
                ]
            ],
            [
                'name' => 'Puri Indah Tennis Court',
                'address' => 'Kembangan Selatan, Puri Indah',
                'city' => 'Jakarta Barat',
                'courts' => [
                    ['name' => 'Hard Court A', 'sport' => 'Tennis'],
                    ['name' => 'Hard Court B', 'sport' => 'Tennis'],
                ]
            ],
            [
                'name' => 'Cilandak Futsal Square',
                'address' => 'Jl. Cilandak Sipapat No.10',
                'city' => 'Jakarta Selatan',
                'courts' => [
                    ['name' => 'Interlock Field 1', 'sport' => 'Futsal'],
                    ['name' => 'Interlock Field 2', 'sport' => 'Futsal'],
                ]
            ],
            [
                'name' => 'Kemang Mini Soccer',
                'address' => 'Jl. Kemang Timur No.5',
                'city' => 'Jakarta Selatan',
                'courts' => [
                    ['name' => 'Field 1 (Synthetic)', 'sport' => 'Soccer'],
                    ['name' => 'Field 2 (Synthetic)', 'sport' => 'Soccer'],
                ]
            ],
            [
                'name' => 'Tebet Sports Square',
                'address' => 'Jl. Tebet Barat Dalam Raya No.15',
                'city' => 'Jakarta Selatan',
                'courts' => [
                    ['name' => 'Badminton Silver 1', 'sport' => 'Badminton'],
                    ['name' => 'Basketball 3x3', 'sport' => 'Basketball'],
                ]
            ],
            [
                'name' => 'Pluit Sport Center',
                'address' => 'Jl. Pluit Samudra No.1',
                'city' => 'Jakarta Utara',
                'courts' => [
                    ['name' => 'Tennis Ocean View', 'sport' => 'Tennis'],
                    ['name' => 'Futsal Ocean View', 'sport' => 'Futsal'],
                ]
            ],
        ];

        $amenities = Amenity::all();

        foreach ($venuesData as $vData) {
            $venue = Venue::create([
                'name' => $vData['name'],
                'slug' => Str::slug($vData['name']),
                'sport_type' => $vData['courts'][0]['sport'],
                'address' => $vData['address'],
                'city' => $vData['city'],
                'province' => 'DKI Jakarta',
                'postal_code' => '12' . rand(1, 9) . rand(1, 9) . rand(1, 9),
                'phone' => '021' . rand(1111111, 9999999),
                'email' => 'info@' . Str::slug($vData['name']) . '.com',
                'description' => "Fasilitas olahraga premium di " . $vData['city'] . " dengan standar internasional.",
                'is_active' => true,
            ]);

            // Settings & Policies
            VenueSetting::create([
                'venue_id' => $venue->id,
                'is_active' => true,
                'allow_dp' => true,
                'dp_percentage' => 50,
            ]);

            VenuePolicy::create([
                'venue_id' => $venue->id,
                'cancellation_policy' => "Pembatalan H-7 refund 100%, H-3 refund 50%.",
                'reschedule_policy' => "Reschedule diperbolehkan maksimal H-1.",
                'allow_dp' => true,
                'dp_percentage' => 50,
                'reschedule_allowed' => true,
                'reschedule_deadline_hours' => 24,
            ]);

            // Operating Hours (6:00 - 23:00)
            for ($day = 1; $day <= 7; $day++) {
                VenueOperatingHour::create([
                    'venue_id' => $venue->id,
                    'day' => $day,
                    'is_open' => true,
                    'open_time' => '06:00:00',
                    'close_time' => '23:00:00',
                ]);
            }

            // Amenities (Random 4-8 amenities)
            if ($amenities->isNotEmpty()) {
                $venue->amenities()->attach(
                    $amenities->random(min(rand(4, 9), $amenities->count()))->pluck('id')->all()
                );
            }

            // Courts & Pricing
            foreach ($vData['courts'] as $cData) {
                $court = VenueCourt::create([
                    'venue_id' => $venue->id,
                    'name' => $cData['name'],
                    'sport' => $cData['sport'],
                    'is_active' => true,
                ]);

                $this->seedPricings($court->id);
            }
        }
    }

    private function seedPricings(int $courtId): void
    {
        $pricings = [];
        $now = now();

        for ($day = 1; $day <= 7; $day++) {
            $isWeekend = ($day === 6 || $day === 7); // 6=Sat, 7=Sun in ISO8601
            
            for ($hour = 6; $hour <= 22; $hour++) {
                $basePrice = $isWeekend ? 100000 : 70000;
                
                if ($hour >= 17 && $hour <= 21) {
                    $basePrice += $isWeekend ? 50000 : 30000; // Peak hours premium
                }

                $startTime = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00';
                $endTime = str_pad($hour + 1, 2, '0', STR_PAD_LEFT) . ':00:00';

                $pricings[] = [
                    'venue_court_id' => $courtId,
                    'day_of_week' => $day,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'price_per_hour' => $basePrice,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        VenuePricing::insert($pricings);
    }
}
