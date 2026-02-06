<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Payment;
use App\Models\Venue;
use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Laporan Keuangan - Admin Panel')]
class FinancialReport extends Component
{
    public string $startDate = '';
    public string $endDate = '';
    public ?int $venueId = null;

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function exportCsv()
    {
        $payments = $this->getQuery()->get();
        
        $filename = "laporan-keuangan-{$this->startDate}-to-{$this->endDate}.csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID Pembayaran', 'Tanggal', 'Venue', 'Lapangan', 'Booking Code', 'Metode', 'Status', 'Total'];

        $callback = function() use($payments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($payments as $p) {
                fputcsv($file, [
                    $p->provider_order_id,
                    $p->paid_at?->format('Y-m-d H:i') ?? $p->created_at->format('Y-m-d H:i'),
                    $p->venue_name,
                    $p->court_name,
                    $p->booking_code,
                    $p->payment_method,
                    $p->status->value,
                    $p->amount
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getQuery()
    {
        return Payment::query()
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('venue_courts', 'bookings.venue_court_id', '=', 'venue_courts.id')
            ->join('venues', 'venue_courts.venue_id', '=', 'venues.id')
            ->select([
                'payments.*',
                'venues.name as venue_name',
                'venue_courts.name as court_name',
                'bookings.booking_code'
            ])
            ->where('payments.status', PaymentStatus::SETTLEMENT->value)
            ->whereDate('payments.created_at', '>=', $this->startDate)
            ->whereDate('payments.created_at', '<=', $this->endDate)
            ->when($this->venueId, fn($q) => $q->where('venues.id', $this->venueId))
            ->orderBy('payments.created_at', 'desc');
    }

    public function render(\App\Services\Wallet\WalletService $walletService)
    {
        $payments = $this->getQuery()->paginate(20);
        $totalRevenue = $this->getQuery()->sum('payments.amount');
        
        // Group by day for a small chart/summary
        $dailyRevenue = $this->getQuery()
            ->reorder() // Clear previous orderBy to avoid only_full_group_by error
            ->select([
                DB::raw('DATE(payments.created_at) as date'),
                DB::raw('SUM(payments.amount) as total')
            ])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('livewire.admin.reports.financial-report', [
            'payments' => $payments,
            'totalRevenue' => $totalRevenue,
            'dailyRevenue' => $dailyRevenue,
            'venues' => Venue::orderBy('name')->get(),
            'totalLiability' => $walletService->getTotalLiability(),
            'topWallets' => $walletService->getTopUsersByBalance(5),
        ]);
    }
}
