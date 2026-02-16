<div class="space-y-10 pb-20">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <a href="{{ route('member.dashboard') }}" class="inline-flex items-center gap-2 text-muted-light font-bold text-[10px] uppercase tracking-[0.2em] mb-4 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali ke Dashboard
                </a>
                <h1 class="text-3xl md:text-4xl font-black text-text-light dark:text-text-dark tracking-tight font-display italic uppercase">Detail <span class="text-primary">Booking</span></h1>
                
                <div class="grid grid-cols-2 gap-4 mt-4 w-full max-w-[30rem]">
                    <div class="h-full">
                        @php
                            $statusClasses = match($booking->status->color()) {
                                'green' => 'bg-emerald-100 text-emerald-600 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800',
                                'yellow' => 'bg-amber-100 text-amber-600 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',
                                'red' => 'bg-rose-100 text-rose-600 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800',
                                default => 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700',
                            };
                        @endphp
                        <span class="flex items-center justify-center w-full h-full px-3 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest border text-center {{ $statusClasses }}">
                            {{ $booking->status->label() }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-2 bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 w-full h-full">
                        <span class="text-xs text-text-light dark:text-text-dark">{{ $booking->booking_code }}</span>
                        <button onclick="navigator.clipboard.writeText('{{ $booking->booking_code }}'); this.querySelector('span').textContent = 'check'; setTimeout(() => { this.querySelector('span').textContent = 'content_copy'; }, 1500);"
                                class="text-muted-light hover:text-primary transition-colors shrink-0 flex items-center" title="Salin kode booking">
                            <span class="material-symbols-outlined text-sm">content_copy</span>
                        </button>
                    </div>
                </div>
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
                        <div class="space-y-2">
                            <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest">Venue</p>
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary text-xl">map</span>
                                <p class="text-base font-bold text-text-light dark:text-text-dark">{{ $booking->venue->name }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest">Arena</p>
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary text-xl">{{ \App\Models\Venue::sportIcon($booking->court->sport ?? $booking->venue->sport_type) }}</span>
                                <p class="text-base font-bold text-primary">{{ $booking->court->name }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest">Tanggal</p>
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary text-xl">calendar_month</span>
                                <p class="text-base font-bold text-text-light dark:text-text-dark">{{ $booking->booking_date->translatedFormat('l, d F Y') }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest">Jam Main</p>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                @foreach($booking->grouped_slots as $slot)
                                    <span class="inline-flex items-center px-3 py-1 bg-primary/10 text-primary rounded-lg text-xs font-bold font-mono">
                                        {{ $slot['start'] }} - {{ $slot['end'] }}
                                    </span>
                                @endforeach
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

                    <div class="relative z-10 flex flex-col items-center text-center md:text-left md:items-start md:flex-row md:justify-between gap-8">
                        <div class="space-y-4 max-w-xl">
                            <h2 class="text-2xl md:text-3xl font-black uppercase italic tracking-tight leading-none">Selesaikan Pembayaran</h2>
                            <p class="text-white/90 font-medium text-sm leading-relaxed">
                                Segera lakukan pembayaran sebelum batas waktu berakhir agar booking Anda tidak dibatalkan otomatis.
                            </p>
                            @if($booking->expires_at)
                            <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/20 rounded-xl backdrop-blur-md border border-white/20 text-xs font-bold text-white shadow-sm hover:bg-white/25 transition-colors cursor-help">
                                <span class="material-symbols-outlined text-base animate-pulse">timer</span>
                                Batas Bayar: {{ $booking->expires_at->format('H:i') }}
                            </div>
                            @endif
                        </div>
                        
                        @if($booking->payments()->where('status', \App\Enums\PaymentStatus::PENDING)->exists())
                            @php
                                $payment = $booking->payments()->where('status', \App\Enums\PaymentStatus::PENDING)->latest()->first();
                            @endphp
                            <a href="{{ route('payments.show', ['payment' => $payment->id]) }}" 
                               class="w-full md:w-auto md:min-w-[200px] bg-white text-primary px-8 py-5 rounded-3xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-50 transition-all transform hover:-translate-y-1 shadow-2xl shadow-black/20 flex flex-shrink-0 items-center justify-center gap-3 group">
                                Lanjut Bayar 
                                <span class="material-symbols-outlined text-xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        @else
                            <a href="{{ route('bookings.checkout', ['booking' => $booking->id]) }}" 
                               class="w-full md:w-auto md:min-w-[200px] bg-white text-primary px-8 py-5 rounded-3xl font-black text-xs uppercase tracking-[0.2em] hover:bg-gray-50 transition-all transform hover:-translate-y-1 shadow-2xl shadow-black/20 flex flex-shrink-0 items-center justify-center gap-3 group">
                                Bayar Sekarang 
                                <span class="material-symbols-outlined text-xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Cost Summary -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] p-8 shadow-card border border-gray-100 dark:border-gray-700" wire:poll.5s>
                    <h3 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic tracking-tight mb-6">Rincian <span class="text-primary">Biaya</span></h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm font-bold text-muted-light">
                            <span>Harga Sewa</span>
                            <span>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                        </div>

                        @if($booking->discount_amount > 0)
                            <div class="flex justify-between items-center text-sm font-bold text-emerald-600">
                                <span>Diskon Voucher</span>
                                <span>- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        
                        @if($booking->paid_amount > 0 && $booking->paid_amount < $booking->payable_amount)
                            <div class="flex justify-between items-center text-sm font-bold text-emerald-600">
                                <span>Down Payment (Terbayar)</span>
                                <span>- Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="h-px bg-gray-100 dark:bg-gray-700 dashed"></div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-black text-text-light dark:text-text-dark uppercase tracking-widest">
                                    Sisa Pelunasan
                                </span>
                                <span class="text-2xl font-black text-primary font-display italic">
                                    Rp {{ number_format($booking->payable_amount - $booking->paid_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @elseif($booking->paid_amount >= $booking->payable_amount && $booking->paid_amount > 0)
                            <div class="flex justify-between items-center text-sm font-bold text-emerald-600">
                                <span>Total Bayar</span>
                                <span>- Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                            </div>

                            <div class="h-px bg-gray-100 dark:bg-gray-700 dashed"></div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-black text-text-light dark:text-text-dark uppercase tracking-widest">
                                    Status
                                </span>
                                <span class="text-2xl font-black text-emerald-500 font-display italic">
                                    LUNAS
                                </span>
                            </div>
                        @else
                            <div class="h-px bg-gray-100 dark:bg-gray-700 dashed"></div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-black text-text-light dark:text-text-dark uppercase tracking-widest">
                                    Total Tagihan
                                </span>
                                <span class="text-2xl font-black text-primary font-display italic">
                                    Rp {{ number_format($booking->payable_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Call to Action Button (Duplicate for Visibility) -->
                @if($booking->status->value === 'HOLD')
                    @if($booking->payments()->where('status', \App\Enums\PaymentStatus::PENDING)->exists())
                        @php
                            $payment = $booking->payments()->where('status', \App\Enums\PaymentStatus::PENDING)->latest()->first();
                        @endphp
                        <a href="{{ route('payments.show', ['payment' => $payment->id]) }}" 
                           class="w-full bg-primary text-white px-8 py-5 rounded-3xl font-black text-sm uppercase tracking-[0.2em] hover:bg-primary-dark transition-all transform hover:-translate-y-1 shadow-xl shadow-primary/30 flex items-center justify-center gap-3 group animate-pulse-subtle">
                            Lanjut Bayar 
                            <span class="material-symbols-outlined text-xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    @else
                        <a href="{{ route('bookings.checkout', ['booking' => $booking->id]) }}" 
                           class="w-full bg-primary text-white px-8 py-5 rounded-3xl font-black text-sm uppercase tracking-[0.2em] hover:bg-primary-dark transition-all transform hover:-translate-y-1 shadow-xl shadow-primary/30 flex items-center justify-center gap-3 group animate-pulse-subtle">
                            Bayar Sekarang 
                            <span class="material-symbols-outlined text-xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    @endif
                @endif

                <!-- Booking Actions -->
                @php
                    $bookingStart = $booking->booking_date->copy()->setTimeFromTimeString($booking->start_time);
                @endphp
                @if($booking->status === \App\Enums\BookingStatus::CONFIRMED && $bookingStart->isFuture())
                <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] p-8 shadow-card border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic tracking-tight mb-6">Aksi <span class="text-primary">Booking</span></h3>
                    
                    <div class="space-y-3">
                        <!-- Reschedule -->
                        @if($venuePolicy && $venuePolicy->reschedule_allowed)
                        <button wire:click="$set('showRescheduleModal', true)" class="w-full flex items-center justify-between p-4 rounded-2xl border border-gray-200 hover:border-primary hover:bg-primary/5 transition-all group">
                            <span class="text-sm font-bold text-gray-700 group-hover:text-primary">Reschedule Booking</span>
                            <span class="material-symbols-outlined text-gray-400 group-hover:text-primary">event_repeat</span>
                        </button>
                        @endif

                        <!-- Cancel -->
                        <button wire:click="$set('showCancelModal', true)" class="w-full flex items-center justify-between p-4 rounded-2xl border border-rose-100 hover:border-rose-500 hover:bg-rose-50 transition-all group">
                            <span class="text-sm font-bold text-rose-600">Batalkan Booking</span>
                            <span class="material-symbols-outlined text-rose-300 group-hover:text-rose-600">cancel</span>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    @if($showRefundModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" wire:click="$set('showRefundModal', false)"></div>
        <div class="bg-white rounded-[2rem] w-full max-w-sm overflow-hidden shadow-2xl relative z-10 p-8 animate-fade-in-up transform transition-all">
            <div class="mb-6 text-center">
                <h3 class="text-xl font-black text-gray-900 mb-2 font-display italic uppercase">Ajukan Refund?</h3>
                <p class="text-sm text-gray-500 font-medium leading-relaxed">
                    Permintaan refund akan dikirim ke admin venue. Proses persetujuan mengikuti kebijakan yang berlaku.
                </p>
                @if($venuePolicy && $venuePolicy->refund_allowed)
                    <div class="mt-4 p-3 bg-amber-50/50 rounded-xl text-xs font-bold text-amber-600 border border-amber-100/50">
                        Pastikan Anda telah membaca kebijakan refund venue ini.
                    </div>
                @endif
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <button wire:click="$set('showRefundModal', false)" 
                        class="py-3.5 px-4 rounded-xl border-2 border-gray-100 text-gray-600 font-black text-xs uppercase tracking-wider hover:bg-gray-50 hover:border-gray-200 transition-all">
                    Batal
                </button>
                <button wire:click="requestRefund" 
                        class="py-3.5 px-4 rounded-xl bg-amber-500 text-white font-black text-xs uppercase tracking-wider hover:bg-amber-600 shadow-lg shadow-amber-500/20 transition-all active:scale-95">
                    Ya, Ajukan
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Cancel Modal -->
    @if($showCancelModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" wire:click="$set('showCancelModal', false)"></div>
        <div class="bg-white rounded-[2rem] w-full max-w-sm overflow-hidden shadow-2xl relative z-10 p-8 animate-fade-in-up transform transition-all">
            @if($venuePolicy && !$venuePolicy->refund_allowed)
                <!-- Non-Refundable Logic -->
                <div class="text-center">
                    <h3 class="text-xl font-black text-gray-900 mb-2 font-display italic uppercase">Tidak Dapat Dibatalkan</h3>
                    <p class="text-sm text-gray-500 font-medium leading-relaxed mb-6">
                        Mohon maaf, booking ini tidak dapat dibatalkan (tidak ada refund) sesuai dengan kebijakan venue.
                    </p>
                    <button wire:click="$set('showCancelModal', false)" 
                            class="w-full py-3.5 px-4 rounded-xl bg-gray-900 text-white font-black text-xs uppercase tracking-wider hover:bg-black shadow-lg shadow-gray-900/20 transition-all active:scale-95">
                        Mengerti
                    </button>
                </div>
            @else
                <!-- Normal Cancellation Logic -->
                <div class="mb-6 text-center">
                    <h3 class="text-xl font-black text-gray-900 mb-2 font-display italic uppercase">Batalkan Booking?</h3>
                    <p class="text-sm text-gray-500 font-medium leading-relaxed">
                        Yakin ingin membatalkan booking ini?
                    </p>
                    
                    @if($venuePolicy)
                    <div class="mt-6 flex flex-col gap-3 bg-gray-50 rounded-2xl p-5 border border-gray-100 text-left">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kebijakan Venue</p>
                        
                        <!-- Reschedule -->
                        <div class="flex items-start gap-3">
                            <div>
                                <p class="text-[11px] font-bold text-gray-700">Reschedule</p>
                                @if($venuePolicy->reschedule_allowed)
                                    <p class="text-[10px] text-gray-500 font-medium">Maksimal H-{{ $venuePolicy->reschedule_deadline_hours }}j sebelum main</p>
                                @else
                                    <p class="text-[10px] text-gray-500 font-medium">Tidak tersedia</p>
                                @endif
                            </div>
                        </div>

                        <!-- Refund -->
                        <div class="flex items-start gap-3">
                            <div>
                                <p class="text-[11px] font-bold text-gray-700">Refund</p>
                                @if($venuePolicy->refund_allowed)
                                    <p class="text-[10px] text-gray-500 font-medium">Sesuai ketentuan waktu.</p>
                                @else
                                    <p class="text-[10px] text-gray-500 font-medium">Tidak tersedia</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mt-4 p-3 bg-rose-50 rounded-xl text-center border border-rose-100">
                        <p class="text-[10px] font-bold text-rose-600 uppercase tracking-wide">Tindakan ini tidak dapat diurungkan</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <button wire:click="$set('showCancelModal', false)" 
                            class="py-3.5 px-4 rounded-xl border-2 border-gray-100 text-gray-600 font-black text-xs uppercase tracking-wider hover:bg-gray-50 hover:border-gray-200 transition-all">
                        Tidak
                    </button>
                    <button wire:click="cancelBooking" 
                            class="py-3.5 px-4 rounded-xl bg-rose-600 text-white font-black text-xs uppercase tracking-wider hover:bg-rose-700 shadow-lg shadow-rose-500/20 transition-all active:scale-95">
                        Ya, Batalkan
                    </button>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Reschedule Modal -->
    @if($showRescheduleModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" wire:click="$set('showRescheduleModal', false)"></div>
        <div class="bg-white rounded-[2rem] w-full max-w-sm overflow-hidden shadow-2xl relative z-10 p-8 animate-fade-in-up transform transition-all">
            <div class="mb-6 text-center">
                <h3 class="text-xl font-black text-gray-900 mb-2 font-display italic uppercase">Reschedule Booking</h3>
                <p class="text-sm text-gray-500 font-medium leading-relaxed">
                    Fitur reschedule belum tersedia saat ini.
                </p>
                 <div class="mt-4 p-3 bg-blue-50/50 rounded-xl text-xs font-bold text-blue-600 border border-blue-100/50">
                     Mohon hubungi admin venue secara langsung untuk mengajukan perubahan jadwal.
                </div>
            </div>
            
            <button wire:click="$set('showRescheduleModal', false)" 
                    class="w-full py-3.5 px-4 rounded-xl bg-gray-900 text-white font-black text-xs uppercase tracking-wider hover:bg-black transition-colors shadow-lg shadow-gray-900/20">
                Mengerti
            </button>
        </div>
    </div>
    @endif

</div>
