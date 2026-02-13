<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Venue;
use App\Models\User;
use App\Models\VenueReview;
use App\Models\Booking;
use App\Models\VenueCourt;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Services\VenueRatingAggregator;

class VenueReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $venues = Venue::all();
        
        // Ensure there are some users
        if (User::count() < 5) {
            User::factory(10)->create();
        }
        $users = User::all();

        foreach ($venues as $venue) {
            $courts = $venue->courts;
            if ($courts->isEmpty()) continue;

            $this->command->info("Generating reviews for venue: {$venue->name}");

            for ($i = 0; $i < 5; $i++) {
                $user = $users->random();
                $court = $courts->random();

                // Generate dummy completed booking
                // We create a booking just to satisfy the data integrity if needed, 
                // but since we are seeding reviews directly, we might not strictly need the booking 
                // unless we enforce foreign keys heavily. The migration has booking_id nullable?
                // Migration: $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
                // So we can leave booking_id null for dummy reviews OR create dummy bookings.
                // Let's create dummy booking to be safe and realistic.

                $bookingDate = $faker->dateTimeBetween('-1 month', 'yesterday');
                
                $booking = Booking::create([
                    'booking_code' => 'BOOK-' . strtoupper(uniqid()),
                    'user_id' => $user->id,
                    'venue_id' => $venue->id,
                    'venue_court_id' => $court->id,
                    'booking_date' => $bookingDate,
                    'start_time' => '10:00:00',
                    'end_time' => '12:00:00',
                    'status' => \App\Enums\BookingStatus::CONFIRMED,
                    'total_amount' => 100000,
                    'paid_amount' => 100000,
                    'customer_name' => $user->name,
                    'customer_email' => $user->email,
                    'customer_phone' => $user->phone ?? '08123456789',
                ]);

                // Create Review
                $rating = $faker->randomElement([3, 4, 4, 5, 5, 5]); // Weighted realistic distribution

                // Create Review
                VenueReview::create([
                    'user_id' => $user->id,
                    'venue_id' => $venue->id,
                    'booking_id' => $booking->id,
                    'venue_court_id' => $court->id,
                    'rating' => $rating,
                    'comment' => $this->generateRealComment($faker, $rating),
                    'is_approved' => true,
                    'approved_at' => now(),
                    'created_at' => $faker->dateTimeBetween($bookingDate, 'now'),
                ]);
            }
            
            // Aggregation
            app(VenueRatingAggregator::class)->recalculate($venue->id);
        }
    }

    private function generateRealComment($faker, $rating)
    {
        if ($rating == 5) {
            $templates = [
                "Tempatnya bersih dan nyaman, pelayanannya ramah. Mantap!",
                "Lapangan bagus, parkiran luas. Bakal main lagi disini.",
                "Sangat recommended! Lapangan terawat dengan baik.",
                "Fasilitas lengkap, ada musholla dan kantin juga. Keren.",
                "Enak mainnya, tidak licin dan pencahayaan terang.",
                "Puas banget main disini, staff nya helpful.",
                "Venue olahraga terbaik di daerah ini!",
            ];
        } elseif ($rating == 4) {
            $templates = [
                "Lumayan bagus, cuma AC nya kurang dingin dikit.",
                "Oke sih, harga terjangkau. Tapi parkiran agak sempit.",
                "Lapangan standar, enak buat latihan rutin.",
                "Not bad, toiletnya perlu dijaga kebersihannya.",
                "Cukup nyaman, tapi bookingnya harus jauh hari biar dapet slot.",
                "Pelayanan oke, lapangan juga masih bagus.",
            ];
        } else {
            // Rating 3
            $templates = [
                "Biasa aja, karpetnya ada yang ngelupas dikit.",
                "Toiletnya kotor, tolong diperbaiki.",
                "Agak panas ya tempatnya, sirkulasi udara kurang.",
                "Lampunya ada yang mati satu, jadi agak gelap di pojok.",
                "Harganya lumayan, tapi fasilitasnya perlu upgrade.",
                "Standar lapangan futsal biasa, nothing special.",
            ];
        }

        return $faker->randomElement($templates) . ' ' . $faker->sentence(2);
    }
}
