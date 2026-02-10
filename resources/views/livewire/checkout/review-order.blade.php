<div class="min-h-screen bg-gray-50 py-8" x-data="{ showPolicyModal: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        @if(count($selectedSlots) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                
                <!-- Left Column: Order Details -->
                <div class="lg:col-span-3 space-y-6">
                    
                    <!-- Venue Info -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h2 class="text-xl font-black text-gray-900 mb-1">{{ $venueCourt->venue->name ?? 'Venue' }}</h2>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span class="text-amber-500 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">star</span>
                                4.93
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
                        <h3 class="text-lg font-black text-gray-900 mb-6">{{ $venueCourt->name ?? 'Lapangan' }}</h3>
                        
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
                        <button type="button" wire:click="toggleVoucherModal" class="w-full flex items-center justify-between text-left">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-xl bg-[#8B1538]/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[#8B1538]">confirmation_number</span>
                                </span>
                                <span class="font-bold text-gray-800">Gunakan Voucher</span>
                            </div>
                            <span class="material-symbols-outlined text-gray-400">chevron_right</span>
                        </button>
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
                            <div class="border-t border-dashed border-gray-200 pt-3 mt-3">
                                <div class="flex justify-between">
                                    <span class="font-bold text-gray-700">Total Bayar</span>
                                    <span class="font-black text-lg text-gray-900">Rp{{ number_format($totalAmount, 0, ',', '.') }}</span>
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
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-[#8B1538] bg-[#8B1538]/5 cursor-pointer">
                                <input type="radio" name="payment_type" value="full" checked class="w-4 h-4 text-[#8B1538] border-gray-300 focus:ring-[#8B1538]">
                                <div>
                                    <span class="font-bold text-gray-800">Bayar Lunas</span>
                                    <div class="text-[#8B1538] font-bold">Rp{{ number_format($totalAmount, 0, ',', '.') }}</div>
                                </div>
                            </label>
                        </div>
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
                    <button type="button"
                            wire:click="proceedToPayment"
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
                
                <p class="text-gray-500 mb-8 max-w-md mx-auto">
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
                    <div class="flex gap-3">
                        <input type="text" 
                               wire:model="voucherCode" 
                               wire:keydown.enter="applyVoucher"
                               placeholder="Masukkan kode promo" 
                               class="flex-1 h-11 rounded-xl border-gray-300 focus:border-[#8B1538] focus:ring-[#8B1538] placeholder-gray-400 text-sm">
                        <button wire:click="applyVoucher" 
                                class="h-11 px-6 bg-[#8B1538] text-white font-bold rounded-xl hover:bg-[#6d1029] transition-colors shadow-lg shadow-[#8B1538]/20 text-sm">
                            Terapkan
                        </button>
                    </div>

                    @if($voucherError)
                        <div class="mt-3 flex items-start gap-2 text-rose-600 text-sm font-semibold bg-rose-50 p-3 rounded-lg border border-rose-100">
                            <span class="material-symbols-outlined text-base mt-0.5">error</span>
                            <span>{{ $voucherError }}</span>
                        </div>
                    @endif

                    <div class="mt-6 h-40 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center text-gray-400">
                        <!-- Placeholder for voucher list -->
                        <div class="text-center">
                            <span class="material-symbols-outlined text-3xl mb-2 opacity-50">confirmation_number</span>
                            <p class="text-sm">Belum ada voucher tersedia</p>
                        </div>
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
            
            <div class="p-6 space-y-4 text-sm text-gray-600 leading-relaxed">
                <p>
                    Proses reschedule atau pembatalan booking hanya dapat dilakukan melalui aplikasi AYO Indonesia.
                </p>
                <p>
                    Pengajuan reschedule maksimal dilakukan 24 jam sebelum jadwal main. Jika kurang dari itu, maka reschedule tidak dapat dilakukan.
                </p>
                <p>
                    Pembatalan booking akan dikenakan biaya administrasi sebesar 10% dari total biaya sewa. Dana akan dikembalikan ke saldo dompet akun Anda dalam waktu 1x24 jam.
                </p>
            </div>
        </div>
    </div>
</div>
