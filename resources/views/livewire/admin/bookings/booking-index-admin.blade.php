<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Daftar <span class="text-indigo-600">Pesanan</span></h1>
            <p class="text-gray-500 font-bold mt-1 tracking-tight">Monitor semua transaksi booking masuk dari pelanggan.</p>
        </div>
        
        <div class="flex items-center gap-4 bg-white p-2 rounded-[1.5rem] shadow-xl border border-gray-50">
            <div class="px-6 py-2">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Total Pesanan</p>
                <p class="text-xl font-black text-indigo-600 font-display italic">{{ \App\Models\Booking::count() }}</p>
            </div>
            <div class="w-px h-10 bg-gray-100"></div>
            <div class="px-6 py-2">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Hari Ini</p>
                <p class="text-xl font-black text-gray-900 font-display italic">{{ \App\Models\Booking::whereDate('created_at', today())->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-6 rounded-[2.5rem] shadow-2xl border border-gray-50 flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 group">
            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>
            <input wire:model.live.debounce.300ms="q" type="text" placeholder="Cari Kode Booking atau Nama Pelanggan..." 
                class="w-full pl-16 pr-6 py-5 bg-gray-50 border-none rounded-[1.5rem] text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600 transition-all">
        </div>

        <div class="w-full md:w-64">
            <select wire:model.live="statusFilter" 
                class="w-full px-6 py-5 bg-gray-50 border-none rounded-[1.5rem] text-sm font-black text-gray-900 focus:ring-2 focus:ring-indigo-600 transition-all appearance-none cursor-pointer">
                <option value="">SEMUA STATUS</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}">{{ strtoupper($status->label()) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kode & Tanggal</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Pelanggan</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Venue / Lapangan</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Waktu Main</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Total</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50/30 transition-colors group">
                        <td class="px-10 py-8">
                            <span class="text-sm font-black text-indigo-600 tracking-tighter">#{{ $booking->booking_code }}</span>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $booking->created_at->format('d M Y, H:i') }}</p>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-gray-900">{{ $booking->customer_name }}</span>
                                <span class="text-[10px] font-bold text-gray-400 truncate max-w-[150px]">{{ $booking->customer_email }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-gray-900 truncate max-w-[200px]">{{ $booking->venue->name }}</span>
                                <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-1 italic">{{ $booking->court->name }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-gray-900 tracking-tighter">{{ $booking->booking_date->format('d/m/Y') }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-center">
                            @php
                                $color = match($booking->status) {
                                    \App\Enums\BookingStatus::CONFIRMED => 'emerald',
                                    \App\Enums\BookingStatus::HOLD => 'amber',
                                    \App\Enums\BookingStatus::CANCELLED => 'rose',
                                    \App\Enums\BookingStatus::EXPIRED => 'gray',
                                    default => 'gray'
                                };
                            @endphp
                            <span class="inline-flex px-4 py-2 bg-{{ $color }}-100 text-{{ $color }}-600 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm">
                                {{ $booking->status->label() }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <span class="text-sm font-black text-gray-900 tracking-tighter">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <a href="{{ route('admin.bookings.show', $booking->id) }}" wire:navigate class="inline-flex items-center gap-2 text-[10px] font-black text-indigo-500 uppercase tracking-widest hover:text-gray-900 transition-colors">
                                DETAIL &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-10 py-32 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-200">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                </div>
                                <p class="text-gray-400 font-bold italic">Belum ada data pesanan yang sesuai filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
        <div class="px-10 py-8 border-t border-gray-50 bg-gray-50/20">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>
