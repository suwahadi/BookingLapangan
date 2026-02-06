<div class="space-y-10 pb-20">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <a href="{{ route('bookings.show', $booking->id) }}" class="inline-flex items-center gap-2 text-indigo-400 font-black text-[10px] uppercase tracking-[0.2em] mb-4 hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali ke Detail
        </a>
        <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Checkout <span class="text-indigo-600">Pembayaran</span></h1>
        <p class="text-gray-500 font-bold mt-2 tracking-tight">Selesaikan pembayaran untuk mengamankan jadwal Anda.</p>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Main Column -->
            <div class="lg:col-span-2 space-y-8">
                
                @if($errorMessage)
                <div class="bg-rose-50 border border-rose-100 p-6 rounded-3xl flex items-start gap-4">
                    <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-rose-900 uppercase text-xs tracking-widest mb-1">Gagal Memproses</h4>
                        <p class="text-sm font-medium text-rose-700">{{ $errorMessage }}</p>
                    </div>
                </div>
                @endif

                <!-- Payment Configuration -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-50">
                    <h2 class="text-xl font-black text-gray-900 uppercase italic tracking-tight mb-8">Metode <span class="text-indigo-600">Pembayaran</span></h2>

                    <div class="space-y-8">
                        <!-- Payment Plan -->
                        @if(!$isRemaining)
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pilih Skema Bayar</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="relative group cursor-pointer">
                                    <input type="radio" wire:model.live="payPlan" value="FULL" class="peer sr-only">
                                    <div class="h-full p-6 bg-gray-50 rounded-3xl border-2 border-transparent peer-checked:border-indigo-600 peer-checked:bg-indigo-50/30 transition-all hover:bg-gray-100 flex flex-col justify-between gap-4">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-gray-900 uppercase tracking-widest text-xs">LUNAS (Full Payment)</span>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 transition-all"></div>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-black text-indigo-600 font-display italic">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Tanpa Sisa Tagihan</p>
                                        </div>
                                    </div>
                                </label>

                                @if(($booking->venue->policy?->allow_dp) && $booking->dp_required_amount > 0)
                                <label class="relative group cursor-pointer">
                                    <input type="radio" wire:model.live="payPlan" value="DP" class="peer sr-only">
                                    <div class="h-full p-6 bg-gray-50 rounded-3xl border-2 border-transparent peer-checked:border-indigo-600 peer-checked:bg-indigo-50/30 transition-all hover:bg-gray-100 flex flex-col justify-between gap-4">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-gray-900 uppercase tracking-widest text-xs">DP (Down Payment)</span>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-600 transition-all"></div>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-black text-indigo-600 font-display italic">Rp {{ number_format($booking->dp_required_amount, 0, ',', '.') }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Sisa dibayar di venue/nanti</p>
                                        </div>
                                    </div>
                                </label>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="p-6 bg-indigo-50 rounded-3xl border border-indigo-100">
                             <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Tagihan Pelunasan</p>
                             <p class="text-3xl font-black text-indigo-900 font-display italic">Rp {{ number_format($booking->total_amount - $booking->paid_amount, 0, ',', '.') }}</p>
                        </div>
                        @endif

                        <!-- Payment Method -->
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pilih Jalur Pembayaran</label>
                            
                            <div class="space-y-4">
                                <!-- Virtual Account -->
                                <div class="overflow-hidden rounded-3xl border-2 {{ $paymentType === 'bank_transfer' ? 'border-indigo-600 bg-indigo-50/10' : 'border-gray-100 bg-white' }}"
                                     wire:key="pm-bank">
                                    <label class="flex items-center gap-4 p-6 cursor-pointer">
                                        <input type="radio" wire:model.live="paymentType" name="payment_method" value="bank_transfer" class="sr-only">
                                        <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-600 shrink-0">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-black text-gray-900 uppercase tracking-tight text-sm">Virtual Account (VA)</h4>
                                            <p class="text-xs font-bold text-gray-400 mt-1">Verifikasi Otomatis • BCA, BNI, BRI, Permata</p>
                                        </div>
                                        <div class="w-6 h-6 rounded-full border-2 {{ $paymentType === 'bank_transfer' ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300' }} flex items-center justify-center">
                                            @if($paymentType === 'bank_transfer') <div class="w-2.5 h-2.5 bg-white rounded-full"></div> @endif
                                        </div>
                                    </label>

                                    @if($paymentType === 'bank_transfer')
                                    <div class="px-6 pb-6 pt-0 pl-[4.5rem]">
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                                            @foreach(['bca' => 'BCA', 'bni' => 'BNI', 'bri' => 'BRI', 'permata' => 'PERMATA'] as $code => $label)
                                            <button type="button" 
                                                    wire:click="$set('bank', '{{ $code }}')"
                                                    class="py-3 px-2 rounded-xl border-2 text-xs font-black uppercase tracking-widest transition-all
                                                    {{ $bank === $code ? 'border-indigo-600 bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'border-gray-200 text-gray-500 hover:border-indigo-200' }}">
                                                {{ $label }}
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- E-Wallet / GoPay -->
                                <div class="overflow-hidden rounded-3xl border-2 {{ $paymentType === 'gopay' ? 'border-indigo-600 bg-indigo-50/10' : 'border-gray-100 bg-white' }}"
                                     wire:key="pm-gopay">
                                    <label class="flex items-center gap-4 p-6 cursor-pointer">
                                        <input type="radio" wire:model.live="paymentType" name="payment_method" value="gopay" class="sr-only">
                                        <div class="w-12 h-12 bg-sky-100 rounded-2xl flex items-center justify-center text-sky-600 shrink-0">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-black text-gray-900 uppercase tracking-tight text-sm">QRIS / E-Wallet</h4>
                                            <p class="text-xs font-bold text-gray-400 mt-1">Scan QR • GoPay, ShopeePay, OVO, Dana</p>
                                        </div>
                                        <div class="w-6 h-6 rounded-full border-2 {{ $paymentType === 'gopay' ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300' }} flex items-center justify-center">
                                            @if($paymentType === 'gopay') <div class="w-2.5 h-2.5 bg-white rounded-full"></div> @endif
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <button type="button"
                        class="w-full bg-gray-900 text-white font-black rounded-[2rem] px-8 py-6 text-sm uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed shadow-2xl shadow-gray-400 hover:shadow-indigo-400 flex items-center justify-center gap-3 group"
                        wire:click="createPayment"
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>PROSES PEMBAYARAN</span>
                    <span wire:loading.remove class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        MEMPROSES...
                    </span>
                </button>
                <p class="text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Transaksi Anda aman dan terenkripsi</p>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8 lg:top-10 lg:sticky lg:h-fit">
                <!-- Summary Card -->
                <div class="bg-indigo-900 rounded-[2.5rem] p-8 shadow-2xl shadow-indigo-900/20 text-white relative overflow-hidden">
                    <div class="relative z-10 space-y-6">
                         <div>
                            <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-1">Total Tagihan</p>
                            @if($payPlan === 'DP')
                                <p class="text-4xl font-black font-display italic tracking-tight">Rp {{ number_format($booking->dp_required_amount, 0, ',', '.') }}</p>
                                <p class="text-xs font-bold text-indigo-200 mt-2">*Pembayaran Awal (DP)</p>
                            @else
                                <p class="text-4xl font-black font-display italic tracking-tight">Rp {{ number_format($isRemaining ? ($booking->total_amount - $booking->paid_amount) : $booking->total_amount, 0, ',', '.') }}</p>
                            @endif
                        </div>

                        <div class="h-px bg-white/10"></div>

                        <div class="space-y-4">
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest">Detail Booking</p>
                                <p class="text-sm font-bold">{{ $booking->venue->name }}</p>
                                <p class="text-xs text-indigo-200">{{ $booking->court->name }}</p>
                            </div>
                             <div class="space-y-1">
                                <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest">Waktu Main</p>
                                <p class="text-sm font-bold">{{ $booking->booking_date->translatedFormat('d M Y') }}</p>
                                <p class="text-xs font-mono text-indigo-200">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Decor -->
                    <div class="absolute -right-6 -bottom-6 opacity-10 rotate-12">
                         <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                    </div>
                </div>

                @if($booking->expires_at && $booking->status->value === 'HOLD')
                <div class="bg-amber-50 rounded-[2rem] p-6 border border-amber-100 flex items-start gap-3">
                     <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                     <div>
                         <p class="text-xs font-black text-amber-800 uppercase tracking-widest mb-1">Batas Waktu</p>
                         <p class="text-xs font-medium text-amber-700 leading-relaxed">
                             Selesaikan pembayaran sebelum <span class="font-black">{{ $booking->expires_at->format('H:i') }}</span> atau pesanan akan dibatalkan otomatis.
                         </p>
                     </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
