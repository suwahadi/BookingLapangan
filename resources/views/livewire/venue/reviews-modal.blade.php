<div>
    @if($showModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden z-20">
                <div class="flex items-center justify-between p-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Semua Ulasan</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="p-4 overflow-y-auto flex-1 space-y-4">
                    @foreach($reviews as $review)
                        <div class="border-b border-slate-100 pb-4 last:border-0">
                           <div class="flex items-center space-x-3 mb-2">
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
                           <p class="text-slate-600 text-sm">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                    
                    <div class="pt-4">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
