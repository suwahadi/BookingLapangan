<div class="max-w-5xl mx-auto py-12 px-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight uppercase italic">Riwayat <span class="text-indigo-600">Booking</span></h1>
            <p class="text-gray-500 font-bold mt-1 uppercase text-xs tracking-widest">Semua transaksi booking Anda</p>
        </div>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Booking Baru
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 mb-8">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kode booking..." 
                    class="w-full pl-12 pr-4 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
            </div>
            <select wire:model.live="status" class="px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                <option value="">Semua Status</option>
                <option value="HOLD">Hold</option>
                <option value="CONFIRMED">Confirmed</option>
                <option value="CANCELLED">Cancelled</option>
                <option value="EXPIRED">Expired</option>
            </select>
        </div>
    </div>

    <!-- Booking List -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-50">
            @forelse($bookings as $booking)
                <a href="{{ route('bookings.show', $booking) }}" class="block p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <!-- Date Badge -->
                        <div class="w-16 h-16 rounded-2xl bg-indigo-100 flex flex-col items-center justify-center shrink-0">
                            <span class="text-2xl font-black text-indigo-600">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d') }}</span>
                            <span class="text-[10px] font-bold uppercase text-indigo-400">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M Y') }}</span>
                        </div>

                        <!-- Booking Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <p class="text-sm font-black text-gray-900 uppercase">{{ $booking->booking_code }}</p>
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest 
                                    {{ $booking->status->value === 'CONFIRMED' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $booking->status->value === 'HOLD' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $booking->status->value === 'CANCELLED' ? 'bg-rose-100 text-rose-700' : '' }}
                                    {{ $booking->status->value === 'EXPIRED' ? 'bg-gray-100 text-gray-600' : '' }}">
                                    {{ $booking->status->value }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $booking->venue->name ?? '-' }} - {{ $booking->court->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $booking->start_time }} - {{ $booking->end_time }}</p>
                        </div>

                        <!-- Amount -->
                        <div class="text-right">
                            <p class="text-lg font-black text-gray-900">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $booking->created_at->diffForHumans() }}</p>
                        </div>

                        <!-- Arrow -->
                        <svg class="w-5 h-5 text-gray-400 shrink-0 hidden md:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            @empty
                <div class="p-16 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500 font-bold text-lg">Belum ada booking</p>
                    <p class="text-gray-400 text-sm mt-2">Mulai cari lapangan untuk booking pertama Anda</p>
                    <a href="{{ route('home') }}" class="inline-block mt-6 px-8 py-4 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-colors">
                        Cari Lapangan
                    </a>
                </div>
            @endforelse
        </div>

        @if($bookings->hasPages())
            <div class="p-6 border-t border-gray-50">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
