<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Booking;
use App\Models\Payment;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Dashboard Admin - BookingLapangan')]
class AdminDashboard extends Component
{
    public array $stats = [];
    public $recentBookings;

    public function mount()
    {
        $this->refreshStats();
    }

    public function refreshStats()
    {
        $today = CarbonImmutable::now();
        $startOfWeek = $today->startOfWeek();
        $startOfMonth = $today->startOfMonth();

        $this->stats = [
            'bookings_today' => Booking::whereDate('created_at', $today->toDateString())->count(),
            'confirmed_today' => Booking::where('status', BookingStatus::CONFIRMED)
                ->whereDate('created_at', $today->toDateString())->count(),
            'revenue_today' => Payment::where('status', PaymentStatus::SETTLEMENT)
                ->whereDate('paid_at', $today->toDateString())->sum('amount'),
            'revenue_week' => Payment::where('status', PaymentStatus::SETTLEMENT)
                ->whereDate('paid_at', '>=', $startOfWeek->toDateString())->sum('amount'),
            'revenue_month' => Payment::where('status', PaymentStatus::SETTLEMENT)
                ->whereDate('paid_at', '>=', $startOfMonth->toDateString())->sum('amount'),
            'pending_reschedule' => DB::table('reschedule_requests')->where('status', 'PENDING')->count(),
            'pending_refund' => DB::table('refund_requests')->where('status', 'PENDING')->count(),
            'upcoming_24h' => Booking::where('status', BookingStatus::CONFIRMED)
                ->where('booking_date', $today->toDateString())
                ->whereTime('start_time', '>', $today->toTimeString())
                ->count(),
        ];

        $this->recentBookings = Booking::with(['user', 'venue', 'court'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.admin-dashboard');
    }
}
