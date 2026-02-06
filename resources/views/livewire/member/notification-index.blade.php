<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase italic">Notifikasi</h1>
        <button wire:click="markAllAsRead" class="text-xs font-black uppercase tracking-widest text-indigo-600 hover:underline">
            Tandai Semua Dibaca
        </button>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden divide-y divide-gray-50">
        @forelse($notifications as $notification)
            <a href="{{ $notification->action_url ?? '#' }}" wire:click="markAsRead('{{ $notification->id }}')" 
               class="block p-6 hover:bg-gray-50 transition-colors {{ !$notification->is_read ? 'bg-indigo-50/50' : '' }}">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl {{ !$notification->is_read ? 'bg-indigo-600' : 'bg-gray-200' }} flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 {{ !$notification->is_read ? 'text-white' : 'text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-gray-900 uppercase">{{ $notification->title }}</p>
                        @if($notification->body)
                            <p class="text-sm text-gray-600 mt-1">{{ $notification->body }}</p>
                        @endif
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                    @if(!$notification->is_read)
                        <span class="w-2 h-2 rounded-full bg-indigo-600 shrink-0"></span>
                    @endif
                </div>
            </a>
        @empty
            <div class="p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <p class="text-gray-500 font-bold">Belum ada notifikasi</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
