<div>
    @if($booking && !$submitted)
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4 mb-6">
            <h3 class="text-lg font-bold text-slate-900 mb-1">Check This Out!</h3>
            <p class="text-sm text-slate-500 mb-4">Let's review your venue booking!</p>
            
            <div class="flex items-start gap-4 mb-4 pb-4 border-b border-slate-50">
                {{-- Venue/Sport Icon --}}
                <div class="w-12 h-12 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                     @if(isset($booking->venue->sport_icon))
                        <span class="material-symbols-rounded">{{ $booking->venue->sport_icon }}</span> 
                     @else
                        <span class="material-symbols-rounded">emoji_events</span>
                     @endif
                </div>
                <div>
                    <h4 class="font-bold text-slate-900">{{ $booking->venue->name ?? 'Venue' }}</h4>
                    <div class="text-xs text-slate-500">{{ $booking->court->name ?? 'Court' }}</div>
                    <div class="text-xs text-slate-400 mt-1">
                        @if($booking->booking_date)
                            {{ $booking->booking_date->translatedFormat('D, d M Y') }}, 
                        @endif
                        {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="submit">
                <div class="flex justify-center space-x-2 mb-4">
                    @for($i=1; $i<=5; $i++)
                        <button type="button" wire:click="$set('rating', {{ $i }})" class="focus:outline-none transition-transform hover:scale-110">
                            <svg class="w-8 h-8 {{ $rating >= $i ? 'text-yellow-400 fill-current' : 'text-slate-300 fill-current' }}" viewBox="0 0 24 24">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                            </svg>
                        </button>
                    @endfor
                </div>

                <textarea 
                    wire:model="comment" 
                    class="w-full text-sm border-slate-200 rounded-lg focus:ring-red-500 focus:border-red-500 mb-3" 
                    rows="3" 
                    placeholder="Tulis pengalaman bermainmu disini... (opsional)"
                ></textarea>

                @error('submit') <span class="text-red-500 text-xs block mb-2">{{ $message }}</span> @enderror

                <button type="submit" class="w-full bg-red-600 text-white rounded-lg py-2 text-sm font-semibold hover:bg-red-700 transition-colors">
                    Kirim Ulasan
                </button>
            </form>
        </div>
    @elseif($submitted)
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6 text-center">
             <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-600 mb-3">
                 <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                 </svg>
             </div>
             <h3 class="text-lg font-bold text-slate-900">Terima Kasih!</h3>
             <p class="text-sm text-slate-500">Ulasan Anda telah berhasil dikirim.</p>
        </div>
    @endif
</div>
