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
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Court <span class="text-indigo-600">Blackout</span></h1>
            <p class="text-gray-500 mt-1 tracking-tight">Atur hari libur atau penutupan khusus untuk lapangan <span class="text-indigo-600">#{{ $court->name }}</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Form Side -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl border border-gray-50 sticky top-10">
                <div class="mb-8">
                    <h3 class="text-xl font-black text-gray-900 font-display italic uppercase tracking-tight">Tambah <span class="text-indigo-600">Blackout</span></h3>
                    <p class="text-gray-400 font-bold text-xs mt-1">Pilih tanggal untuk menonaktifkan lapangan ini.</p>
                </div>

                <form wire:submit.prevent="save" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Tanggal Blackout</label>
                        <input wire:model="date" type="date" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                        @error('date') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Alasan / Label</label>
                        <input wire:model="reason" type="text" placeholder="Contoh: Perbaikan Lantai / Event" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                        @error('reason') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-2xl font-black text-[11px] tracking-[0.3em] hover:bg-black transition-all shadow-xl shadow-gray-100 flex items-center justify-center gap-2 group">
                        <span wire:loading.remove>SIMPAN TANGGAL</span>
                        <span wire:loading>PROSES...</span>
                        <svg wire:loading.remove class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- List Side -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-50 overflow-hidden">
                <div class="px-10 py-8 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">Daftar Blackout Lapangan</h2>
                    <span class="px-4 py-1.5 bg-indigo-100 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $blackouts->count() }} Total</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/20">
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Tanggal</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Keterangan</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($blackouts as $item)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-10 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900">{{ $item->date->translatedFormat('d F Y') }}</span>
                                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">{{ $item->date->translatedFormat('l') }}</span>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <p class="text-sm font-bold text-gray-600 italic">"{{ $item->reason }}"</p>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus blackout lapangan ini?" class="p-3 bg-gray-50 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all shadow-sm">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-10 py-20 text-center">
                                    <div class="bg-gray-50 rounded-[2rem] p-10 inline-block border-2 border-dashed border-gray-100">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <p class="text-gray-400 font-bold mb-1">Tidak ada blackout khusus.</p>
                                        <p class="text-[10px] text-gray-300 font-black uppercase tracking-widest leading-relaxed">Gunakan form di samping untuk<br>mengatur penutupan khusus lapangan ini.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
