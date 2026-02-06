<?php

use App\Services\Venue\VenueSearchService;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $service = app(VenueSearchService::class);
    
    echo "Testing Search...\n";
    
    // Test 1: Search without filters (should return all active venues)
    $results = $service->search([]);
    echo "Total venues (no filter): " . $results->total() . "\n";
    foreach ($results as $v) {
        echo " - " . $v->name . " (Active courts: " . $v->active_courts_count . ")\n";
    }

    // Test 2: Search with sport 'Badminton'
    echo "\nTesting Search Sport 'Badminton'...\n";
    $results = $service->search(['sport_type' => 'Badminton']);
    echo "Total venues (Badminton): " . $results->total() . "\n";

    // Test 3: Search with Date & Time (Availability)
    // Assuming we have no bookings, it should be available.
    echo "\nTesting Search Availability (No bookings yet)...\n";
    $results = $service->search([
        'date' => now()->addDay()->format('Y-m-d'),
        'start_time' => '10:00',
        'end_time' => '11:00',
    ]);
    echo "Total venues (Available tomorrow 10-11): " . $results->total() . "\n";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
