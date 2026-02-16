<div x-data="{ showConfirm: false }">
    @if($booking && !$submitted)
        <div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-slate-100 dark:border-gray-700 p-4 h-full flex flex-col relative overflow-hidden">
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
            
            <div class="flex-1 flex flex-col">
                <div class="flex justify-center space-x-2 mb-4">
                    @for($i=1; $i<=5; $i++)
                        <button type="button" wire:click="$set('rating', {{ $i }})" class="focus:outline-none transition-transform hover:scale-110">
                            <svg class="w-8 h-8 {{ $rating >= $i ? 'text-yellow-400 fill-current' : 'text-slate-300 dark:text-gray-600 fill-current' }}" viewBox="0 0 24 24">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                            </svg>
                        </button>
                    @endfor
                </div>
                @error('rating') 
                    <div class="text-red-500 text-xs text-center mb-4 transition-all animate-shake">
                        {{ $message }}
                    </div> 
                @enderror

                <textarea 
                    wire:model="comment" 
                    class="w-full text-sm border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-900 dark:text-white rounded-lg focus:ring-red-500 focus:border-red-500 mb-3 resize-none" 
                    rows="3" 
                    placeholder="Tulis pengalaman bermainmu disini..."
                ></textarea>

                @error('submit') <span class="text-red-500 text-xs block mb-2">{{ $message }}</span> @enderror

                <button type="button" @click="showConfirm = true" class="w-full bg-red-600 text-white rounded-lg py-2 text-sm font-semibold hover:bg-red-700 transition-colors mt-auto">
                    Kirim Ulasan
                </button>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <template x-teleport="body">
            <div x-show="showConfirm" 
                 class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
                 role="dialog" 
                 aria-modal="true"
                 style="display: none;">
                
                <!-- Backdrop -->
                <div x-show="showConfirm"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" 
                     @click="showConfirm = false"></div>

                <!-- Modal Panel -->
                <div x-show="showConfirm"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-xl max-w-sm w-full p-6 overflow-hidden transform transition-all text-center">
                    
                    <div class="w-16 h-16 bg-red-50 dark:bg-red-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-3xl">rate_review</span>
                    </div>

                    <h3 class="text-lg font-black text-slate-900 dark:text-white uppercase italic tracking-tight mb-2">Kirim Ulasan?</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400 font-medium mb-8">
                        Apakah Anda yakin ingin mengirim ulasan ini?
                    </p>

                    <div class="grid grid-cols-2 gap-3">
                        <button @click="showConfirm = false" 
                                class="w-full py-3 px-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">
                            Batal
                        </button>
                        <button wire:click="submit" @click="showConfirm = false"
                                class="w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-colors shadow-lg shadow-red-200 dark:shadow-none">
                            Ya, Kirim
                        </button>
                    </div>
                </div>
            </div>
        </template>

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
