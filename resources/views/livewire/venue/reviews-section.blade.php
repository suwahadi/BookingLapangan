<div class="mt-8 space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <h2 class="text-2xl font-black text-text-light dark:text-text-dark uppercase italic">Ulasan</h2>
            <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 px-4 py-1.5 rounded-full border border-gray-200 dark:border-gray-700">
                <span class="material-symbols-outlined text-amber-500 text-lg fill-current">star</span>
                <span class="font-black text-lg text-text-light dark:text-text-dark">{{ number_format($ratingAvg ?? 0, 1) }}</span>
                <span class="text-xs font-bold text-muted-light uppercase tracking-wider">({{ $ratingCount ?? 0 }} ulasan)</span>
            </div>
        </div>
        
        @if($ratingCount > 4)
            <button wire:click="$dispatch('open-reviews-modal')" class="hidden md:flex items-center gap-2 text-xs font-black text-primary hover:text-primary-dark uppercase tracking-widest transition-colors group">
                Lihat Semua
                <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </button>
        @endif
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($reviews as $review)
            <div class="p-4 border border-slate-200 rounded-lg">
               <div class="flex items-center space-x-3 mb-2">
                   {{-- Avatar --}}
                   @if($review->user->profile_photo_path ?? false)
                        <img src="{{ Storage::url($review->user->profile_photo_path) }}" class="w-10 h-10 rounded-full object-cover" alt="{{ $review->user->name }}" />
                   @else
                        <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold uppercase">
                            {{ substr($review->user->name, 0, 1) }}
                        </div>
                   @endif
                   
                   <div>
                       <div class="font-medium text-slate-900">{{ $review->user->name }}</div>
                       <div class="text-xs text-slate-400">Diulas: {{ $review->created_at->translatedFormat('d F Y') }}</div>
                   </div>
                   <div class="ml-auto flex items-center bg-yellow-100 px-2 py-0.5 rounded text-xs font-semibold">
                       <svg class="w-3 h-3 text-yellow-600 mr-1 fill-current" viewBox="0 0 24 24">
                           <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                       </svg>
                       {{ number_format($review->rating, 1) }}
                   </div>
               </div>
               <p class="text-slate-600 text-sm leading-relaxed">{{ $review->comment }}</p>
            </div>
        @empty
            <div class="col-span-full text-center py-8 text-slate-500">
                Belum ada ulasan untuk venue ini.
            </div>
        @endforelse
    </div>

    @if($ratingCount > 4)
        <div class="mt-8 text-center">
            <button wire:click="$dispatch('open-reviews-modal')" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-white dark:bg-gray-800 border-2 border-primary text-primary hover:bg-primary hover:text-white rounded-xl text-sm font-black uppercase tracking-widest transition-all hover:shadow-lg hover:-translate-y-1">
                Lihat Lebih Banyak
                <span class="material-symbols-outlined text-lg">expand_more</span>
            </button>
        </div>
    @endif

    <livewire:venue.reviews-modal :venue-id="$venueId" />
</div>
