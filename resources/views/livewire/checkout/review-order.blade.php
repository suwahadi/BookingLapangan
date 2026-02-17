<div class="min-h-screen bg-gray-50 py-8" x-data="{ showPolicyModal: false, showConfirm: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if(count($selectedSlots) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                
                <!-- Left Column: Order Details -->
                <div class="lg:col-span-3 space-y-6">
                    
                    <!-- Guest Info Form (Only for Guest) -->
                    @guest
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-[#8B1538]">person</span>
                            <h3 class="text-lg font-black text-gray-900">Data Penyewa</h3>
                        </div>

                        <div class="mb-6">
                            <p class="text-sm text-gray-500">Sudah punya akun member? <button type="button" onclick="Livewire.dispatch('openAuthModal', { mode: 'login' })" class="text-[#8B1538] font-bold hover:underline">Masuk disini</button></p>
                        </div>

                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <input type="text" wire:model="guestName" placeholder="Nama Lengkap" 
                                class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#8B1538] focus:ring-[#8B1538] placeholder-gray-400 text-sm transition-all"
                                >
                                @error('guestName') <span class="text-xs text-rose-500 font-bold ml-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Phone & Email -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-sm font-bold flex items-center gap-1">
                                                <img src="https://flagcdn.com/w20/id.png" class="w-5 rounded-sm" alt="ID">
                                                +62
                                            </span>
                                            <span class="text-gray-300 mx-2">|</span>
                                        </div>
                                        <input type="text" wire:model="guestPhone" placeholder="Nomor Ponsel" 
                                        class="w-full pl-24 pr-4 py-3 rounded-xl border-gray-200 focus:border-[#8B1538] focus:ring-[#8B1538] placeholder-gray-400 text-sm transition-all"
                                        >
                                    </div>
                                    @error('guestPhone') <span class="text-xs text-rose-500 font-bold ml-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <input type="email" wire:model="guestEmail" placeholder="Email" 
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#8B1538] focus:ring-[#8B1538] placeholder-gray-400 text-sm transition-all"
                                    >
                                    @error('guestEmail') <span class="text-xs text-rose-500 font-bold ml-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-gray-50 rounded-xl flex items-start gap-3">
                            <span class="material-symbols-outlined text-gray-400 text-lg mt-0.5">info</span>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Dengan melakukan booking lapangan, Anda akan otomatis terdaftar sebagai member.
                            </p>
                        </div>
                    </div>
                    @endguest

                    <!-- Venue Info -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h2 class="text-xl font-black text-gray-900 mb-1">{{ $venueCourt->venue->name ?? 'Venue' }}</h2>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span class="text-amber-500 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">star</span>
                                {{ number_format($venueCourt->venue->rating_avg ?? 0, 1) }}
                            </span>
                            <span>•</span>
                            <span>{{ $venueCourt->venue->city ?? 'Kota' }}</span>
                        </div>
                    </div>

                    @if($errorMessage)
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 text-sm font-bold flex items-center gap-2">
                            <span class="material-symbols-outlined">error</span>
                            {{ $errorMessage }}
                        </div>
                    @endif

                    <!-- Selected Slots -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-black text-gray-900">{{ $venueCourt->name ?? 'Lapangan' }}</h3>
                        <div class="flex items-center gap-2 mb-6">
                            {!! \App\Models\Venue::getSportSvg($venueCourt->sport ?? '', 'w-4 h-4') !!}
                            <span class="text-sm font-semibold">{{ $venueCourt->sport }}</span>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach($selectedSlots as $index => $slot)
                                <div class="flex items-center justify-between py-3 border-l-4 border-[#8B1538] pl-4 bg-gray-50 rounded-r-xl pr-4">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-700">
                                            {{ \Carbon\Carbon::parse($slot['date'])->translatedFormat('D, d F Y') }} • {{ $slot['start'] }} - {{ $slot['end'] }}
                                        </div>
                                        <div class="text-sm font-bold text-[#8B1538]">
                                            Rp{{ number_format($slot['amount'], 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <button type="button" 
                                            wire:click="removeSlot({{ $index }})" 
                                            class="w-10 h-10 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-red-500 hover:border-red-200 transition-colors">
                                        <span class="material-symbols-outlined text-xl">delete</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" 
                                onclick="history.back()" 
                                class="mt-6 flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-[#8B1538] transition-colors">
                            <span class="material-symbols-outlined text-lg">arrow_back</span>
                            Ubah Jadwal
                        </button>
                    </div>
                </div>

                <!-- Right Column: Summary & Payment -->
                <div class="lg:col-span-2 space-y-4">
                    
                    <!-- Voucher -->
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        @if($appliedVoucherCode)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-emerald-600">confirmation_number</span>
                                    </span>
                                    <div>
                                        <p class="text-xs font-black text-emerald-700 uppercase tracking-widest">Voucher</p>
                                        <p class="text-[11px] font-bold text-emerald-600 mt-0.5">Hemat Rp{{ number_format($discountAmount, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <button type="button" wire:click="removeVoucher" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-lg">close</span>
                                </button>
                            </div>
                        @else
                            <button type="button" wire:click="toggleVoucherModal" class="w-full flex items-center justify-between text-left">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-10 rounded-xl bg-[#8B1538]/10 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[#8B1538]">confirmation_number</span>
                                    </span>
                                    <span class="font-bold text-gray-800">Gunakan Voucher</span>
                                </div>
                                <span class="material-symbols-outlined text-gray-400">chevron_right</span>
                            </button>
                        @endif
                    </div>

                    <!-- Cost Breakdown -->
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-10 h-10 rounded-xl bg-[#8B1538]/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[#8B1538]">receipt_long</span>
                            </span>
                            <span class="font-bold text-gray-800">Rincian Biaya</span>
                        </div>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Biaya Sewa</span>
                                <span class="font-bold text-gray-800">Rp{{ number_format($totalAmount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Biaya Produk Tambahan</span>
                                <span class="font-bold text-gray-800">Rp0</span>
                            </div>
                            @if($discountAmount > 0)
                            <div class="flex justify-between">
                                <span class="text-emerald-600 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">confirmation_number</span>
                                    Diskon ({{ $appliedVoucherCode }})
                                </span>
                                <span class="font-bold text-emerald-600">-Rp{{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="border-t border-dashed border-gray-200 pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="font-bold text-gray-700">Total Bayar</span>
                                    <span class="font-black text-lg text-gray-900">Rp{{ number_format($this->netAmount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <div class="flex items-center gap-3 mb-5">
                            <span class="w-10 h-10 rounded-xl bg-[#8B1538]/10 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[#8B1538]">payments</span>
                            </span>
                            <span class="font-bold text-gray-800">Atur Pembayaran</span>
                        </div>
                        
                        <div class="space-y-3">
                            <!-- Bayar Lunas -->
                            <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                                {{ $payPlan === 'FULL' ? 'border-[#8B1538] bg-[#8B1538]/5' : 'border-gray-100 hover:border-gray-200' }}">
                                <input type="radio" wire:model.live="payPlan" name="pay_plan" value="FULL" class="w-4 h-4 text-[#8B1538] border-gray-300 focus:ring-[#8B1538]">
                                <div class="flex-1">
                                    <span class="font-bold text-gray-800">Bayar Lunas</span>
                                    <div class="text-[#8B1538] font-bold">Rp{{ number_format($this->netAmount, 0, ',', '.') }}</div>
                                </div>
                                @if($payPlan === 'FULL')
                                    <span class="material-symbols-outlined text-[#8B1538] text-xl">check_circle</span>
                                @endif
                            </label>

                            <!-- DP Option (only if policy allows) -->
                            @if($this->isDpAllowed())
                            <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                                {{ $payPlan === 'DP' ? 'border-[#8B1538] bg-[#8B1538]/5' : 'border-gray-100 hover:border-gray-200' }}">
                                <input type="radio" wire:model.live="payPlan" name="pay_plan" value="DP" class="w-4 h-4 text-[#8B1538] border-gray-300 focus:ring-[#8B1538]">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-800">Bayar DP</span>
                                        <span class="text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full uppercase tracking-widest border border-amber-100">Min {{ $venuePolicy->dp_min_percent }}%</span>
                                    </div>
                                    <div class="text-[#8B1538] font-bold">Rp{{ number_format($dpAmount, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-gray-400 font-medium mt-0.5">
                                        Sisa Rp{{ number_format($this->netAmount - $dpAmount, 0, ',', '.') }} dilunasi nanti
                                    </div>
                                </div>
                                @if($payPlan === 'DP')
                                    <span class="material-symbols-outlined text-[#8B1538] text-xl">check_circle</span>
                                @endif
                            </label>
                            @endif
                        </div>

                        <!-- Payable summary -->
                        @if($payPlan === 'DP' && $this->isDpAllowed())
                        <div class="mt-4 p-3 bg-amber-50 rounded-xl border border-amber-100">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-amber-700">Yang Harus Dibayar Sekarang</span>
                                <span class="font-black text-lg text-amber-700">Rp{{ number_format($dpAmount, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-[10px] text-amber-600 mt-1">Pelunasan sisa tagihan wajib dilakukan sebelum jadwal bermain</p>
                        </div>
                        @endif
                    </div>

                    <!-- Policy -->
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                        <button type="button" @click="showPolicyModal = true" class="w-full flex items-center justify-between text-left">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-red-500">policy</span>
                                </span>
                                <span class="text-gray-800 text-sm">Kebijakan Reschedule & Pembatalan</span>
                            </div>
                            <span class="material-symbols-outlined text-gray-400">chevron_right</span>
                        </button>
                    </div>

                    <!-- CTA Button -->
                    <div class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t border-gray-200 lg:static lg:bg-transparent lg:border-0 lg:p-0 z-50">
                        <button type="button"
                                @click="showConfirm = true"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-70 cursor-wait"
                                class="w-full py-4 bg-[#8B1538] hover:bg-[#6d1029] text-white font-bold text-base rounded-2xl shadow-lg shadow-[#8B1538]/20 transition-all active:scale-[0.98]">
                            <span wire:loading.remove wire:target="proceedToPayment">Lanjutkan ke Pembayaran</span>
                            <span wire:loading wire:target="proceedToPayment" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>

                    <!-- Confirmation Modal -->
                    <template x-teleport="body">
                        <div x-show="showConfirm" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                             style="z-index: 9999; display: none;"
                             @click.self="showConfirm = false"
                             x-cloak>
                            <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl text-center space-y-6 transform transition-all"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-90"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-90">

                                <div>
                                    <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tight mb-2">Konfirmasi Data</h3>
                                    <p class="text-sm text-gray-500 font-medium">
                                        Yakin data booking sudah benar?
                                    </p>
                                </div>

                                <div class="flex gap-3">
                                    <button @click="showConfirm = false" 
                                            class="flex-1 py-3.5 bg-gray-100 hover:bg-gray-200 rounded-xl text-xs font-black uppercase tracking-wider text-gray-600 transition-colors">
                                        Cek Lagi
                                    </button>
                                    <button x-on:click="showConfirm = false; $wire.proceedToPayment()" 
                                            class="flex-1 py-3.5 bg-[#8B1538] hover:bg-[#6d1029] rounded-xl text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-[#8B1538]/30 transition-all transform active:scale-95">
                                        Ya, Booking
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                     <!-- Spacer for fixed bottom button on mobile -->
                    <div class="h-24 lg:hidden"></div>
                </div>
            </div>
        @else
            <!-- Full Width Empty State -->
            <div class="bg-white rounded-2xl p-12 shadow-sm border border-gray-100 text-center max-w-2xl mx-auto">
                <div class="mb-6 flex justify-center">
                    <span class="w-24 h-24 rounded-full bg-gray-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-6xl text-gray-300">event_busy</span>
                    </span>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Item Booking Kosong</h3>
                
                <p class="text-gray-500 text-sm mb-8 max-w-md mx-auto">
                    Anda belum memilih jadwal lapangan. Silakan pilih jadwal terlebih dahulu untuk melanjutkan pemesanan.
                </p>

                <a href="{{ route('home') }}" 
                   wire:navigate 
                   class="inline-flex px-8 py-4 bg-[#8B1538] hover:bg-[#6d1029] text-white font-bold rounded-2xl shadow-lg shadow-[#8B1538]/20 transition-all active:scale-[0.98]">
                    Cari Lapangan
                </a>
            </div>
        @endif
    </div>

    <!-- Voucher Modal -->
    @if($showVoucherModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             style="z-index: 9999;">
            <div class="fixed inset-0" wire:click="toggleVoucherModal"></div>
            <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl relative z-10 animate-fade-in-up">
                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Gunakan Voucher</h3>
                    <button wire:click="toggleVoucherModal" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined text-gray-500">close</span>
                    </button>
                </div>

                <div class="p-5">
                    <div class="flex gap-2">
                        <input type="text"
                               wire:model="voucherCode"
                               wire:keydown.enter="applyVoucher"
                               placeholder="Masukkan kode voucher"
                               class="w-full flex-1 h-11 px-4 rounded-xl border-gray-300 focus:border-[#8B1538] focus:ring-[#8B1538] placeholder-gray-400 text-sm uppercase tracking-wider transition-all min-w-0">
                        <button wire:click="applyVoucher"
                                class="h-11 px-4 lg:px-5 bg-[#8B1538] hover:bg-[#6d1029] text-white font-bold rounded-xl text-xs lg:text-sm transition-all active:scale-95 shrink-0 whitespace-nowrap">
                            Terapkan
                        </button>
                    </div>

                    @if($voucherError)
                        <div class="mt-3 flex items-start gap-2 text-rose-600 text-sm font-semibold bg-rose-50 p-3 rounded-xl border border-rose-100">
                            <span class="material-symbols-outlined text-base mt-0.5 shrink-0">error</span>
                            <span class="text-sm">{{ $voucherError }}</span>
                        </div>
                    @endif

                    @php $availableVouchers = $this->availableVouchers; @endphp
                    <div class="mt-5">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Voucher Tersedia</p>
                        @if($availableVouchers->count() > 0)
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                @foreach($availableVouchers as $v)
                                    @php
                                        $calculator = app(\App\Services\Voucher\VoucherCalculator::class);
                                        $potentialDiscount = $calculator->calculate($v, $totalAmount);
                                        $meetsMinOrder = $v->min_order_amount <= 0 || $totalAmount >= $v->min_order_amount;
                                    @endphp
                                    <button type="button"
                                            wire:click="selectVoucher('{{ $v->code }}')"
                                            @if(!$meetsMinOrder) disabled @endif
                                            class="w-full text-left p-3 rounded-xl border transition-all
                                                {{ $meetsMinOrder
                                                    ? 'border-gray-200 hover:border-[#8B1538] hover:bg-[#8B1538]/5 cursor-pointer'
                                                    : 'border-gray-100 bg-gray-50 opacity-60 cursor-not-allowed' }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <span class="w-9 h-9 rounded-lg bg-[#8B1538]/10 flex items-center justify-center shrink-0">
                                                    <span class="material-symbols-outlined text-[#8B1538] text-lg">confirmation_number</span>
                                                </span>
                                                <div>
                                                    <p class="text-xs font-black text-gray-800 uppercase tracking-wider">{{ $v->code }}</p>
                                                    <p class="text-[11px] text-gray-500 mt-0.5">{{ $v->description }}</p>
                                                </div>
                                            </div>
                                            @if($meetsMinOrder && $potentialDiscount > 0)
                                                <span class="text-xs font-bold text-emerald-600 whitespace-nowrap ml-2">-Rp{{ number_format($potentialDiscount, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                        @if(!$meetsMinOrder)
                                            <p class="text-[10px] text-rose-500 font-semibold mt-1 ml-12">Min. order Rp{{ number_format($v->min_order_amount, 0, ',', '.') }}</p>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div class="py-8 text-center text-gray-400">
                                <span class="material-symbols-outlined text-3xl mb-2 opacity-50">confirmation_number</span>
                                <p class="text-sm">Belum ada voucher tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Policy Modal -->
    <div x-show="showPolicyModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="z-index: 9999;">
        <div class="fixed inset-0" @click="showPolicyModal = false"></div>
        <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative z-10">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h3 class="text-lg font-black text-gray-900">Kebijakan Reschedule & Pembatalan</h3>
                <button @click="showPolicyModal = false" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-gray-500">close</span>
                </button>
            </div>
            
            <div class="p-6 space-y-5">
                @if($venuePolicy)
                    <!-- Reschedule -->
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center {{ $venuePolicy->reschedule_allowed ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                            <span class="material-symbols-outlined text-xl">event_repeat</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Reschedule</h4>
                            @if($venuePolicy->reschedule_allowed)
                                <p class="text-sm text-gray-600 mt-1">
                                    Pengajuan reschedule dapat dilakukan maksimal <strong>{{ $venuePolicy->reschedule_deadline_hours }} jam</strong> sebelum jadwal main.
                                </p>
                            @else
                                <p class="text-sm text-gray-500 mt-1">Venue ini tidak mengizinkan reschedule.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Refund -->
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center {{ $venuePolicy->refund_allowed ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                            <span class="material-symbols-outlined text-xl">currency_exchange</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Pembatalan & Refund</h4>
                            @if($venuePolicy->refund_allowed)
                                <p class="text-sm text-gray-600 mt-1">Pembatalan booking dikenakan kebijakan refund sebagai berikut:</p>
                                @php $rules = $venuePolicy->refund_rules ?? []; @endphp
                                <div class="mt-3 space-y-2">
                                    @if(isset($rules['h_minus_72']))
                                    <div class="flex items-center justify-between p-2 bg-emerald-50 rounded-lg text-xs">
                                        <span class="text-gray-600">> 72 jam sebelumnya</span>
                                        <span class="font-bold text-emerald-600">Refund {{ $rules['h_minus_72'] }}%</span>
                                    </div>
                                    @endif
                                    @if(isset($rules['h_minus_24']))
                                    <div class="flex items-center justify-between p-2 bg-amber-50 rounded-lg text-xs">
                                        <span class="text-gray-600">24 - 72 jam sebelumnya</span>
                                        <span class="font-bold text-amber-600">Refund {{ $rules['h_minus_24'] }}%</span>
                                    </div>
                                    @endif
                                    @if(isset($rules['below_24']))
                                    <div class="flex items-center justify-between p-2 bg-rose-50 rounded-lg text-xs">
                                        <span class="text-gray-600">< 24 jam sebelumnya</span>
                                        <span class="font-bold text-rose-600">Refund {{ $rules['below_24'] }}%</span>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500 mt-1">Venue ini tidak mengizinkan refund. Pembayaran bersifat final.</p>
                            @endif
                        </div>
                    </div>

                    <!-- DP -->
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl shrink-0 flex items-center justify-center {{ $venuePolicy->allow_dp ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                            <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Down Payment (DP)</h4>
                            @if($venuePolicy->allow_dp)
                                <p class="text-sm text-gray-600 mt-1">
                                    Tersedia opsi DP minimal <strong>{{ $venuePolicy->dp_min_percent }}%</strong> dari total biaya sewa. Sisa pembayaran harus dilunasi sebelum jadwal bermain.
                                </p>
                            @else
                                <p class="text-sm text-gray-500 mt-1">Venue ini tidak menyediakan opsi DP. Pembayaran harus dilakukan secara lunas.</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <span class="material-symbols-outlined text-5xl text-gray-300 mb-3">info</span>
                        <p class="text-sm text-gray-500">Kebijakan venue belum ditentukan. Hubungi pengelola venue untuk informasi lebih lanjut.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
