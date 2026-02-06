<div class="space-y-10" wire:poll.60s="refreshStats">
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Statistik <span class="text-indigo-600">Terbaru</span></h1>
            <p class="text-gray-500 font-bold mt-1 tracking-tight">Selamat datang kembali, {{ Auth::user()->name }}! Pantau performa bisnis hari ini.</p>
        </div>
        <button wire:click="refreshStats" wire:loading.attr="disabled"
            class="bg-white border-2 border-gray-900 group relative px-6 py-3 rounded-2xl font-black text-sm tracking-widest hover:bg-gray-900 hover:text-white transition-all transform active:scale-95 shadow-xl shadow-gray-200">
            <span class="flex items-center gap-2">
                <svg wire:loading.class="animate-spin" class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                REFRESH DATA
            </span>
        </button>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stat Card 1 -->
        <div class="bg-indigo-600 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" /></svg>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center mb-6 backdrop-blur-md">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-70 mb-1">Booking Hari Ini</p>
                <h3 class="text-4xl font-display font-black leading-none">{{ $stats['bookings_today'] }}</h3>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-gray-300 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-700 text-indigo-500">
                <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-indigo-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">Confirmed</p>
                <h3 class="text-4xl font-display font-black leading-none">{{ $stats['confirmed_today'] }}</h3>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-white rounded-[2.5rem] p-8 text-gray-900 shadow-xl border border-gray-100 relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">Revenue Hari Ini</p>
                <h3 class="text-3xl font-display font-black leading-none tracking-tight">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="bg-white rounded-[2.5rem] p-8 text-gray-900 shadow-xl border border-gray-100 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">Tugas Pending</p>
                <div class="flex items-center gap-4 mt-2">
                    <div>
                        <span class="text-2xl font-black font-display text-rose-600">{{ $stats['pending_refund'] + $stats['pending_reschedule'] }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Issues</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Summary & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Revenue Card -->
        <div class="lg:col-span-1 bg-white p-10 rounded-[3rem] shadow-2xl border border-gray-50 flex flex-col items-center text-center">
            <div class="w-24 h-24 bg-indigo-50 rounded-[2rem] flex items-center justify-center mb-8">
                <svg class="w-12 h-12 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            </div>
            <h4 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight mb-2 italic">Performa Bulan Ini</h4>
            <p class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-8">Total Pendapatan Terkumpul</p>
            
            <div class="text-4xl font-display font-black text-gray-900 leading-none">
                Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}
            </div>
            
            <div class="mt-8 w-full bg-gray-50 rounded-2xl p-6 flex justify-between items-center border border-gray-100">
                <div class="text-left">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Revenue Minggu Ini</p>
                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($stats['revenue_week'], 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                </div>
            </div>
        </div>

        <!-- Recent Bookings Table -->
        <div class="lg:col-span-2 bg-white rounded-[3rem] shadow-2xl border border-gray-50 overflow-hidden flex flex-col">
            <div class="p-10 flex items-center justify-between border-b border-gray-50 bg-white/50 backdrop-blur-md">
                <div>
                    <h4 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight italic">Booking <span class="text-indigo-600">Terbaru</span></h4>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Status dan aktifitas user seketika</p>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs font-black text-indigo-600 hover:text-black transition-colors uppercase tracking-widest border-b-2 border-indigo-100 hover:border-black py-1">Semua Data &rarr;</a>
            </div>

            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">User / Kode</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Venue & Lapangan</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Total</th>
                            <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentBookings as $booking)
                        <tr onclick="window.location='{{ route('admin.bookings.show', $booking->id) }}'" class="hover:bg-gray-50/50 transition-colors group cursor-pointer">
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-gray-900 lowercase">{{ $booking->user->name }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest group-hover:text-indigo-600 transition-colors ">{{ $booking->booking_code }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-bold text-gray-900 lowercase">{{ $booking->venue->name }}</p>
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $booking->court->name }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-gray-900">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 {{ $booking->status->color() }} shadow-sm">
                                    {{ $booking->status->label() }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="bg-gray-50 rounded-3xl p-10 inline-block border-2 border-dashed border-gray-200">
                                    <p class="text-gray-400 font-bold text-sm">Belum ada aktifitas booking terbaru.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
