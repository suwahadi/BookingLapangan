<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-text-light dark:text-text-dark tracking-tight uppercase italic">Notifikasi</h1>
            <p class="text-muted-light mt-1 text-xs">Semua pemberitahuan untuk Anda</p>
        </div>
        @if($notifications->count() > 0)
            <button wire:click="markAllAsRead" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-primary/20 transition-colors">
                <span class="material-symbols-outlined text-base">done_all</span>
                <span class="hidden sm:inline">Tandai Semua Dibaca</span>
                <span class="sm:hidden">Semua Dibaca</span>
            </button>
        @endif
    </div>

    <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] shadow-card border border-gray-100 dark:border-gray-700 overflow-hidden divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($notifications as $notification)
            @php
                $iconConfig = match($notification->type) {
                    'booking.created'   => ['icon' => 'add_circle',      'bg' => 'bg-blue-100 dark:bg-blue-900/30',    'text' => 'text-blue-600 dark:text-blue-400'],
                    'booking.paid'      => ['icon' => 'payments',        'bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                    'booking.confirmed' => ['icon' => 'check_circle',    'bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                    'booking.expired'   => ['icon' => 'timer_off',       'bg' => 'bg-gray-100 dark:bg-gray-800',       'text' => 'text-gray-500 dark:text-gray-400'],
                    'booking.cancelled' => ['icon' => 'cancel',          'bg' => 'bg-rose-100 dark:bg-rose-900/30',    'text' => 'text-rose-600 dark:text-rose-400'],
                    'refund.processed'  => ['icon' => 'sync',            'bg' => 'bg-amber-100 dark:bg-amber-900/30',  'text' => 'text-amber-600 dark:text-amber-400'],
                    'refund.success'    => ['icon' => 'account_balance_wallet', 'bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                    'refund.rejected'   => ['icon' => 'block',           'bg' => 'bg-rose-100 dark:bg-rose-900/30',    'text' => 'text-rose-600 dark:text-rose-400'],
                    default             => ['icon' => 'notifications',   'bg' => 'bg-primary/10',                      'text' => 'text-primary'],
                };
            @endphp
            <a href="{{ $notification->action_url ?? '#' }}" wire:click="markAsRead('{{ $notification->id }}')" 
               class="block p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors {{ !$notification->is_read ? 'bg-primary/5 dark:bg-primary/10' : '' }}">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl {{ $iconConfig['bg'] }} flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-xl {{ $iconConfig['text'] }}">{{ $iconConfig['icon'] }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-black text-text-light dark:text-text-dark uppercase tracking-tight">{{ $notification->title }}</p>
                            @if(!$notification->is_read)
                                <span class="w-2 h-2 rounded-full bg-primary shrink-0 animate-pulse"></span>
                            @endif
                        </div>
                        @if($notification->body)
                            <p class="text-sm text-muted-light mt-1 leading-relaxed">{{ $notification->body }}</p>
                        @endif
                        <p class="text-[10px] font-bold text-muted-light tracking-widest mt-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[10px]">schedule</span>
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-20 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">notifications_off</span>
                </div>
                <p class="text-muted-light font-bold text-lg">Belum ada notifikasi</p>
                <p class="text-muted-light text-sm mt-2 opacity-60">Notifikasi akan muncul saat ada aktivitas booking Anda</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
