<div class="space-y-10 pb-20">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-muted-light font-bold text-[10px] uppercase tracking-[0.2em] mb-4 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali ke Beranda
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl md:text-4xl font-black text-text-light dark:text-text-dark tracking-tight font-display italic uppercase">Detail <span class="text-primary">Booking</span></h1>
                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest border {{ $booking->status->color() === 'primary' ? 'bg-primary/10 text-primary border-primary/20' : ($booking->status->color() === 'emerald' ? 'bg-emerald-100 text-emerald-600 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200') }}">
                        {{ $booking->status->label() }}
                    </span>
                </div>
                <p class="text-muted-light font-bold mt-2 tracking-tight flex items-center gap-2">
                    Kode Booking: 
                    <span class="font-mono text-text-light dark:text-text-dark bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">{{ $booking->booking_code }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Info Venue -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] p-8 shadow-card border border-gray-100 dark:border-gray-700 overflow-hidden relative group">
                    <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                       <span class="material-symbols-outlined text-[10rem]">stadium</span>
                    </div>
                    
                    <h2 class="text-2xl font-black text-text-light dark:text-text-dark uppercase italic tracking-tight mb-8 relative z-10">Informasi <span class="text-primary">Venue</span></h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-muted-light uppercase tracking-widest">Venue</p>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">location_city</span>
                                <p class="text-lg font-black text-text-light dark:text-text-dark">{{ $booking->venue->name }}</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-muted-light uppercase tracking-widest">Lapangan</p>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">sports_tennis</span>
                                <p class="text-lg font-black text-primary">{{ $booking->court->name }}</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-muted-light uppercase tracking-widest">Tanggal</p>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">calendar_month</span>
                                <p class="text-lg font-black text-text-light dark:text-text-dark">{{ $booking->booking_date->translatedFormat('l, d F Y') }}</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-muted-light uppercase tracking-widest">Jam Main</p>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-text-light dark:bg-black rounded-lg text-white font-mono text-sm font-bold shadow-md">
                                <span class="material-symbols-outlined text-sm">schedule</span>
                                {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Action -->
                @if($booking->status->value === 'HOLD')
                <div class="bg-primary rounded-[2.5rem] p-8 shadow-2xl shadow-primary/30 text-white relative overflow-hidden group">
                     <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:scale-110 transition-transform duration-700">
                        <span class="material-symbols-outlined text-[15rem]">payments</span>
                    </div>

                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                        <div>
                            <h2 class="text-2xl font-black uppercase italic tracking-tight mb-2">Selesaikan Pembayaran</h2>
                            <p class="text-white/80 font-medium text-sm max-w-md leading-relaxed">
                                Segera lakukan pembayaran sebelum batas waktu berakhir agar booking Anda tidak dibatalkan otomatis.
                            </p>
                            @if($booking->expires_at)
                            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-white/20 rounded-xl backdrop-blur-md border border-white/20 text-xs font-bold text-white shadow-sm">
                                <span class="material-symbols-outlined text-sm animate-pulse">timer</span>
                                Batas Bayar: {{ $booking->expires_at->format('H:i') }}
                            </div>
                            @endif
                        </div>
                        <a href="{{ route('bookings.checkout', ['booking' => $booking->id]) }}" 
                           class="whitespace-nowrap bg-white text-primary px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-50 transition-all transform hover:-translate-y-1 shadow-lg shadow-black/20 flex items-center gap-2 group-hover:gap-3">
                            Bayar Sekarang 
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Cost Summary -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] p-8 shadow-card border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic tracking-tight mb-6">Rincian <span class="text-primary">Biaya</span></h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm font-bold text-muted-light">
                            <span>Harga Sewa</span>
                            <span>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-px bg-gray-100 dark:bg-gray-700 dashed"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-black text-text-light dark:text-text-dark uppercase tracking-widest">Total</span>
                            <span class="text-2xl font-black text-primary font-display italic">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
