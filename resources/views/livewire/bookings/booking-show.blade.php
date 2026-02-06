<div class="space-y-10 pb-20">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-indigo-400 font-black text-[10px] uppercase tracking-[0.2em] mb-4 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali ke Beranda
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Detail <span class="text-indigo-600">Booking</span></h1>
                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $booking->status->color() === 'indigo' ? 'bg-indigo-100 text-indigo-600' : ($booking->status->color() === 'emerald' ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-600') }}">
                        {{ $booking->status->label() }}
                    </span>
                </div>
                <p class="text-gray-500 font-bold mt-2 tracking-tight">Kode Booking: <span class="font-mono text-gray-900 bg-gray-100 px-2 py-0.5 rounded">{{ $booking->booking_code }}</span></p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Info Venue -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-50 overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                       <svg class="w-40 h-40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl font-black text-gray-900 uppercase italic tracking-tight mb-8 relative z-10">Informasi <span class="text-indigo-600">Venue</span></h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Venue</p>
                            <p class="text-lg font-black text-gray-900">{{ $booking->venue->name }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Lapangan</p>
                            <p class="text-lg font-black text-indigo-600">{{ $booking->court->name }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</p>
                            <p class="text-lg font-black text-gray-900">{{ $booking->booking_date->translatedFormat('l, d F Y') }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jam Main</p>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-900 rounded-lg text-white font-mono text-sm font-bold">
                                {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Action -->
                @if($booking->status->value === 'HOLD')
                <div class="bg-indigo-900 rounded-[2.5rem] p-8 shadow-2xl shadow-indigo-200 text-white relative overflow-hidden">
                     <div class="absolute -right-10 -bottom-10 opacity-10">
                        <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                    </div>

                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div>
                            <h2 class="text-2xl font-black uppercase italic tracking-tight mb-2">Selesaikan Pembayaran</h2>
                            <p class="text-indigo-200 font-medium text-sm max-w-md">
                                Segera lakukan pembayaran sebelum batas waktu berakhir agar booking Anda tidak dibatalkan otomatis.
                            </p>
                            @if($booking->expires_at)
                            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white/10 rounded-xl backdrop-blur-sm border border-white/10 text-xs font-bold text-indigo-100">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Batas Bayar: {{ $booking->expires_at->format('H:i') }}
                            </div>
                            @endif
                        </div>
                        <a href="{{ route('bookings.checkout', ['booking' => $booking->id]) }}" 
                           class="whitespace-nowrap bg-white text-indigo-900 px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-100 transition-colors shadow-lg shadow-black/20">
                            Bayar Sekarang &rarr;
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Cost Summary -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-50">
                    <h3 class="text-lg font-black text-gray-900 uppercase italic tracking-tight mb-6">Rincian <span class="text-indigo-600">Biaya</span></h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm font-bold text-gray-500">
                            <span>Harga Sewa</span>
                            <span>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-px bg-gray-100"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-black text-gray-900 uppercase tracking-widest">Total</span>
                            <span class="text-2xl font-black text-indigo-600 font-display italic">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
