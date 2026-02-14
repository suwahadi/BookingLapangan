<div>
    @if($showModal)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden z-20 animate-scale-up">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 sticky top-0 z-30">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase italic tracking-tight">Semua Ulasan</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">Total {{ $total }} ulasan dari pemain</p>
                    </div>
                    <button wire:click="closeModal" class="w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <!-- Scrollable Content -->
                <div class="p-6 overflow-y-auto flex-1 space-y-6 custom-scrollbar" id="reviews-container">
                    @foreach($reviews as $review)
                        <div class="border-b border-gray-100 dark:border-gray-700 pb-6 last:border-0 last:pb-0 fade-in-up" wire:key="review-{{ $review->id }}">
                           <div class="flex items-start gap-4 mb-3">
                               <div class="shrink-0">
                                   @if($review->user->profile_photo_path ?? false)
                                        <img src="{{ Storage::url($review->user->profile_photo_path) }}" class="w-12 h-12 rounded-2xl object-cover shadow-sm ring-2 ring-white dark:ring-gray-700" alt="{{ $review->user->name }}" />
                                   @else
                                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-black uppercase text-lg shadow-sm ring-2 ring-white dark:ring-gray-700">
                                            {{ substr($review->user->name, 0, 1) }}
                                        </div>
                                   @endif
                               </div>
                               
                               <div class="flex-1 min-w-0">
                                   <div class="flex items-center justify-between gap-2 mb-1">
                                       <h4 class="font-bold text-gray-900 dark:text-white truncate">{{ $review->user->name }}</h4>
                                       <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800">
                                           <span class="material-symbols-outlined text-sm fill-current">star</span>
                                           <span class="text-xs font-black">{{ number_format($review->rating, 1) }}</span>
                                       </span>
                                   </div>
                                   <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                       Diulas: {{ $review->created_at->translatedFormat('d F Y') }}
                                   </p>
                                   <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">{{ $review->comment }}</p>
                               </div>
                           </div>
                        </div>
                    @endforeach
                    
                    @if($hasMore)
                        <div class="pt-4 pb-2 flex justify-center">
                            <button wire:click="loadMore" 
                                    wire:loading.attr="disabled"
                                    class="group flex items-center gap-2 px-6 py-3 rounded-2xl bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 font-bold text-xs uppercase tracking-widest transition-all">
                                <span wire:loading.remove wire:target="loadMore">Muat Lebih Banyak</span>
                                <span wire:loading wire:target="loadMore" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memuat...
                                </span>
                                <span class="material-symbols-outlined text-lg group-hover:translate-y-0.5 transition-transform" wire:loading.remove wire:target="loadMore">expand_more</span>
                            </button>
                        </div>
                    @else
                        <div class="pt-6 pb-2 text-center">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Semua ulasan telah ditampilkan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 20px;
            border: 2px solid transparent;
            background-clip: content-box;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.8);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }
    </style>
</div>
