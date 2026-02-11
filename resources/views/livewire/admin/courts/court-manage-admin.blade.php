<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.venues.index') }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">Daftar Venue</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('admin.venues.hub', $venue->slug) }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">{{ $venue->name }}</a>
                <span class="text-gray-300">/</span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Lapangan</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Kelola <span class="text-indigo-600">Lapangan</span></h1>
            <p class="text-gray-500 mt-1 tracking-tight">Manajemen unit lapangan untuk {{ $venue->name }}</p>
        </div>
        <button wire:click="openCreateModal" class="bg-gray-900 text-white px-8 py-4 rounded-[1.5rem] font-black text-sm tracking-widest hover:bg-black transition-all transform active:scale-95 shadow-2xl shadow-gray-200 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            TAMBAH LAPANGAN
        </button>
    </div>

    <!-- Courts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($courts as $court)
        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-50 relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-6">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-display font-black text-lg shadow-lg shadow-indigo-200">
                        {{ substr($court->name, 0, 1) }}
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="edit({{ $court->id }})" class="p-2 text-gray-400 hover:text-indigo-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </button>
                    </div>
                </div>

                <h3 class="text-xl font-black text-gray-900 mb-1">{{ $court->name }}</h3>
                <p class="text-[10px] font-bold text-gray-400 mb-6">{{ $court->sport }} • {{ $court->floor_type ?? 'Standard' }}</p>

                <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                    <div class="flex items-center gap-2">
                        <div @click="$wire.toggleStatus({{ $court->id }})" class="relative inline-flex items-center cursor-pointer select-none transition-opacity hover:opacity-80">
                            <div class="w-10 h-5 rounded-full transition-colors duration-200 {{ $court->is_active ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>
                            <div class="absolute left-1 top-1 bg-white w-3 h-3 rounded-full shadow-sm transition-transform duration-200 {{ $court->is_active ? 'translate-x-5' : 'translate-x-0' }}"></div>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest {{ $court->is_active ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ $court->is_active ? 'HIDUP' : 'MATI' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.courts.blackouts', $court->id) }}" wire:navigate class="text-[10px] font-black text-rose-500 uppercase tracking-widest hover:text-gray-900 transition-colors">
                            BLACKOUT
                        </a>
                        <a href="{{ route('admin.courts.pricing', $court->id) }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-widest hover:text-gray-900 transition-colors">
                            ATUR HARGA
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100 italic text-gray-400">
            Belum ada lapangan terdaftar untuk venue ini.
        </div>
        @endforelse
    </div>

    <!-- Modal Form -->
    <div x-data="{ open: @entangle('showModal') }" x-show="open" x-cloak class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                class="inline-block px-10 py-12 overflow-hidden text-left align-bottom transition-all transform bg-white shadow-3xl rounded-[3rem] sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                
                <div class="mb-10 text-center">
                    <h3 class="text-3xl font-black text-gray-900 font-display italic uppercase tracking-tight">
                        {{ $isEdit ? 'Sunting' : 'Tambah' }} <span class="text-indigo-600">Lapangan</span>
                    </h3>
                    <p class="text-gray-400 font-bold mt-1 tracking-tight">Lengkapi detail unit lapangan di bawah ini.</p>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Nama Lapangan</label>
                        <input wire:model="name" type="text" placeholder="Contoh: Lapangan A (Indoor)" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                        @error('name') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Cabang Olahraga</label>
                            <select wire:model="sport" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                                <option value="">Pilih Cabang</option>
                                <option value="Futsal">Futsal</option>
                                <option value="Badminton">Badminton</option>
                                <option value="Basket">Basket</option>
                                <option value="Mini Soccer">Mini Soccer</option>
                                <option value="Tennis">Tennis</option>
                                <option value="Voli">Voli</option>
                                <option value="Padel">Padel</option>
                            </select>
                            @error('sport') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Jenis Lantai</label>
                            <input wire:model="floor_type" type="text" placeholder="Vinyl / Semen" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 py-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 transition-colors"></div>
                            <span class="ml-3 text-[10px] font-black text-gray-900 uppercase tracking-widest">Status Aktif</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-6 pt-6">
                        <button type="button" @click="open = false" class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-900 transition-colors">BATAL</button>
                        <button type="submit" class="bg-indigo-600 text-white px-10 py-5 rounded-2xl font-black text-[11px] tracking-[0.3em] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 flex items-center gap-2">
                            <span wire:loading.remove>SIMPAN DATA</span>
                            <span wire:loading>MENYIMPAN...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
