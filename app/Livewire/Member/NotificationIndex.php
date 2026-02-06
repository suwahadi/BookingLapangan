<?php

namespace App\Livewire\Member;

use App\Models\Notification;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class NotificationIndex extends Component
{
    use WithPagination;

    public function markAsRead(string $id): void
    {
        $notification = Notification::where('user_id', auth()->id())->find($id);
        $notification?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function render()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.member.notification-index', [
            'notifications' => $notifications,
        ]);
    }
}
