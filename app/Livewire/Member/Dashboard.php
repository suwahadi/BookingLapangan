<?php

namespace App\Livewire\Member;

use App\Models\Booking;
use App\Models\Notification;
use App\Services\Wallet\WalletService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $wallet;
    public $unreadNotifications = 0;

    public function mount(WalletService $walletService): void
    {
        $this->wallet = $walletService->getWallet(auth()->user());
        $this->unreadNotifications = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    public function render()
    {
        $upcomingBookings = Booking::where('user_id', auth()->id())
            ->whereIn('status', ['HOLD', 'CONFIRMED'])
            ->where('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date')
            ->limit(5)
            ->get();

        $recentBookings = Booking::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('livewire.member.dashboard', [
            'upcomingBookings' => $upcomingBookings,
            'recentBookings' => $recentBookings,
        ]);
    }
}
