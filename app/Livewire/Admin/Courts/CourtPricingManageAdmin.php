<?php

namespace App\Livewire\Admin\Courts;

use App\Models\VenueCourt;
use App\Models\VenuePricing;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Atur Harga Lapangan - Admin Panel')]
class CourtPricingManageAdmin extends Component
{
    public VenueCourt $court;

    public int $dayOfWeek = 1; // 1=Senin, 7=Minggu
    public array $rows = [];

    // Dictionary for tab display
    public array $days = [
        1 => 'SENIN',
        2 => 'SELASA',
        3 => 'RABU',
        4 => 'KAMIS',
        5 => 'JUMAT',
        6 => 'SABTU',
        7 => 'MINGGU',
    ];

    public function mount(VenueCourt $court)
    {
        $this->court = $court->load('venue');
        $this->loadPricing();
    }

    public function selectDay(int $day)
    {
        $this->dayOfWeek = $day;
        $this->loadPricing();
    }

    public function loadPricing()
    {
        $items = VenuePricing::where('venue_court_id', $this->court->id)
            ->where('day_of_week', $this->dayOfWeek)
            ->orderBy('start_time')
            ->get();

        $this->rows = $items->map(fn($p) => [
            'start_time' => substr($p->start_time, 0, 5),
            'end_time' => substr($p->end_time, 0, 5),
            'price_per_hour' => (int) $p->price_per_hour,
        ])->all();

        if (empty($this->rows)) {
            $this->rows = [
                ['start_time' => '08:00', 'end_time' => '17:00', 'price_per_hour' => 100000],
                ['start_time' => '17:00', 'end_time' => '23:00', 'price_per_hour' => 150000],
            ];
        }
    }

    public function addRow()
    {
        $lastRow = end($this->rows);
        $startTime = $lastRow ? $lastRow['end_time'] : '08:00';
        $endTime = $lastRow ? date('H:i', strtotime($startTime . ' +1 hour')) : '09:00';

        $this->rows[] = [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price_per_hour' => $lastRow ? $lastRow['price_per_hour'] : 100000,
        ];
    }

    public function removeRow(int $index)
    {
        unset($this->rows[$index]);
        $this->rows = array_values($this->rows);
    }

    public function save()
    {
        $this->validate([
            'rows' => 'required|array|min:1',
            'rows.*.start_time' => 'required',
            'rows.*.end_time' => 'required',
            'rows.*.price_per_hour' => 'required|integer|min:0',
        ]);

        // Basic overlap check or sorting could be done here, but DB transaction is key
        DB::transaction(function () {
            VenuePricing::where('venue_court_id', $this->court->id)
                ->where('day_of_week', $this->dayOfWeek)
                ->delete();

            foreach ($this->rows as $row) {
                VenuePricing::create([
                    'venue_court_id' => $this->court->id,
                    'day_of_week' => $this->dayOfWeek,
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time'],
                    'price_per_hour' => $row['price_per_hour'],
                ]);
            }
        });

        $this->dispatch('toast', message: 'Harga untuk hari ' . $this->days[$this->dayOfWeek] . ' berhasil disimpan!', type: 'success');
    }

    /**
     * Copy current rates to selected days
     */
    public function copyToDays(array $targetDays)
    {
        DB::transaction(function () use ($targetDays) {
            foreach ($targetDays as $day) {
                VenuePricing::where('venue_court_id', $this->court->id)
                    ->where('day_of_week', $day)
                    ->delete();

                foreach ($this->rows as $row) {
                    VenuePricing::create([
                        'venue_court_id' => $this->court->id,
                        'day_of_week' => $day,
                        'start_time' => $row['start_time'],
                        'end_time' => $row['end_time'],
                        'price_per_hour' => $row['price_per_hour'],
                    ]);
                }
            }
        });

        $this->dispatch('toast', message: 'Harga berhasil disalin ke hari terpilih!', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.courts.court-pricing-manage-admin');
    }
}
