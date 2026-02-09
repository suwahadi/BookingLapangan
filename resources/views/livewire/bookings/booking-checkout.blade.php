<div class="max-w-3xl mx-auto px-4 py-12">
    <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] shadow-card overflow-hidden border border-gray-100 dark:border-gray-700">
        {{-- Header --}}
        <div class="bg-primary/5 dark:bg-surface-dark px-8 py-10 relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <span class="material-symbols-outlined text-[10rem] text-primary">shopping_cart_checkout</span>
            </div>
            
            <div class="relative z-10">
                <a href="{{ route('bookings.show', $booking->id) }}" class="inline-flex items-center gap-2 text-muted-light font-bold text-[10px] uppercase tracking-[0.2em] mb-4 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali ke Detail
                </a>
                <h1 class="text-3xl md:text-4xl font-black text-text-light dark:text-text-dark tracking-tight uppercase italic mb-2">Checkout <span class="text-primary">Pembayaran</span></h1>
                <p class="text-muted-light text-sm font-medium">Selesaikan pembayaran untuk mengamankan jadwal Anda</p>
            </div>
        </div>

        <div class="p-8 space-y-10">
            {{-- Order Summary --}}
            <div class="flex flex-col md:flex-row justify-between gap-6 p-6 bg-gray-50 dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="space-y-1">
                    <div class="text-[10px] font-black text-muted-light uppercase tracking-widest mb-1">Booking Detail</div>
                    <div class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ $booking->venue->name }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $booking->court->name }}</div>
                    <div class="flex items-center gap-2 text-xs font-mono text-muted-light mt-1">
                        <span class="material-symbols-outlined text-sm">calendar_today</span>
                        {{ $booking->booking_date->translatedFormat('d M Y') }}
                        <span class="mx-1">•</span>
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}
                    </div>
                </div>
                <div class="md:text-right flex flex-col md:items-end justify-center border-t md:border-t-0 md:border-l border-gray-200 dark:border-gray-700 pt-4 md:pt-0 md:pl-6 mt-4 md:mt-0">
                    <div class="text-[10px] font-black text-muted-light uppercase tracking-widest mb-1">Total Tagihan</div>
                    <div class="text-3xl md:text-4xl font-black text-primary font-display italic">
                        Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                    </div>
                    @if($booking->expires_at)
                    <div class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded mt-2 inline-block">
                        Bayar sebelum {{ $booking->expires_at->format('H:i') }}
                    </div>
                    @endif
                </div>
            </div>

            @if($errorMessage)
            <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-900 p-6 rounded-3xl flex items-start gap-4">
                <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-3xl">error</span>
                <div>
                    <h4 class="font-black text-rose-900 dark:text-rose-300 uppercase text-xs tracking-widest mb-1">Gagal Memproses</h4>
                    <p class="text-sm font-medium text-rose-700 dark:text-rose-400">{{ $errorMessage }}</p>
                </div>
            </div>
            @endif

            <div class="space-y-8">
                <!-- Payment Plan -->
                @if(!$isRemaining)
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-muted-light uppercase tracking-widest px-1">Pilih Skema Bayar</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative group cursor-pointer">
                            <input type="radio" wire:model.live="payPlan" value="FULL" class="peer sr-only">
                            <div class="h-full p-5 bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-100 dark:border-gray-700 peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:border-primary/30 flex flex-col justify-between gap-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-black text-xs uppercase tracking-widest">LUNAS</span>
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-primary peer-checked:bg-primary transition-all"></div>
                                </div>
                                <div>
                                    <p class="text-xl font-black text-primary font-display italic">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                                    <p class="text-[10px] font-bold text-muted-light mt-1">Tanpa Sisa Tagihan</p>
                                </div>
                            </div>
                        </label>

                        @if(($booking->venue->policy?->allow_dp) && $booking->dp_required_amount > 0)
                        <label class="relative group cursor-pointer">
                            <input type="radio" wire:model.live="payPlan" value="DP" class="peer sr-only">
                            <div class="h-full p-5 bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-100 dark:border-gray-700 peer-checked:border-primary peer-checked:bg-primary/5 transition-all hover:border-primary/30 flex flex-col justify-between gap-3">
                                <div class="flex items-center justify-between">
                                    <span class="font-black text-xs uppercase tracking-widest">DP (Down Payment)</span>
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-primary peer-checked:bg-primary transition-all"></div>
                                </div>
                                <div>
                                    <p class="text-xl font-black text-primary font-display italic">Rp {{ number_format($booking->dp_required_amount, 0, ',', '.') }}</p>
                                    <p class="text-[10px] font-bold text-muted-light mt-1">Sisa dibayar nanti</p>
                                </div>
                            </div>
                        </label>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Payment Method -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-muted-light uppercase tracking-widest px-1">Pilih Metode Pembayaran</label>
                    
                    <div class="space-y-3">
                        <!-- Virtual Account -->
                        <div class="overflow-hidden rounded-2xl border-2 {{ $paymentType === 'bank_transfer' ? 'border-primary bg-primary/5' : 'border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800' }}"
                             wire:key="pm-bank">
                            <label class="flex items-center gap-4 p-5 cursor-pointer">
                                <input type="radio" wire:model.live="paymentType" name="payment_method" value="bank_transfer" class="sr-only">
                                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center text-muted-light shrink-0">
                                    <span class="material-symbols-outlined">account_balance</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-black text-sm uppercase tracking-tight">Virtual Account</h4>
                                    <p class="text-[10px] font-bold text-muted-light">Verifikasi Otomatis</p>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 {{ $paymentType === 'bank_transfer' ? 'border-primary bg-primary' : 'border-gray-300' }} flex items-center justify-center">
                                    @if($paymentType === 'bank_transfer') <div class="w-2 h-2 bg-white rounded-full"></div> @endif
                                </div>
                            </label>

                            @if($paymentType === 'bank_transfer')
                            <div class="px-5 pb-5 pt-0 pl-[4rem]">
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach(['bca' => 'BCA', 'bni' => 'BNI', 'bri' => 'BRI', 'permata' => 'PERMATA'] as $code => $label)
                                    <button type="button" 
                                            wire:click="$set('bank', '{{ $code }}')"
                                            class="py-2 px-3 rounded-lg border text-[10px] font-black uppercase tracking-widest transition-all
                                            {{ $bank === $code ? 'border-primary bg-primary text-white shadow-lg shadow-primary/20' : 'border-gray-200 dark:border-gray-600 text-muted-light hover:border-primary/50 bg-white' }}">
                                        {{ $label }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- E-Wallet -->
                        <div class="overflow-hidden rounded-2xl border-2 {{ $paymentType === 'gopay' ? 'border-primary bg-primary/5' : 'border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800' }}"
                             wire:key="pm-gopay">
                            <label class="flex items-center gap-4 p-5 cursor-pointer">
                                <input type="radio" wire:model.live="paymentType" name="payment_method" value="gopay" class="sr-only">
                                <div class="w-10 h-10 bg-sky-100 dark:bg-sky-900/30 rounded-xl flex items-center justify-center text-sky-600 shrink-0">
                                    <span class="material-symbols-outlined">qr_code_scanner</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-black text-sm uppercase tracking-tight">QRIS / E-Wallet</h4>
                                    <p class="text-[10px] font-bold text-muted-light">Scan QR (GoPay, OVO, dll)</p>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 {{ $paymentType === 'gopay' ? 'border-primary bg-primary' : 'border-gray-300' }} flex items-center justify-center">
                                    @if($paymentType === 'gopay') <div class="w-2 h-2 bg-white rounded-full"></div> @endif
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div x-data="{ showConfirm: false }" class="pt-4">
                    <button type="button"
                            @click="showConfirm = true"
                            class="w-full bg-gradient-to-r from-[#8B1538] to-[#b91d47] text-white font-black rounded-xl px-8 py-4 text-sm uppercase tracking-widest hover:from-[#6d1029] hover:to-[#8B1538] transition-all transform active:scale-[0.99] shadow-xl shadow-[#8B1538]/30 flex items-center justify-center gap-3 group">
                        <span>Bayar Sekarang</span>
                        <span class="group-hover:translate-x-1 transition-transform material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                    
                    <div class="flex items-center justify-center gap-2 text-center mt-4 opacity-75">
                        <span class="material-symbols-outlined text-green-500 text-xs">lock</span>
                        <p class="text-[10px] font-bold text-muted-light">Transaksi Aman & Terenkripsi</p>
                    </div>

                    <!-- Confirmation Modal (Simplified) -->
                    <div x-show="showConfirm" 
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                         @click.self="showConfirm = false"
                         style="display: none;">
                        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-sm w-full shadow-2xl text-center space-y-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 mx-auto flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary text-3xl">payments</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 dark:text-white">Konfirmasi Pembayaran</h3>
                                <p class="text-sm text-gray-500 mt-2">
                                    Total: <span class="font-black text-primary">Rp {{ number_format($payPlan === 'DP' ? $booking->dp_required_amount : ($isRemaining ? ($booking->total_amount - $booking->paid_amount) : $booking->total_amount), 0, ',', '.') }}</span>
                                </p>
                            </div>
                            <div class="flex gap-3">
                                <button @click="showConfirm = false" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider text-gray-600">Batal</button>
                                <button x-on:click="showConfirm = false; $wire.createPayment()" class="flex-1 py-3 bg-primary hover:bg-primary-dark rounded-xl text-xs font-bold uppercase tracking-wider text-white">Ya, Bayar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
