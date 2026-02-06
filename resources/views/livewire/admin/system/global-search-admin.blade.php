<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <div class="relative group">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 group-focus-within:text-indigo-400">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </span>
        <input type="text" 
               wire:model.live.debounce.300ms="query"
               @focus="open = true"
               placeholder="Cari booking, venue..." 
               class="w-full pl-12 pr-4 py-3 bg-gray-800 border-none rounded-2xl text-xs font-bold text-white focus:ring-2 focus:ring-indigo-600 placeholder-gray-500">
    </div>

    @if(!empty($results))
    <div x-show="open" class="absolute left-0 right-0 mt-3 bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl z-50 overflow-hidden">
        <div class="p-2 space-y-1">
            @foreach($results as $result)
                <a href="{{ $result['url'] }}" wire:navigate class="flex items-center gap-3 p-3 hover:bg-gray-700 rounded-xl transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-gray-900 flex items-center justify-center text-[8px] font-black {{ $result['type'] === 'Venue' ? 'text-emerald-400' : 'text-indigo-400' }} border border-gray-700">
                        {{ strtoupper(substr($result['type'], 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-white leading-none mb-1">{{ $result['title'] }}</p>
                        <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest">{{ $result['meta'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
