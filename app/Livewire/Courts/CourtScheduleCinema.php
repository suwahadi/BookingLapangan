<?php

namespace App\Livewire\Courts;

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

    public function mount(VenueCourt $venueCourt): void
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

        $availability = app(AvailabilityService::class);
        $this->timeSlots = $availability->getDailySlots(
            venueCourtId: $this->venueCourt->id,
            dateYmd: $this->date,
            openTimeHi: $this->openTime,
            closeTimeHi: $this->closeTime
        );

        // Disable past dates and past times
        $now = CarbonImmutable::now();
        $selectedDate = CarbonImmutable::createFromFormat('Y-m-d', $this->date)->startOfDay();
        $today = $now->startOfDay();
        
        $isPastDate = $selectedDate->lessThan($today);
        $isToday = $selectedDate->equalTo($today);
        $currentTimeHi = $now->format('H:i');

        foreach ($this->timeSlots as &$slot) {
            if ($isPastDate) {
                $slot['is_available'] = false;
                continue;
            }

            if ($isToday) {
                // If the slot starts before current time, disable it
                if ($slot['start'] < $currentTimeHi) {
                    $slot['is_available'] = false;
                }
            }
        }
        unset($slot); // clear reference

        // Harga per slot (untuk display)
        try {
            $pricing = app(PricingService::class);
            $this->slotAmounts = $pricing->getSlotAmounts(
                venueCourtId: $this->venueCourt->id,
                dateYmd: $this->date,
                openTimeHi: $this->openTime,
                closeTimeHi: $this->closeTime
            );
        } catch (\Throwable $e) {
            // Untuk MVP: jika pricing bermasalah, tetap render tanpa harga
            $this->slotAmounts = [];
        }
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

    public function confirmSelection(): void
    {
        $this->errorMessage = null;

        if (!Auth::check()) {
            $this->redirectRoute('login', navigate: true);
            return;
        }

        if (empty($this->selectedIndexes)) {
            $this->errorMessage = 'Silakan pilih jam terlebih dahulu.';
            return;
        }

        // Group indexes into contiguous blocks
        sort($this->selectedIndexes);
        $blocks = [];
        $currentBlock = [];
        $prevIndex = null;

        foreach ($this->selectedIndexes as $index) {
            if ($prevIndex === null || $index === $prevIndex + 1) {
                $currentBlock[] = $index;
            } else {
                $blocks[] = $currentBlock;
                $currentBlock = [$index];
            }
            $prevIndex = $index;
        }
        if (!empty($currentBlock)) {
            $blocks[] = $currentBlock;
        }

        $bookingService = app(BookingService::class);
        $createdBookings = [];

        try {
            foreach ($blocks as $block) {
                $minIndex = min($block);
                $maxIndex = max($block);

                $start = $this->timeSlots[$minIndex]['start'];
                $end = $this->timeSlots[$maxIndex]['end'];

                $booking = $bookingService->createHold(
                    userId: (int) Auth::id(),
                    venueCourtId: (int) $this->venueCourt->id,
                    dateYmd: $this->date,
                    startTimeHi: $start,
                    endTimeHi: $end
                );

                $createdBookings[] = $booking;
            }

            if (count($createdBookings) === 1) {
                $this->redirectRoute('bookings.show', ['booking' => $createdBookings[0]->id], navigate: true);
            } else {
                // If multiple bookings, redirect to history list
                session()->flash('success', count($createdBookings) . ' Booking berhasil dibuat. Silakan lakukan pembayaran.');
                $this->redirectRoute('member.bookings', navigate: true);
            }

        } catch (SlotNotAvailableException $e) {
            $this->errorMessage = "Slot tidak tersedia: " . $e->getMessage();
            // Refresh data to show latest availability
            $this->reloadData();
        } catch (InvalidBookingTimeException $e) {
            $this->errorMessage = $e->getMessage();
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }

    public function render()
    {
        $startDate = CarbonImmutable::now();
        $upcomingDates = [];
        for ($i = 0; $i < 14; $i++) {
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
