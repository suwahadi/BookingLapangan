<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.venues.index') }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">Daftar Venue</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('admin.venues.hub', $venue->slug) }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">{{ $venue->name }}</a>
                <span class="text-gray-300">/</span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Fasilitas</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Fasilitas <span class="text-indigo-600">Venue</span></h1>
            <p class="text-gray-500 mt-1 tracking-tight">Pilih fasilitas yang tersedia di venue ini</p>
        </div>
        <button wire:click="save" class="bg-indigo-600 text-white px-8 py-4 rounded-[1.5rem] font-black text-sm tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            SIMPAN
        </button>
    </div>

    <!-- Amenities List -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden p-8">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($amenities as $amenity)
                <button wire:click="toggle({{ $amenity->id }})" type="button"
                    class="group flex items-center gap-4 p-4 rounded-2xl border-2 transition-all duration-200 text-left hover:shadow-md
                    {{ in_array($amenity->id, $selected) 
                        ? 'border-indigo-600 bg-indigo-50/50' 
                        : 'border-gray-100 hover:border-indigo-200 bg-white' 
                    }}">
                    
                    <!-- Check Icon -->
                    <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-200 shrink-0
                        {{ in_array($amenity->id, $selected) 
                            ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 scale-110' 
                            : 'bg-gray-100 text-gray-300 group-hover:bg-indigo-100 group-hover:text-indigo-400' 
                        }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <!-- Text -->
                    <span class="text-[10px] sm:text-xs md:text-sm font-bold truncate pr-2 {{ in_array($amenity->id, $selected) ? 'text-indigo-900' : 'text-gray-600' }}">
                        {{ $amenity->name }}
                    </span>
                </button>
            @endforeach
        </div>

        @if($amenities->isEmpty())
            <div class="text-center py-10">
                <p class="text-gray-400 italic">Belum ada data fasilitas master.</p>
            </div>
        @endif
    </div>

    <!-- Selected Summary -->
    <div class="bg-gray-900 rounded-3xl p-8">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Fasilitas Terpilih</p>
                <p class="text-3xl font-black text-white mt-1">{{ count($selected) }} <span class="text-lg text-gray-400">fasilitas</span></p>
            </div>
            <button wire:click="save" class="px-8 py-4 bg-indigo-600 text-white text-sm font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>
