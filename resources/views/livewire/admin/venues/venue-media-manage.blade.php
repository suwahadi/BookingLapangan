<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.venues.index') }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">Daftar Venue</a>
                <span class="text-gray-300">/</span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ $venue->name }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Galeri <span class="text-indigo-600">Media</span></h1>
            <p class="text-gray-500 mt-1 tracking-tight">Unggah foto-foto terbaik untuk menarik minat pelanggan</p>
        </div>
        
        <div class="relative group">
            <input type="file" wire:model="photos" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
            <button class="bg-gray-900 text-white px-8 py-4 rounded-[1.5rem] font-black text-sm tracking-widest hover:bg-black transition-all transform active:scale-95 shadow-2xl shadow-indigo-100 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                UNGGAH FOTO BARU
            </button>
        </div>
    </div>

    <!-- Upload Progress -->
    <div wire:loading wire:target="photos" class="w-full bg-indigo-50 p-6 rounded-[2rem] border-2 border-dashed border-indigo-200 text-center">
        <div class="flex items-center justify-center gap-3">
            <svg class="animate-spin h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-black text-indigo-600 uppercase tracking-widest">Sedang mengunggah foto... mohon tunggu</span>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8" x-data="{ sortable: null }" x-init="
        sortable = new Sortable($el, {
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: (evt) => {
                let ids = Array.from($el.querySelectorAll('[data-id]')).map(el => el.dataset.id);
                $wire.reorder(ids);
            }
        });
    ">
        @forelse($media as $m)
        <div data-id="{{ $m->id }}" class="group relative aspect-[4/3] bg-white rounded-[2.5rem] overflow-hidden shadow-xl border border-gray-50 flex items-center justify-center">
            <img src="{{ asset('storage/' . $m->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            
            <!-- Overlay Info -->
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-6">
                <div class="flex items-center justify-between gap-2">
                    <button wire:click="deleteMedia({{ $m->id }})" class="p-3 bg-rose-500/20 backdrop-blur-md text-white border border-rose-500/30 rounded-2xl hover:bg-rose-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                    
                    @if(!$m->is_cover)
                    <button wire:click="setCover({{ $m->id }})" class="px-4 py-2 bg-white/20 backdrop-blur-md text-white border border-white/30 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-white hover:text-gray-900 transition-all">
                        SET SAMPUL
                    </button>
                    @endif
                </div>
            </div>

            <!-- Badges -->
            <div class="absolute top-4 left-4 flex flex-col gap-2">
                @if($m->is_cover)
                <span class="px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/30 ring-2 ring-emerald-400/50">SAMPUL</span>
                @endif
                <div class="w-8 h-8 bg-white/20 backdrop-blur-md rounded-lg flex items-center justify-center text-white cursor-move opacity-0 group-hover:opacity-100 transition-opacity" title="Geser untuk urutkan">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" /></svg>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-32 text-center bg-white rounded-[4rem] border-2 border-dashed border-gray-100 flex flex-col items-center gap-4">
            <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center text-gray-200">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <div>
                <p class="text-gray-900 font-black uppercase tracking-widest">Belum Ada Foto</p>
                <p class="text-gray-400 font-bold mt-1 tracking-tight">Klik tombol "UNGGAH FOTO" untuk mulai mengisi galeri.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush
