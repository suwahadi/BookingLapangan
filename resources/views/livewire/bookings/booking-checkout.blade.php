<div class="space-y-10 pb-20">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <a href="{{ route('bookings.show', $booking->id) }}" class="inline-flex items-center gap-2 text-muted-light font-bold text-[10px] uppercase tracking-[0.2em] mb-4 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Detail
        </a>
        <h1 class="text-3xl md:text-4xl font-black text-text-light dark:text-text-dark tracking-tight font-display italic uppercase">Checkout <span class="text-primary">Pembayaran</span></h1>
        <p class="text-muted-light font-bold mt-2 tracking-tight">Selesaikan pembayaran untuk mengamankan jadwal Anda.</p>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            <!-- Main Column -->
            <div class="lg:col-span-2 space-y-8">
                
                @if($errorMessage)
                <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-900 p-6 rounded-3xl flex items-start gap-4">
                    <div class="w-10 h-10 bg-rose-100 dark:bg-rose-900 rounded-xl flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-rose-600 dark:text-rose-400">error</span>
                    </div>
                    <div>
                        <h4 class="font-black text-rose-900 dark:text-rose-300 uppercase text-xs tracking-widest mb-1">Gagal Memproses</h4>
                        <p class="text-sm font-medium text-rose-700 dark:text-rose-400">{{ $errorMessage }}</p>
                    </div>
                </div>
                @endif

                <!-- Payment Configuration -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] p-8 shadow-card border border-gray-100 dark:border-gray-700">
                    <h2 class="text-xl font-black text-text-light dark:text-text-dark uppercase italic tracking-tight mb-8">Metode <span class="text-primary">Pembayaran</span></h2>

                    <div class="space-y-8">
                        <!-- Payment Plan -->
                        @if(!$isRemaining)
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-muted-light uppercase tracking-widest">Pilih Skema Bayar</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="relative group cursor-pointer">
                                    <input type="radio" wire:model.live="payPlan" value="FULL" class="peer sr-only">
                                    <div class="h-full p-6 bg-gray-50 dark:bg-gray-800 rounded-3xl border-2 border-transparent peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:bg-gray-100 dark:hover:bg-gray-700 flex flex-col justify-between gap-4">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-text-light dark:text-text-dark uppercase tracking-widest text-xs">LUNAS (Full Payment)</span>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-primary peer-checked:bg-primary transition-all"></div>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-black text-primary font-display italic">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                                            <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest mt-1">Tanpa Sisa Tagihan</p>
                                        </div>
                                    </div>
                                </label>

                                @if(($booking->venue->policy?->allow_dp) && $booking->dp_required_amount > 0)
                                <label class="relative group cursor-pointer">
                                    <input type="radio" wire:model.live="payPlan" value="DP" class="peer sr-only">
                                    <div class="h-full p-6 bg-gray-50 dark:bg-gray-800 rounded-3xl border-2 border-transparent peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:bg-gray-100 dark:hover:bg-gray-700 flex flex-col justify-between gap-4">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-text-light dark:text-text-dark uppercase tracking-widest text-xs">DP (Down Payment)</span>
                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-primary peer-checked:bg-primary transition-all"></div>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-black text-primary font-display italic">Rp {{ number_format($booking->dp_required_amount, 0, ',', '.') }}</p>
                                            <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest mt-1">Sisa dibayar di venue/nanti</p>
                                        </div>
                                    </div>
                                </label>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="p-6 bg-primary/10 rounded-3xl border border-primary/20">
                             <p class="text-[10px] font-black text-primary uppercase tracking-widest mb-1">Tagihan Pelunasan</p>
                             <p class="text-3xl font-black text-primary font-display italic">Rp {{ number_format($booking->total_amount - $booking->paid_amount, 0, ',', '.') }}</p>
                        </div>
                        @endif

                        <!-- Payment Method -->
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-muted-light uppercase tracking-widest">Pilih Jalur Pembayaran</label>
                            
                            <div class="space-y-4">
                                <!-- Virtual Account -->
                                <div class="overflow-hidden rounded-3xl border-2 {{ $paymentType === 'bank_transfer' ? 'border-primary bg-primary/5' : 'border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800' }}"
                                     wire:key="pm-bank">
                                    <label class="flex items-center gap-4 p-6 cursor-pointer">
                                        <input type="radio" wire:model.live="paymentType" name="payment_method" value="bank_transfer" class="sr-only">
                                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center text-muted-light shrink-0">
                                            <span class="material-symbols-outlined">account_balance</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-black text-text-light dark:text-text-dark uppercase tracking-tight text-sm">Virtual Account (VA)</h4>
                                            <p class="text-xs font-bold text-muted-light mt-1">Verifikasi Otomatis • BCA, BNI, BRI, Permata</p>
                                        </div>
                                        <div class="w-6 h-6 rounded-full border-2 {{ $paymentType === 'bank_transfer' ? 'border-primary bg-primary' : 'border-gray-300' }} flex items-center justify-center">
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
                                                    {{ $bank === $code ? 'border-primary bg-primary text-white shadow-lg shadow-primary/20' : 'border-gray-200 dark:border-gray-600 text-muted-light hover:border-primary/50' }}">
                                                {{ $label }}
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- E-Wallet / GoPay -->
                                <div class="overflow-hidden rounded-3xl border-2 {{ $paymentType === 'gopay' ? 'border-primary bg-primary/5' : 'border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800' }}"
                                     wire:key="pm-gopay">
                                    <label class="flex items-center gap-4 p-6 cursor-pointer">
                                        <input type="radio" wire:model.live="paymentType" name="payment_method" value="gopay" class="sr-only">
                                        <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/30 rounded-2xl flex items-center justify-center text-sky-600 shrink-0">
                                            <span class="material-symbols-outlined">qr_code_scanner</span>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-black text-text-light dark:text-text-dark uppercase tracking-tight text-sm">QRIS / E-Wallet</h4>
                                            <p class="text-xs font-bold text-muted-light mt-1">Scan QR • GoPay, ShopeePay, OVO, Dana</p>
                                        </div>
                                        <div class="w-6 h-6 rounded-full border-2 {{ $paymentType === 'gopay' ? 'border-primary bg-primary' : 'border-gray-300' }} flex items-center justify-center">
                                            @if($paymentType === 'gopay') <div class="w-2.5 h-2.5 bg-white rounded-full"></div> @endif
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Submit Button with Confirmation -->
                <div x-data="{ showConfirm: false }" class="space-y-4">
                    <!-- Main CTA Button -->
                    <button type="button"
                            @click="showConfirm = true"
                            class="w-full bg-gradient-to-r from-[#8B1538] to-[#b91d47] text-white font-black rounded-2xl px-8 py-5 text-base uppercase tracking-widest hover:from-[#6d1029] hover:to-[#8B1538] transition-all transform active:scale-[0.98] shadow-2xl shadow-[#8B1538]/40 flex items-center justify-center gap-3 group">
                        <span class="material-symbols-outlined text-xl">lock</span>
                        <span>Bayar Sekarang</span>
                        <span class="group-hover:translate-x-1 transition-transform material-symbols-outlined text-xl">arrow_forward</span>
                    </button>
                    
                    <div class="flex items-center justify-center gap-3 text-center">
                        <span class="material-symbols-outlined text-green-500 text-sm">verified_user</span>
                        <p class="text-xs font-bold text-muted-light">Transaksi Aman & Terenkripsi SSL</p>
                    </div>

                    <!-- Confirmation Modal -->
                    <div x-show="showConfirm" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                         @click.self="showConfirm = false"
                         style="display: none;">
                        
                        <div x-show="showConfirm"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-md w-full shadow-2xl">
                            
                            <!-- Icon -->
                            <div class="flex justify-center mb-6">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#8B1538]/10 to-[#8B1538]/20 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[#8B1538] text-4xl">payments</span>
                                </div>
                            </div>

                            <!-- Title -->
                            <h3 class="text-xl font-black text-gray-900 dark:text-white text-center mb-3">Konfirmasi Pembayaran</h3>
                            
                            <!-- Description -->
                            <div class="text-center mb-8">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    Anda akan melakukan pembayaran sebesar:
                                </p>
                                <p class="text-3xl font-black text-[#8B1538]">
                                    @if($payPlan === 'DP')
                                        Rp {{ number_format($booking->dp_required_amount, 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($isRemaining ? ($booking->total_amount - $booking->paid_amount) : $booking->total_amount, 0, ',', '.') }}
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 mt-2">via {{ $paymentType === 'bank_transfer' ? 'Virtual Account ' . strtoupper($bank) : 'QRIS/E-Wallet' }}</p>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-4">
                                <button type="button"
                                        @click="showConfirm = false"
                                        class="flex-1 py-4 px-6 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl transition-colors text-sm uppercase tracking-wider">
                                    Batal
                                </button>
                                <button type="button"
                                        x-on:click="showConfirm = false; setTimeout(() => { @this.createPayment() }, 50)"
                                        class="flex-1 py-4 px-6 bg-gradient-to-r from-[#8B1538] to-[#b91d47] hover:from-[#6d1029] hover:to-[#8B1538] text-white font-bold rounded-xl transition-all text-sm uppercase tracking-wider flex items-center justify-center gap-2">
                                    Ya, Bayar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8 lg:top-10 lg:sticky lg:h-fit">
                <!-- Summary Card -->
                <div class="bg-primary rounded-[2.5rem] p-8 shadow-2xl shadow-primary/30 text-white relative overflow-hidden group">
                    <div class="relative z-10 space-y-6">
                         <div>
                            <p class="text-[10px] font-black text-white/60 uppercase tracking-widest mb-1">Total Tagihan</p>
                            @if($payPlan === 'DP')
                                <p class="text-4xl font-black font-display italic tracking-tight">Rp {{ number_format($booking->dp_required_amount, 0, ',', '.') }}</p>
                                <p class="text-xs font-bold text-white/80 mt-2">*Pembayaran Awal (DP)</p>
                            @else
                                <p class="text-4xl font-black font-display italic tracking-tight">Rp {{ number_format($isRemaining ? ($booking->total_amount - $booking->paid_amount) : $booking->total_amount, 0, ',', '.') }}</p>
                            @endif
                        </div>

                        <div class="h-px bg-white/10"></div>

                        <div class="space-y-4">
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-white/60 uppercase tracking-widest">Detail Booking</p>
                                <p class="text-sm font-bold">{{ $booking->venue->name }}</p>
                                <p class="text-xs text-white/80">{{ $booking->court->name }}</p>
                            </div>
                             <div class="space-y-1">
                                <p class="text-[10px] font-black text-white/60 uppercase tracking-widest">Waktu Main</p>
                                <p class="text-sm font-bold">{{ $booking->booking_date->translatedFormat('d M Y') }}</p>
                                <p class="text-xs font-mono text-white/80">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Decor -->
                    <div class="absolute -right-6 -bottom-6 opacity-10 rotate-12 group-hover:scale-110 transition-transform duration-700">
                         <span class="material-symbols-outlined text-[10rem]">verified</span>
                    </div>
                </div>

                @if($booking->expires_at && $booking->status->value === 'HOLD')
                <div class="bg-amber-50 dark:bg-amber-900/20 rounded-[2rem] p-6 border border-amber-100 dark:border-amber-800 flex items-start gap-3">
                     <span class="material-symbols-outlined text-amber-600 dark:text-amber-500">timer</span>
                     <div>
                         <p class="text-xs font-black text-amber-800 dark:text-amber-400 uppercase tracking-widest mb-1">Batas Waktu</p>
                         <p class="text-xs font-medium text-amber-700 dark:text-amber-500 leading-relaxed">
                             Selesaikan pembayaran sebelum <span class="font-black">{{ $booking->expires_at->format('H:i') }}</span> atau pesanan akan dibatalkan otomatis.
                         </p>
                     </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
