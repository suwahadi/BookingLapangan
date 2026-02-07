<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
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
                    
                    @if(count($selectedSlots) > 0)
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
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <span class="material-symbols-outlined text-4xl mb-2">event_busy</span>
                            <p class="font-medium">Tidak ada jadwal yang dipilih</p>
                        </div>
                    @endif

                    <!-- Add More -->
                    <button type="button" 
                            wire:click="goBack" 
                            class="mt-6 flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-[#8B1538] transition-colors">
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                        Tambah Jadwal
                    </button>
                </div>
            </div>

            <!-- Right Column: Summary & Payment -->
            <div class="lg:col-span-2 space-y-4">
                
                <!-- Voucher -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <button type="button" class="w-full flex items-center justify-between text-left">
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
                    <button type="button" class="w-full flex items-center justify-between text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-red-500">policy</span>
                            </span>
                            <span class="font-bold text-gray-800">Kebijakan Reschedule & Pembatalan</span>
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
    </div>
</div>
