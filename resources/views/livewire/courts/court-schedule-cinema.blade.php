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

<div class="max-w-5xl mx-auto px-4 py-6">
    <div class="mb-4">
        <a href="{{ route('public.venues.show', ['venue' => $venueCourt->venue->id]) }}" class="text-sm text-indigo-600 hover:underline mb-2 inline-block">&larr; Kembali ke detail venue</a>
        <h1 class="text-xl font-semibold">
            Jadwal {{ $venueCourt->name }} - {{ $venueCourt->venue->name }}
        </h1>
        <p class="text-sm text-gray-600">
            Cabang olahraga: {{ $venueCourt->sport }}
            @if($venueCourt->floor_type)
                • Jenis lantai: {{ $venueCourt->floor_type }}
            @endif
        </p>
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="bg-white rounded-2xl shadow-sm p-4 mb-6 border border-gray-100">
        <div class="flex items-center justify-between gap-4">
            <!-- Scrollable Dates -->
            <div class="flex-1 flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                @foreach($upcomingDates as $item)
                    @php
                        $isActive = $date === $item['value'];
                    @endphp
                    <button type="button" 
                        wire:click="$set('date', '{{ $item['value'] }}')"
                        class="flex-shrink-0 flex flex-col items-center justify-center w-16 h-16 rounded-2xl transition-all duration-200 border border-transparent
                               {{ $isActive 
                                    ? 'bg-rose-800 text-white shadow-lg shadow-rose-200 scale-105 font-bold' 
                                    : 'hover:bg-gray-50 text-gray-500 hover:border-gray-200' 
                               }}">
                        <span class="text-[10px] uppercase tracking-wide opacity-80 mb-0.5">{{ $item['label_day'] }}</span>
                        <span class="text-sm {{ $isActive ? 'font-black' : 'font-bold' }}">{{ $item['label_date'] }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Custom Date Picker Trigger -->
            <div class="pl-4 border-l border-gray-100 relative shrink-0">
                <input type="date" 
                       wire:model.live="date" 
                       class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10"
                >
                <button class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors border border-dashed border-gray-300">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between">
             <div class="text-xs text-gray-400 font-medium uppercase tracking-widest">
                Jadwal Lapangan
            </div>
             <div class="text-xs text-gray-400">
                Klik slot di bawah untuk memilih
            </div>
        </div>

        @if($errorMessage)
            <div class="mt-3 p-3 rounded-xl bg-red-50 text-red-700 text-sm font-medium border border-red-100">
                {{ $errorMessage }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2 bg-white rounded-lg shadow p-4">
            <div class="mb-3 flex items-center justify-between">
                <div class="text-sm font-semibold">Pilih Jam</div>
                <div class="text-xs text-gray-500">
                    Slot tidak tersedia ditandai abu-abu.
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($timeSlots as $i => $slot)
                    @php
                        $isSelected = in_array($i, $selectedIndexes, true);
                        $key = $slot['start'] . '|' . $slot['end'];
                        $amount = $slotAmounts[$key] ?? null;
                    @endphp

                    @if($slot['is_available'])
                        <button type="button"
                                class="relative border rounded-lg px-3 py-3 text-left hover:bg-gray-50 transition-colors
                                       {{ $isSelected ? 'bg-gray-900 text-white border-gray-900 hover:bg-gray-800 ring-2 ring-indigo-500 ring-offset-2' : '' }}"
                                wire:click="toggleSelect({{ $i }})"
                                wire:loading.attr="disabled">
                            
                            @if($isSelected)
                                <div class="absolute -top-2 -right-2 bg-green-500 rounded-full p-1 shadow-md z-10">
                                     <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                                     </svg>
                                </div>
                            @endif

                            <div class="font-medium">{{ $slot['start'] }} - {{ $slot['end'] }}</div>

                            <div class="text-xs {{ $isSelected ? 'text-gray-200' : 'text-gray-600' }}">
                                @if(!is_null($amount))
                                    Rp {{ number_format($amount, 0, ',', '.') }}
                                @else
                                    Harga belum tersedia
                                @endif
                            </div>
                        </button>
                    @else
                        <div class="border rounded-lg px-3 py-3 text-left bg-gray-100 text-gray-400 opacity-60 cursor-not-allowed">
                            <div class="font-medium">{{ $slot['start'] }} - {{ $slot['end'] }}</div>
                            <div class="text-xs">Tidak tersedia</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 h-fit">
            <div class="text-sm font-semibold mb-3">Ringkasan</div>

            @if(empty($selectedIndexes))
                <div class="text-sm text-gray-600">
                    Belum ada jam yang dipilih.
                </div>
            @else
                <div class="text-sm text-gray-700 space-y-2">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 2v3M17 2v3M3 9h18M5 5h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"
                                  stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <div>
                            <div class="text-gray-500 text-xs">Tanggal</div>
                            <div class="font-medium">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 6v6l4 2M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0Z"
                                  stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <div class="w-full">
                            <div class="text-gray-500 text-xs mb-1">Jam Terpilih</div>
                            <ul class="space-y-1">
                            @foreach($displayBlocks as $block)
                                @php
                                    $min = min($block);
                                    $max = max($block);
                                    $s = $timeSlots[$min]['start'];
                                    $e = $timeSlots[$max]['end'];
                                @endphp
                                <li class="font-medium bg-gray-50 px-2 py-1 rounded text-xs flex justify-between">
                                    <span>{{ $s }} - {{ $e }}</span>
                                </li>
                            @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="border-t pt-3 mt-2">
                        <div class="text-gray-500 text-xs">Total</div>
                        <div class="text-lg font-semibold">
                            Rp {{ number_format($this->selectedTotal, 0, ',', '.') }}
                        </div>
                    </div>
                    
                    <button type="button"
                            class="w-full mt-4 bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700 disabled:opacity-50 font-medium"
                            wire:click="confirmSelection"
                            wire:loading.attr="disabled">
                        Booking Sekarang
                    </button>
                    
                    <div class="text-xs text-gray-500 mt-2 text-center">
                        Konfirmasi booking untuk lanjut ke pembayaran.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
