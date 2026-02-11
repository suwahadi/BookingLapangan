<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.venues.index') }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">Daftar Venue</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('admin.venues.hub', $court->venue->slug) }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">{{ $court->venue->name }}</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('admin.venues.courts', $court->venue->slug) }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">Lapangan</a>
                <span class="text-gray-300">/</span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ $court->name }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Atur <span class="text-indigo-600">Harga</span></h1>
            <p class="text-gray-500 mt-1 tracking-tight">Kustomisasi tarif per jam berdasarkan waktu</p>
        </div>
        
        <div class="flex items-center gap-3">
             <div x-data="{ open: false, selectedDays: [] }" class="relative">
                <button @click="open = !open" class="bg-white border-2 border-gray-900 px-6 py-3 rounded-2xl font-black text-xs tracking-widest hover:bg-gray-100 transition-all flex items-center gap-2">
                    SALIN KE HARI LAIN
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                </button>
                
                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-4 w-64 bg-white rounded-[2rem] shadow-3xl border border-gray-50 p-6 z-50">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Pilih Hari Tujuan:</p>
                    <div class="space-y-2 mb-6">
                        @foreach($days as $id => $label)
                            @if($id !== $dayOfWeek)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" value="{{ $id }}" x-model="selectedDays" class="w-5 h-5 rounded-lg border-2 border-gray-200 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-bold text-gray-600 group-hover:text-indigo-600 transition-colors">{{ $label }}</span>
                            </label>
                            @endif
                        @endforeach
                    </div>
                    <button @click="$wire.copyToDays(selectedDays); open = false" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-black text-[10px] tracking-widest hover:bg-indigo-700 transition-all">
                        TERAPKAN SEKARANG
                    </button>
                </div>
            </div>

            <button wire:click="save" class="bg-gray-900 text-white px-8 py-4 rounded-[1.5rem] font-black text-sm tracking-widest hover:bg-black transition-all transform active:scale-95 shadow-2xl shadow-indigo-100 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                SIMPAN HARGA
            </button>
        </div>
    </div>

    <!-- Day Selector (Tabs) -->
    <div class="flex flex-wrap gap-2 p-2 bg-white rounded-[2rem] shadow-xl border border-gray-50">
        @foreach($days as $id => $label)
        <button wire:click="selectDay({{ $id }})" 
            class="flex-1 min-w-[100px] py-4 rounded-2xl font-black text-[10px] tracking-widest transition-all
            {{ $dayOfWeek === $id ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 scale-105' : 'text-gray-400 hover:text-gray-900 hover:bg-gray-50' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    <!-- Pricing Rows -->
    <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="p-10 border-b border-gray-50 flex items-center justify-between">
            <h4 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight italic">Rincian Tarif: <span class="text-indigo-600">{{ $days[$dayOfWeek] }}</span></h4>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Minimal 1 Aturan Harga</div>
        </div>

        <div class="p-10 space-y-6">
            @foreach($rows as $index => $row)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-end bg-gray-50/50 p-8 rounded-[2rem] border border-gray-100 group hover:border-indigo-200 transition-all">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Jam Mulai</label>
                    <div class="relative">
                        <input wire:model.defer="rows.{{ $index }}.start_time" type="time" 
                            class="w-full pl-6 pr-10 py-4 bg-white border-2 border-transparent rounded-2xl text-sm font-bold focus:border-indigo-600 focus:ring-0 transition-all uppercase">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Jam Selesai</label>
                    <div class="relative">
                        <input wire:model.defer="rows.{{ $index }}.end_time" type="time" 
                            class="w-full pl-6 pr-10 py-4 bg-white border-2 border-transparent rounded-2xl text-sm font-bold focus:border-indigo-600 focus:ring-0 transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Harga Per Jam</label>
                    <div class="relative">
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                        <input wire:model.defer="rows.{{ $index }}.price_per_hour" type="number" 
                            class="w-full pl-14 pr-6 py-4 bg-white border-2 border-transparent rounded-2xl text-sm font-black text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button wire:click="removeRow({{ $index }})" 
                        class="flex-1 py-4 px-6 border-2 border-transparent text-rose-500 font-black text-[10px] tracking-widest hover:bg-rose-50 hover:border-rose-100 rounded-2xl transition-all uppercase">
                        HAPUS
                    </button>
                </div>
            </div>
            @endforeach

            <div class="pt-6">
                <button wire:click="addRow" 
                    class="w-full py-8 border-2 border-dashed border-gray-200 rounded-[2rem] text-gray-400 font-black text-[10px] tracking-[0.3em] hover:border-indigo-600 hover:text-indigo-600 hover:bg-indigo-50/30 transition-all uppercase flex items-center justify-center gap-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    TAMBAH BARIS HARGA BARU
                </button>
            </div>
        </div>
    </div>
</div>


