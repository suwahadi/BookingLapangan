<div class="max-w-5xl mx-auto py-12 px-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-text-light dark:text-text-dark tracking-tight uppercase italic">Riwayat <span class="text-primary">Booking</span></h1>
            <p class="text-muted-light font-bold mt-1 uppercase text-xs tracking-widest">Semua transaksi booking Anda</p>
        </div>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-primary-dark transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-0.5">
            <span class="material-symbols-outlined text-sm">add</span>
            Booking Baru
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] shadow-card border border-gray-100 dark:border-gray-700 p-6 mb-8">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-light group-focus-within:text-primary transition-colors">
                    <span class="material-symbols-outlined">search</span>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kode booking..." 
                    class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary placeholder-muted-light transition-all">
            </div>
            <div class="relative min-w-[200px]">
                 <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-light">
                    <span class="material-symbols-outlined">filter_list</span>
                </span>
                <select wire:model.live="status" class="w-full pl-12 pr-10 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary appearance-none cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="HOLD">Menunggu Bayar</option>
                    <option value="CONFIRMED">Lunas/DP</option>
                    <option value="CANCELLED">Batal</option>
                    <option value="EXPIRED">Kadaluarsa</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Booking List -->
    <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] shadow-card border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($bookings as $booking)
                <a href="{{ route('bookings.show', $booking) }}" class="block p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        <!-- Date Badge -->
                        <div class="w-20 h-20 rounded-2xl bg-primary/5 dark:bg-gray-800 border border-primary/10 flex flex-col items-center justify-center shrink-0 group-hover:bg-primary group-hover:border-primary transition-colors duration-300">
                            <span class="text-2xl font-black text-primary group-hover:text-white transition-colors">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d') }}</span>
                            <span class="text-[10px] font-bold uppercase text-muted-light group-hover:text-white/80 transition-colors">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M Y') }}</span>
                        </div>

                        <!-- Booking Info -->
                        <div class="flex-1 min-w-0 space-y-2">
                            <div class="flex flex-wrap items-center gap-3">
                                <p class="text-sm font-black text-text-light dark:text-text-dark uppercase tracking-wide group-hover:text-primary transition-colors flex items-center gap-2">
                                     <span class="material-symbols-outlined text-base text-muted-light">receipt_long</span>
                                    {{ $booking->booking_code }}
                                </p>
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border shadow-sm
                                    {{ $booking->status->value === 'CONFIRMED' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : '' }}
                                    {{ $booking->status->value === 'HOLD' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800 animate-pulse' : '' }}
                                    {{ $booking->status->value === 'CANCELLED' ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800' : '' }}
                                    {{ $booking->status->value === 'EXPIRED' ? 'bg-gray-100 dark:bg-gray-800 text-muted-light border-gray-200 dark:border-gray-700' : '' }}">
                                    {{ $booking->status->value }}
                                </span>
                            </div>
                            
                            <div>
                                <p class="text-sm font-bold text-text-light dark:text-text-dark">{{ $booking->venue->name ?? '-' }}</p>
                                <div class="flex items-center gap-1 text-xs text-muted-light mt-0.5">
                                    <span class="material-symbols-outlined text-[14px]">{{ \App\Models\Venue::sportIcon($booking->court->sport ?? '') }}</span>
                                    {{ $booking->court->name ?? '-' }}
                                </div>
                                <div class="flex flex-wrap items-center gap-1 mt-1">
                                    @foreach($booking->grouped_slots as $slot)
                                        <span class="inline-flex items-center px-2 py-0.5 bg-primary/10 text-primary rounded text-[10px] font-bold font-mono">
                                            {{ $slot['start'] }} - {{ $slot['end'] }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Amount -->
                        <div class="text-right flex flex-row md:flex-col justify-between items-center md:items-end gap-x-4">
                            <p class="text-lg md:text-xl font-black text-primary font-display italic">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                            <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest">{{ $booking->created_at->diffForHumans() }}</p>
                        </div>

                        <!-- Arrow -->
                        <div class="w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-muted-light group-hover:bg-primary group-hover:text-white transition-all shrink-0 hidden md:flex">
                             <span class="material-symbols-outlined">arrow_forward</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-20 text-center">
                    <div class="w-24 h-24 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">calendar_today</span>
                    </div>
                    <p class="text-muted-light font-bold text-lg">Belum ada booking</p>
                    <p class="text-muted-light text-sm mt-2 opacity-60">Mulai cari lapangan untuk booking Anda</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mt-8 px-8 py-4 bg-primary text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-primary-dark transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1">
                        Cari Lapangan
                    </a>
                </div>
            @endforelse
        </div>

        @if($bookings->hasPages())
            <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
