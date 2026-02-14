<div>
    @if($booking && !$submitted)
        <div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-slate-100 dark:border-gray-700 p-4 h-full flex flex-col">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Menunggu Ulasan</h3>
            <p class="text-sm text-slate-500 dark:text-gray-400 mb-4">Bagikan pengalaman bermainmu</p>
            
            <div class="flex items-start gap-4 mb-4 pb-4 border-b border-slate-50 dark:border-gray-700">
                {{-- Venue/Sport Icon --}}
                <div class="w-12 h-12 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                     <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-2xl">
                        {{ \App\Models\Venue::sportIcon($booking->court->sport ?? $booking->venue->sport_type) }}
                     </span>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 dark:text-white">{{ $booking->venue->name ?? 'Venue' }}</h4>
                    <div class="text-xs text-slate-500 dark:text-gray-400">{{ $booking->court->name ?? 'Court' }}</div>
                    <div class="text-xs text-slate-400 dark:text-gray-500 mt-1">
                        @if($booking->booking_date)
                            {{ $booking->booking_date->translatedFormat('D, d M Y') }}, 
                        @endif
                        {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="submit" class="flex-1 flex flex-col">
                <div class="flex justify-center space-x-2 mb-4">
                    @for($i=1; $i<=5; $i++)
                        <button type="button" wire:click="$set('rating', {{ $i }})" class="focus:outline-none transition-transform hover:scale-110">
                            <svg class="w-8 h-8 {{ $rating >= $i ? 'text-yellow-400 fill-current' : 'text-slate-300 dark:text-gray-600 fill-current' }}" viewBox="0 0 24 24">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                            </svg>
                        </button>
                    @endfor
                </div>

                <textarea 
                    wire:model="comment" 
                    class="w-full text-sm border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-900 dark:text-white rounded-lg focus:ring-red-500 focus:border-red-500 mb-3 resize-none" 
                    rows="3" 
                    placeholder="Tulis pengalaman bermainmu disini... (opsional)"
                ></textarea>

                @error('submit') <span class="text-red-500 text-xs block mb-2">{{ $message }}</span> @enderror

                <button type="submit" class="w-full bg-red-600 text-white rounded-lg py-2 text-sm font-semibold hover:bg-red-700 transition-colors mt-auto">
                    Kirim Ulasan
                </button>
            </form>
        </div>
    @elseif($submitted)
        <div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-slate-100 dark:border-gray-700 p-6 text-center h-full flex flex-col items-center justify-center">
             <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 mb-3">
                 <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                 </svg>
             </div>
             <h3 class="text-lg font-bold text-slate-900 dark:text-white">Terima Kasih!</h3>
             <p class="text-sm text-slate-500 dark:text-gray-400">Ulasan Anda telah berhasil dikirim.</p>
        </div>
    @endif
</div>
