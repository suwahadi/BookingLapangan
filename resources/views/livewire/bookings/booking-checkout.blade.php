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
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 font-medium">
                        {!! \App\Models\Venue::getSportSvg($booking->court->sport ?? $booking->venue->sport_type, 'w-5 h-5') !!}
                        {{ $booking->court->name }}
                    </div>
                    <div class="flex items-center gap-2 text-xs text-muted-light mt-1">
                        {{ $booking->booking_date->translatedFormat('d M Y') }}

                        @foreach($booking->grouped_slots as $slot)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-100 dark:bg-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                            {{ $slot['start'] }} - {{ $slot['end'] }}
                        </span>
                        @endforeach
                    </div>
                </div>
                <div class="md:text-right flex flex-col md:items-end justify-center border-t md:border-t-0 md:border-l border-gray-200 dark:border-gray-700 pt-4 md:pt-0 md:pl-6 mt-4 md:mt-0">
                    <div class="text-[10px] font-black text-muted-light uppercase tracking-widest mb-1">Total Bayar Sekarang</div>
                    @php
                        $payableAmount = max(0, $booking->total_amount - $booking->discount_amount);
                        $displayAmount = match($payPlan) {
                            'DP' => min($booking->dp_required_amount, $payableAmount),
                            'REMAINING' => max(0, $payableAmount - $booking->paid_amount),
                            default => $payableAmount,
                        };
                    @endphp
                    <div class="text-3xl md:text-4xl font-black text-primary font-display italic">
                        Rp {{ number_format($displayAmount, 0, ',', '.') }}
                    </div>
                    @if($booking->discount_amount > 0)
                        <div class="text-[10px] font-bold text-emerald-600 mt-1">Diskon Voucher: -Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</div>
                    @endif
                    @if($payPlan === 'DP')
                        <div class="text-[10px] font-bold text-muted-light mt-1">Total Nilai Booking: Rp {{ number_format($payableAmount, 0, ',', '.') }}</div>
                    @elseif($isRemaining)
                         <div class="text-[10px] font-bold text-emerald-600 mt-1">Sudah Dibayar: Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</div>
                    @endif
                    @if($booking->expires_at)
                    <div class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded mt-2 inline-block">
                        Bayar sebelum {{ $booking->expires_at->format('H:i') }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="space-y-8">
                <!-- Payment Plan -->
                @if(!$isRemaining)
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-muted-light uppercase tracking-widest px-1">Pilih Skema Bayar</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- FULL Payment Option -->
                        <div class="relative cursor-pointer group" wire:click="$set('payPlan', 'FULL')">
                            <div class="h-full p-5 rounded-3xl border-2 transition-all duration-300 flex flex-col justify-between gap-4 shadow-sm relative overflow-hidden
                                {{ $payPlan === 'FULL' 
                                    ? 'bg-[#FFF5F7] border-[#8B1538] ring-1 ring-[#8B1538]/20' 
                                    : 'bg-white border-gray-100 hover:border-gray-200 hover:shadow-md' }}">
                                
                                <div class="flex items-start justify-between">
                                    <span class="font-black text-[10px] uppercase tracking-[0.2em] {{ $payPlan === 'FULL' ? 'text-[#8B1538]' : 'text-gray-400' }}">
                                        Bayar Lunas
                                    </span>
                                    <div class="relative w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-300
                                        {{ $payPlan === 'FULL' ? 'border-[#8B1538] bg-white' : 'border-gray-200 bg-white' }}">
                                        <div class="w-2.5 h-2.5 rounded-full bg-[#8B1538] transition-all duration-300 transform {{ $payPlan === 'FULL' ? 'scale-100' : 'scale-0 opacity-0' }}"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <p class="text-2xl font-black {{ $payPlan === 'FULL' ? 'text-[#8B1538]' : 'text-gray-900' }} font-display italic tracking-tight">
                                        Rp {{ number_format(max(0, $booking->total_amount - $booking->discount_amount), 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-1">Tanpa Sisa Tagihan</p>
                                </div>
                            </div>
                        </div>

                        <!-- DP Payment Option -->
                        @if(($booking->venue->policy?->allow_dp) && $booking->dp_required_amount > 0)
                        <div class="relative cursor-pointer group" wire:click="$set('payPlan', 'DP')">
                            <div class="h-full p-5 rounded-3xl border-2 transition-all duration-300 flex flex-col justify-between gap-4 shadow-sm relative overflow-hidden
                                {{ $payPlan === 'DP' 
                                    ? 'bg-[#FFF5F7] border-[#8B1538] ring-1 ring-[#8B1538]/20' 
                                    : 'bg-white border-gray-100 hover:border-gray-200 hover:shadow-md' }}">
                                
                                <div class="flex items-start justify-between">
                                    <span class="font-black text-[10px] uppercase tracking-[0.2em] {{ $payPlan === 'DP' ? 'text-[#8B1538]' : 'text-gray-400' }}">
                                        DP (Down Payment)
                                    </span>
                                    <div class="relative w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-300
                                        {{ $payPlan === 'DP' ? 'border-[#8B1538] bg-white' : 'border-gray-200 bg-white' }}">
                                        <div class="w-2.5 h-2.5 rounded-full bg-[#8B1538] transition-all duration-300 transform {{ $payPlan === 'DP' ? 'scale-100' : 'scale-0 opacity-0' }}"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <p class="text-2xl font-black {{ $payPlan === 'DP' ? 'text-[#8B1538]' : 'text-gray-900' }} font-display italic tracking-tight">
                                        Rp {{ number_format($booking->dp_required_amount, 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-1">Sisa dibayar nanti</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Payment Method -->
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-muted-light uppercase tracking-widest px-1">Pilih Metode Pembayaran</label>
                    
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden divide-y divide-gray-50 dark:divide-gray-700/50 shadow-sm">
                        
                        <!-- Virtual Account Section -->
                        <div x-data="{ open: true }" class="bg-white dark:bg-gray-800">
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center text-indigo-600 shrink-0">
                                        <span class="material-symbols-outlined text-xl">account_balance</span>
                                    </div>
                                    <div class="text-left">
                                        <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100">Transfer Virtual Account</h4>
                                        <div class="flex gap-1.5 mt-1 overflow-x-auto no-scrollbar">
                                            <img src="https://asset.ayo.co.id/assets/img/bca.png" class="h-3 w-auto opacity-50 grayscale" alt="BCA">
                                            <img src="https://asset.ayo.co.id/assets/img/bni.png" class="h-3 w-auto opacity-50 grayscale" alt="BNI">
                                            <img src="https://asset.ayo.co.id/assets/img/bri.png" class="h-3 w-auto opacity-50 grayscale" alt="BRI">
                                            <img src="https://asset.ayo.co.id/assets/img/mandiri.png" class="h-3 w-auto opacity-50 grayscale" alt="Mandiri">
                                            <img src="https://asset.ayo.co.id/assets/img/permata_only.png" class="h-3 w-auto opacity-50 grayscale" alt="Permata">
                                        </div>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }">expand_more</span>
                            </button>
                            
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="px-4 pb-4 space-y-3">
                                
                                @foreach([
                                    'bca' => ['label' => 'BCA', 'logo' => 'https://asset.ayo.co.id/assets/img/bca.png'],
                                    'bni' => ['label' => 'BNI', 'logo' => 'https://asset.ayo.co.id/assets/img/bni.png'],
                                    'bri' => ['label' => 'BRI', 'logo' => 'https://asset.ayo.co.id/assets/img/bri.png'],
                                    'permata' => ['label' => 'Permata Bank', 'logo' => 'https://asset.ayo.co.id/assets/img/permata_only.png'],
                                    'mandiri' => ['label' => 'Mandiri', 'logo' => 'https://asset.ayo.co.id/assets/img/mandiri.png']
                                ] as $code => $bankData)
                                <label class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all group
                                    {{ ($paymentType === 'bank_transfer' && $bank === $code) 
                                        ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/10 ring-1 ring-indigo-600' 
                                        : 'border-transparent hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-8 bg-white dark:bg-gray-700 rounded border border-gray-100 dark:border-gray-600 flex items-center justify-center p-1.5">
                                            <img src="{{ $bankData['logo'] }}" alt="{{ $bankData['label'] }}" class="h-full w-auto object-contain">
                                        </div>
                                        <div>
                                            <div class="font-bold text-xs text-gray-900 dark:text-gray-100">{{ $bankData['label'] }}</div>
                                            <div class="text-[10px] text-gray-500">Virtual Account</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-[10px] font-black text-emerald-500 uppercase">Gratis</div>
                                        
                                        <div class="relative w-5 h-5 rounded-full border flex items-center justify-center transition-all
                                            {{ ($paymentType === 'bank_transfer' && $bank === $code) 
                                                ? 'border-indigo-600' 
                                                : 'border-gray-300 dark:border-gray-600 group-hover:border-gray-400' }}">
                                            @if($paymentType === 'bank_transfer' && $bank === $code) 
                                                <div class="w-2.5 h-2.5 bg-indigo-600 rounded-full"></div> 
                                            @endif
                                        </div>
                                    </div>
                                    <input type="radio" wire:model.live="bank" value="{{ $code }}" class="sr-only" wire:click="$set('paymentType', 'bank_transfer')">
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- QRIS Section -->
                        <label class="w-full flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group
                            {{ $paymentType === 'gopay' ? 'bg-indigo-50/30' : '' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600 flex items-center justify-center p-1.5 shadow-sm">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-full w-auto object-contain">
                                </div>
                                <div class="text-left">
                                    <h4 class="font-bold text-sm text-gray-900 dark:text-gray-100">QRIS / E-Wallet</h4>
                                    <div class="text-[10px] text-gray-500">Scan QR (GoPay, OVO, ShopeePay)</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-[10px] font-black text-emerald-500 uppercase">Gratis</div>
                                
                                <div class="relative w-5 h-5 rounded-full border flex items-center justify-center transition-all
                                    {{ $paymentType === 'gopay' 
                                        ? 'border-indigo-600' 
                                        : 'border-gray-300 dark:border-gray-600 group-hover:border-gray-400' }}">
                                    @if($paymentType === 'gopay') 
                                        <div class="w-2.5 h-2.5 bg-indigo-600 rounded-full"></div> 
                                    @endif
                                </div>
                            </div>
                            <input type="radio" wire:model.live="paymentType" name="payment_method" value="gopay" class="sr-only">
                        </label>

                    </div>
                </div>

                <!-- Submit Button -->
                <div x-data="{ showConfirm: false }" class="pt-4 pb-12">
                    <button type="button"
                            @click="showConfirm = true"
                            class="w-full bg-gradient-to-r from-[#8B1538] to-[#b91d47] text-white font-black rounded-xl px-8 py-4 text-sm uppercase tracking-widest hover:from-[#6d1029] hover:to-[#8B1538] transition-all transform active:scale-[0.99] shadow-xl shadow-[#8B1538]/30 flex items-center justify-center gap-3 group">
                        <span>Bayar Sekarang</span>
                        <span class="group-hover:translate-x-1 transition-transform material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                    
                    <div class="flex items-center justify-center gap-2 text-center mt-4 opacity-75">
                        <span class="material-symbols-outlined text-green-500 text-xs">lock</span>
                        <p class="text-[10px] font-bold text-muted-light">Transaksi Aman & Otomatis</p>
                    </div>

                    <!-- Confirmation Modal -->
                    <div x-show="showConfirm" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                         @click.self="showConfirm = false"
                         style="display: none;">
                        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-sm w-full shadow-2xl text-center space-y-6 transform transition-all"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-90"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-90">
                            
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase italic tracking-tight mb-2">Konfirmasi Data</h3>
                                <p class="text-sm text-gray-500 font-medium">
                                    Yakin data booking sudah benar?
                                </p>
                            </div>

                            <div class="flex gap-3">
                                <button @click="showConfirm = false" 
                                        class="flex-1 py-3.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-xl text-xs font-black uppercase tracking-wider text-gray-600 dark:text-gray-300 transition-colors">
                                    Cek Lagi
                                </button>
                                <button x-on:click="showConfirm = false; $wire.createPayment()" 
                                        class="flex-1 py-3.5 bg-primary hover:bg-primary-dark rounded-xl text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-primary/30 transition-all transform active:scale-95">
                                    Lanjut Bayar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
