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

<div class="space-y-0 pb-32 lg:pb-20">
    <!-- Header Title -->
    <div class="bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center gap-2">
                <a href="{{ route('public.venues.show', ['venue' => $venueCourt->venue->slug]) }}">
                     <span class="material-symbols-outlined text-red-600 text-2xl cursor-pointer">play_circle</span>
                </a>
                <h1 class="text-xl font-black text-text-light dark:text-text-dark tracking-tight">Pilih Jadwal</h1>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        
        <!-- Date Picker Strip -->
        <div class="bg-white dark:bg-surface-dark rounded-2xl border border-gray-100 dark:border-gray-700 p-3 sm:p-4 mb-6 sm:mb-8">
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Date Strip -->
                <div class="flex-1 flex md:grid md:grid-cols-7 gap-1 sm:gap-2 overflow-x-auto no-scrollbar scroll-smooth snap-x sm:snap-none py-1">
                    @foreach($upcomingDates as $item)
                        @php
                            $isActive = $date === $item['value'];
                        @endphp
                        <button type="button" 
                            wire:click="$set('date', '{{ $item['value'] }}')"
                            class="flex-shrink-0 min-w-[64px] sm:w-auto py-3 px-3 sm:py-4 sm:px-2 snap-center flex flex-col items-center justify-center rounded-xl sm:rounded-2xl transition-all duration-200
                            {{ $isActive 
                                ? 'bg-[#981028] text-white' 
                                : 'bg-transparent text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200' }}">
                            
                            <span class="text-[9px] sm:text-[10px] uppercase tracking-widest mb-0.5 {{ $isActive ? 'text-white/70' : 'text-gray-400' }}">{{ $item['label_day'] }}</span>
                            <span class="text-xs sm:text-sm font-black font-display {{ $isActive ? 'text-white' : 'text-gray-900 dark:text-gray-200' }}">{{ $item['label_date'] }}</span>
                        </button>
                    @endforeach
                </div>

                <!-- Vertical Divider -->
                <div class="hidden md:block w-px h-12 bg-gray-200 dark:bg-gray-700 mx-1"></div>

                <!-- Calendar Button -->
                <div class="relative flex-shrink-0 ml-auto md:ml-0 pl-2 md:pl-0 border-l md:border-l-0 border-gray-200 dark:border-gray-700 h-12 flex items-center">
                    <input type="date" 
                           wire:model.live="date" 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                           min="{{ now()->format('Y-m-d') }}">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:border-[#981028] hover:text-[#981028] transition-all bg-white dark:bg-gray-800 cursor-pointer">
                        <span class="material-symbols-outlined text-lg sm:text-xl">calendar_month</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 lg:gap-12 items-start">
            
            <!-- Left Column: Info & Slots -->
            <div class="lg:col-span-3 space-y-6 sm:space-y-8">
                
                <!-- Court Info Header -->
                <div>
                     <div class="flex items-center gap-2 mb-2">
                        <h2 class="text-lg font-black text-gray-900 dark:text-white">{{ $venueCourt->name }}</h2>
                        <span class="material-symbols-outlined text-gray-400 text-sm">chevron_right</span>
                     </div>

                     <div class="flex flex-wrap gap-4 text-xs font-bold text-gray-500">
                        <div class="flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">{{ \App\Models\Venue::sportIcon($venueCourt->sport ?? '') }}</span>
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
                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#981028] text-white rounded-xl">
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
                            $originalAmount = $amount > 0 ? $amount + 10000 : 0;
                        @endphp

                        @if($slot['is_available'] && $amount > 0)
                            <button type="button"
                                    wire:click="toggleSelect({{ $i }})"
                                    wire:loading.attr="disabled"
                                    class="relative p-4 rounded-2xl border-2 transition-all duration-200 text-center group
                                    {{ $isSelected 
                                        ? 'bg-[#981028] border-[#981028] ring-2 ring-[#981028]/20' 
                                        : 'bg-white dark:bg-gray-800 hover:border-red-200 border-gray-100 dark:border-gray-700' 
                                    }}">
                                
                                <div class="text-[10px] tracking-wider mb-1.5 {{ $isSelected ? 'text-white/50' : 'text-gray-400' }}">60 Menit</div>
                                @if($isSelected)
                                    <div class="absolute top-2.5 right-2.5">
                                        <span class="material-symbols-outlined text-white text-base" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    </div>
                                @endif

                                <div class="text-sm font-bold mb-2 {{ $isSelected ? 'text-white' : 'text-gray-900 dark:text-white' }}">
                                    {{ $slot['start'] }} - {{ $slot['end'] }}
                                </div>
                                
                                <div class="text-xs font-medium {{ $isSelected ? 'text-white/80' : 'text-gray-500' }}">
                                    Rp{{ number_format($amount, 0, ',', '.') }}
                                </div>
                            </button>
                        @elseif($slot['is_available'] && $amount <= 0)
                             <div class="p-4 rounded-2xl border-2 border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 text-center cursor-not-allowed">
                                <div class="text-[10px] tracking-wider mb-1.5 text-gray-300">60 Menit</div>
                                <div class="text-sm font-bold mb-2 text-gray-300">
                                    {{ $slot['start'] }} - {{ $slot['end'] }}
                                </div>
                                <div class="text-xs font-medium text-gray-300">Booked</div>
                            </div>
                        @else
                            <div class="p-4 rounded-2xl border-2 border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 text-center cursor-not-allowed">
                                <div class="text-[10px] tracking-wider mb-1.5 text-gray-300">60 Menit</div>
                                <div class="text-sm font-bold mb-2 text-gray-400">
                                    {{ $slot['start'] }} - {{ $slot['end'] }}
                                </div>
                                <div class="text-xs font-medium text-gray-300">Booked</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Right Column: Sticky Summary (Desktop) -->
            <div class="hidden lg:block lg:col-span-1">
                @if(!empty($selectedIndexes))
                    <div class="lg:sticky lg:top-8 bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-xl border border-gray-100 dark:border-gray-700">
                        <h3 class="font-black text-lg text-gray-600 dark:text-gray-300 mb-6">Ringkasan</h3>
                        
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
                                        <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $s }} - {{ $e }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-black text-red-600">Rp {{ number_format($blockTotal, 0, ',', '.') }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $count }} Jam</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-dashed border-gray-200 dark:border-gray-600 pt-4 mb-6">
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
                            class="w-full py-3 bg-[#981028] hover:bg-red-800 text-white font-bold rounded-xl transition-all active:scale-95">
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

    <!-- Mobile Sticky Bottom Bar -->
    @if(!empty($selectedIndexes))
        <div class="fixed bottom-0 left-0 right-0 z-50 lg:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] px-4 py-3 safe-area-bottom">
            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <div class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Total ({{ count($selectedIndexes) }} slot)</div>
                    <div class="text-xl font-black text-red-600 tracking-tight">Rp {{ number_format($this->selectedTotal, 0, ',', '.') }}</div>
                </div>
                <button type="button"
                    wire:click="confirmSelection" 
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-wait"
                    wire:target="confirmSelection"
                    class="flex-shrink-0 px-6 py-3 bg-[#981028] hover:bg-red-800 text-white font-bold text-sm rounded-xl transition-all active:scale-95 flex items-center gap-2">
                    <span wire:loading.remove wire:target="confirmSelection">Lanjut Bayar</span>
                    <span wire:loading wire:target="confirmSelection" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Proses...
                    </span>
                    <span class="material-symbols-outlined text-lg" wire:loading.remove wire:target="confirmSelection">arrow_forward</span>
                </button>
            </div>
        </div>
    @endif
</div>
