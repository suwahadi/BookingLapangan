<?php

namespace App\Livewire\Courts;

use App\Models\Venue;
use App\Models\VenueCourt;
use App\Services\Booking\AvailabilityService;
use App\Services\Booking\BookingService;
use App\Services\Booking\Exceptions\InvalidBookingTimeException;
use App\Services\Booking\Exceptions\SlotNotAvailableException;
use App\Services\Booking\PricingService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CourtScheduleCinema extends Component
{
    public VenueCourt $venueCourt;

    public string $date; // Y-m-d
    public string $openTime = '06:00';
    public string $closeTime = '23:00';

    /** @var array<int, array{start:string,end:string,is_available:bool}> */
    public array $timeSlots = [];

    /** @var array<string,int> slot_key => amount */
    public array $slotAmounts = [];

    /** @var array<int, int> index slot yang dipilih (berurutan) */
    public array $selectedIndexes = [];

    public ?string $errorMessage = null;

    public function mount(Venue $venue, VenueCourt $venueCourt): void
    {
        $this->venueCourt = $venueCourt->load('venue.policy');
        $this->date = CarbonImmutable::now()->format('Y-m-d');

        $this->reloadData();
    }

    public function updatedDate(): void
    {
        $this->selectedIndexes = [];
        $this->errorMessage = null; // Clear error on date change
        $this->reloadData();
    }

    public function reloadData(): void
    {
        $this->errorMessage = null;

        // 1. Get Operating Hours for the SELECTED date
        $selectedDate = CarbonImmutable::createFromFormat('Y-m-d', $this->date)->startOfDay();
        $dayOfWeek = $selectedDate->isoWeekday(); // 1-7
        
        $operatingHour = $this->venueCourt->venue->operatingHours()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if ($operatingHour && !$operatingHour->is_closed) {
            // Use venue hours if available
            $this->openTime = substr($operatingHour->open_time, 0, 5);
            
            $rawClose = substr($operatingHour->close_time, 0, 5);
            // Treat 00:00 or 23:59 as 24:00 (End of Day) to allow fully midnight slots
            if ($rawClose === '00:00' || $rawClose === '23:59') {
                $this->closeTime = '24:00';
            } else {
                $this->closeTime = $rawClose;
            }
        } else {
            // Default fallback if no operating hours found or if closed (though usually we'd show closed message)
            // For now, let's assume default 06:00-23:00 OR full 24h if desired.
            // But per request "24 jam", let's try 00:00 - 24:00 (next day 00:00)
            // However, AvailabilityService usually expects start < end.
            // If closed, maybe empty array?
            if ($operatingHour && $operatingHour->is_closed) {
                $this->timeSlots = [];
                $this->slotAmounts = [];
                return;
            }
            // Fallback: 00:00 to 24:00
            $this->openTime = '00:00'; 
            $this->closeTime = '24:00'; // AvailabilityService handles this: Carbon parses 24:00 as next day 00:00.
        }

        $availability = app(AvailabilityService::class);
        // Ensure closeTime > openTime for 24h logic (e.g. 00:00 to 00:00 next day)
        // If closeTime is '00:00' and open is '00:00', it might be empty.
        // If closeTime is smaller than openTime (spanning midnight), logic might need adjustment.
        // For now assume standard day range.

        $rawSlots = $availability->getDailySlots(
            venueCourtId: $this->venueCourt->id,
            dateYmd: $this->date,
            openTimeHi: $this->openTime,
            closeTimeHi: $this->closeTime
        );

        // 2. Fetch Pricing
        try {
            $pricing = app(PricingService::class);
            $this->slotAmounts = $pricing->getSlotAmounts(
                venueCourtId: $this->venueCourt->id,
                dateYmd: $this->date,
                openTimeHi: $this->openTime,
                closeTimeHi: $this->closeTime
            );
        } catch (\Throwable $e) {
            $this->slotAmounts = [];
        }

        // 3. Filter Slots: Only keep slots that have a price AND are within operating hours (already handled by getDailySlots range)
        // AND not "empty" (no price).
        // Also handle past time logic here.

        $filteredSlots = [];
        $now = CarbonImmutable::now();
        $today = $now->startOfDay();
        $isPastDate = $selectedDate->lessThan($today);
        $isToday = $selectedDate->equalTo($today);
        $currentTimeHi = $now->format('H:i');

        foreach ($rawSlots as $slot) {
            $key = $slot['start'] . '|' . $slot['end'];
            $amount = $this->slotAmounts[$key] ?? 0;

            // SKIP empty/unpriced slots (Visual Masonry / No Gaps requirement)
            if ($amount <= 0) {
                continue;
            }

            // Determine status
            $slot['status'] = $slot['is_available'] ? 'available' : 'booked';

            // Check Past Time -> SKIP (So they don't appear in grid)
            if ($isPastDate) {
                 continue;
            }
            if ($isToday && $slot['start'] < $currentTimeHi) {
                 continue;
            }

            $filteredSlots[] = $slot;
        }

        $this->timeSlots = $filteredSlots;

        // Re-index selectedIndexes? No, selectedIndexes stores INDICES of the timeSlots array.
        // Since we replaced $this->timeSlots with a new indexed array (0, 1, 2...), the old selection indices are invalid.
        // We should clear selection on reload (handled by updatedDate).
        // If this is just a reload without date change (e.g. polling), we might lose selection.
        // But for now, simple reload clears selection or risks wrong index mapping if not cleared.
        // In `updatedDate` we clear `$this->selectedIndexes = []`, so it is safe.
    }

    /**
     * Klik slot: Toggle selection (bisa lompat/non-contiguous).
     */
    public function toggleSelect(int $index): void
    {
        $this->errorMessage = null;

        if (!isset($this->timeSlots[$index])) {
            return;
        }

        if (!$this->timeSlots[$index]['is_available']) {
            return;
        }

        if (in_array($index, $this->selectedIndexes)) {
            // Unselect
            $this->selectedIndexes = array_values(array_diff($this->selectedIndexes, [$index]));
        } else {
            // Select
            $this->selectedIndexes[] = $index;
            sort($this->selectedIndexes);
        }
    }

    public function getSelectedTotalProperty(): int
    {
        $total = 0;

        foreach ($this->selectedIndexes as $i) {
            // Re-validate index existence just in case
            if (!isset($this->timeSlots[$i])) continue;

            $key = $this->timeSlots[$i]['start'] . '|' . $this->timeSlots[$i]['end'];
            $total += (int) ($this->slotAmounts[$key] ?? 0);
        }

        return $total;
    }

    public function confirmSelection()
    {
        $this->errorMessage = null;

        if (empty($this->selectedIndexes)) {
            $this->errorMessage = 'Silakan pilih jam terlebih dahulu.';
            return;
        }

        // Build slots array for cart
        $slots = [];
        foreach ($this->selectedIndexes as $idx) {
            // Find slot by index in the current filtered/mapped list
            // Note: Since we might filter timeSlots, we need to be careful with indexes.
            // However, assuming selectedIndexes stores keys of $this->timeSlots array:
            if (!isset($this->timeSlots[$idx])) continue;
            
            $slot = $this->timeSlots[$idx];
            $key = $slot['start'] . '|' . $slot['end'];
            $amount = $this->slotAmounts[$key] ?? 0;
            
            $slots[] = [
                'date' => $this->date,
                'start' => $slot['start'],
                'end' => $slot['end'],
                'amount' => $amount,
            ];
        }

        // Store cart in session
        session()->put('booking_cart', [
            'venue_court_id' => $this->venueCourt->id,
            'date' => $this->date,
            'slots' => $slots, // Order might matter? Usually handled by date/time sort in checkout.
            'total_amount' => $this->selectedTotal,
        ]);

        // Redirect to review page
        $this->redirect(route('checkout.review'));
    }

    public function render()
    {
        $startDate = CarbonImmutable::now();
        $upcomingDates = [];
        for ($i = 0; $i < 7; $i++) {
            $d = $startDate->addDays($i);
            $upcomingDates[] = [
                'value' => $d->format('Y-m-d'),
                'label_day' => $d->translatedFormat('D'),
                'label_date' => $d->translatedFormat('j M'),
            ];
        }

        return view('livewire.courts.court-schedule-cinema', [
            'upcomingDates' => $upcomingDates
        ]);
    }
}
