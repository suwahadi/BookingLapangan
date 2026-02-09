@php
    // Helper to group indexes for display
    $displayBlocks = [];
    if (!empty($selectedIndexes)) {
        $sorted = $selectedIndexes;
        sort($sorted);
        $currentBlock = [];
        $prevIndex = null;
        
        foreach ($sorted as $index) {
            if ($prevIndex === null || $index === $prevIndex + 1) {
                $currentBlock[] = $index;
            } else {
                $displayBlocks[] = $currentBlock;
                $currentBlock = [$index];
            }
            $prevIndex = $index;
        }
        if (!empty($currentBlock)) {
            $displayBlocks[] = $currentBlock;
        }
    }
@endphp

<div class="space-y-0 pb-20">
    <!-- Header Title -->
    <div class="bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center gap-2">
                <a href="{{ route('public.venues.show', ['venue' => $venueCourt->venue->slug]) }}">
                     <span class="material-symbols-outlined text-red-600 text-2xl cursor-pointer">play_circle</span>
                </a>
                <h1 class="text-xl font-black text-text-light dark:text-text-dark tracking-tight">Pilih Lapangan</h1>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Date Picker Strip -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-8">
            <div class="flex items-center gap-3">
                <!-- Date Strip -->
                <div class="flex-1 flex md:grid md:grid-cols-7 gap-2 overflow-x-auto no-scrollbar scroll-smooth snap-x sm:snap-none">
                    @foreach($upcomingDates as $item)
                        @php
                            $isActive = $date === $item['value'];
                        @endphp
                        <button type="button" 
                            wire:click="$set('date', '{{ $item['value'] }}')"
                            class="flex-shrink-0 w-[22%] md:w-auto h-20 snap-center flex flex-col items-center justify-center rounded-2xl transition-all duration-300 relative overflow-hidden group
                            {{ $isActive 
                                ? 'bg-[#981028] text-white shadow-lg shadow-red-900/20 scale-100 ring-2 ring-red-900/10' 
                                : 'bg-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                            
                            <span class="text-[10px] uppercase tracking-widest mb-1 {{ $isActive ? 'text-white/70' : 'text-gray-400 group-hover:text-gray-500' }}">{{ $item['label_day'] }}</span>
                            <span class="text-sm font-black font-display {{ $isActive ? 'text-white' : 'text-gray-900' }}">{{ $item['label_date'] }}</span>
                            
                            @if($isActive)
                                <div class="absolute bottom-1 w-1 h-1 rounded-full bg-white/50"></div>
                            @endif
                        </button>
                    @endforeach
                </div>

                <!-- Vertical Divider -->
                <div class="hidden md:block w-px h-12 bg-gray-200 mx-2"></div>

                <!-- Calendar Button -->
                <div class="relative flex-shrink-0 ml-auto md:ml-0 pl-2 md:pl-0 border-l md:border-l-0 border-gray-200 h-12 flex items-center">
                    <input type="date" 
                           wire:model.live="date" 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                           min="{{ now()->format('Y-m-d') }}">
                    <div class="w-12 h-12 rounded-xl border-2 border-gray-200 flex items-center justify-center text-gray-600 hover:border-[#981028] hover:text-[#981028] transition-all bg-white group cursor-pointer">
                        <span class="material-symbols-outlined text-xl group-hover:scale-110 transition-transform">calendar_month</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 items-start">
            
            <!-- Left Column: Info & Slots -->
            <div class="lg:col-span-3 space-y-8">
                
                <!-- Court Info Header -->
                <div>
                     <div class="flex items-center gap-2 mb-2">
                        <h2 class="text-lg font-black text-gray-900 dark:text-white">{{ $venueCourt->name }}</h2>
                        <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                     </div>
                     <p class="text-sm font-medium text-gray-500 mb-4">{{ $venueCourt->description ?? 'Lapangan dengan fasilitas terbaik' }}</p>
                     
                     <div class="flex flex-wrap gap-4 text-xs font-bold text-gray-500">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">sports_tennis</span>
                            {{ $venueCourt->sport }}
                        </div>
                         <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">roofing</span>
                            Indoor
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">texture</span>
                            {{ $venueCourt->floor_type ?? 'Karpet' }}
                        </div>
                     </div>
                </div>

                <!-- Availability Badge -->
                <div class="inline-flex items-center gap-2 px-6 py-3 bg-[#981028] text-white rounded-2xl shadow-lg shadow-red-900/20">
                     <span class="text-sm font-bold">{{ collect($timeSlots)->where('is_available', true)->count() }} Jadwal Tersedia</span>
                     <span class="material-symbols-outlined text-lg">expand_circle_up</span>
                </div>

                @if($errorMessage)
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 text-sm font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined">error</span>
                        {{ $errorMessage }}
                    </div>
                @endif

                <!-- Slots Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($timeSlots as $i => $slot)
                        @php
                            $isSelected = in_array($i, $selectedIndexes, true);
                            $key = $slot['start'] . '|' . $slot['end'];
                            $amount = $slotAmounts[$key] ?? 0;
                            // Dummy original price for visual effect if amount > 0
                            $originalAmount = $amount > 0 ? $amount + 10000 : 0;
                        @endphp

                        @if($slot['is_available'] && $amount > 0)
                            <button type="button"
                                    wire:click="toggleSelect({{ $i }})"
                                    wire:loading.attr="disabled"
                                    class="relative p-3 rounded-xl border transition-all duration-200 text-center group
                                    {{ $isSelected 
                                        ? 'bg-red-50 border-red-200' 
                                        : 'bg-white hover:border-red-200 border-gray-100' 
                                    }}">
                                
                                <div class="text-[10px] uppercase tracking-wider mb-2 {{ $isSelected ? 'text-red-700' : 'text-gray-400' }}">60 Menit</div>
                                @if($isSelected)
                                    <div class="absolute top-2 right-2">
                                        <span class="material-symbols-outlined text-red-600 text-sm">check_circle</span>
                                    </div>
                                @endif

                                <div class="text-sm font-black mb-3 {{ $isSelected ? 'text-red-900' : 'text-gray-900' }}">
                                    {{ $slot['start'] }} - {{ $slot['end'] }}
                                </div>
                                
                                <div class="space-y-0.5">
                                    <div class="text-[10px] text-gray-300 line-through">Rp{{ number_format($originalAmount, 0, ',', '.') }}</div>
                                    <div class="text-sm font-black {{ $isSelected ? 'text-red-600' : 'text-gray-500' }}">
                                        Rp{{ number_format($amount, 0, ',', '.') }}
                                    </div>
                                </div>
                            </button>
                        @elseif($slot['is_available'] && $amount <= 0)
                             <div class="p-3 rounded-xl border border-gray-100 bg-gray-50/50 text-center opacity-40 cursor-not-allowed">
                                <div class="text-[10px] uppercase tracking-wider mb-2 text-gray-300">60 Menit</div>
                                <div class="text-sm font-black mb-3 text-gray-400">
                                    {{ $slot['start'] }} - {{ $slot['end'] }}
                                </div>
                                <div class="text-sm font-black text-gray-300">-</div>
                            </div>
                        @else
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 text-center opacity-60 cursor-not-allowed">
                                <div class="text-[10px] uppercase tracking-wider mb-2 text-gray-300">60 Menit</div>
                                <div class="text-sm font-black mb-3 text-gray-400 line-through">
                                    {{ $slot['start'] }} - {{ $slot['end'] }}
                                </div>
                                <div class="text-xs text-gray-300 uppercase">Booked</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Right Column: Sticky Summary -->
            <div class="lg:col-span-1">
                @if(!empty($selectedIndexes))
                    <div class="lg:sticky lg:top-8 bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl border border-gray-100">
                        <h3 class="font-black text-lg text-gray-600 mb-6">Ringkasan</h3>
                        
                        <div class="space-y-4 mb-6">
                            @foreach($displayBlocks as $block)
                                @php
                                    $min = min($block);
                                    $max = max($block);
                                    $s = $timeSlots[$min]['start'];
                                    $e = $timeSlots[$max]['end'];
                                    $count = count($block);
                                    $blockTotal = 0;
                                    foreach($block as $idx) {
                                         $k = $timeSlots[$idx]['start'] . '|' . $timeSlots[$idx]['end'];
                                         $blockTotal += ($slotAmounts[$k] ?? 0);
                                    }
                                @endphp
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="text-sm font-bold text-gray-800">{{ $s }} - {{ $e }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-black text-red-600">Rp {{ number_format($blockTotal, 0, ',', '.') }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $count }} Jam</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-4 mb-6">
                             <div class="flex justify-between items-end">
                                <span class="text-xs text-gray-500">Total</span>
                                <span class="text-2xl font-black text-red-600">Rp {{ number_format($this->selectedTotal, 0, ',', '.') }}</span>
                             </div>
                        </div>

                        <button type="button"
                            wire:click="confirmSelection" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-wait"
                            wire:target="confirmSelection"
                            class="w-full py-3 bg-[#981028] hover:bg-red-800 text-white font-bold rounded-xl shadow-lg shadow-red-900/20 transition-all active:scale-95">
                            <span wire:loading.remove wire:target="confirmSelection">Lanjut Pembayaran</span>
                            <span wire:loading wire:target="confirmSelection" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
