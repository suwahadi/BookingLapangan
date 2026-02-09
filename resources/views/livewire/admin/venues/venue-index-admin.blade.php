<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Kelola <span class="text-indigo-600">Venue</span></h1>
            <p class="text-gray-500 mt-1 tracking-tight">Menampilkan daftar semua tempat olahraga yang terdaftar di sistem</p>
        </div>
        <a href="{{ route('admin.venues.create') }}" wire:navigate class="bg-gray-900 text-white px-8 py-4 rounded-[1.5rem] font-black text-sm tracking-widest hover:bg-black transition-all transform active:scale-95 shadow-2xl shadow-gray-200 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
            TAMBAH VENUE BARU
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-6 rounded-[2rem] shadow-xl border border-gray-50 flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 group">
            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>
            <input wire:model.live.debounce.300ms="q" type="text" placeholder="Cari nama venue, kota, atau alamat..." 
                class="w-full pl-14 pr-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600 transition-all">
        </div>

        <div class="w-full md:w-64">
            <select wire:model.live="status" 
                class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black text-gray-900 focus:ring-2 focus:ring-indigo-600 transition-all appearance-none cursor-pointer">
                <option value="">SEMUA STATUS</option>
                <option value="active">AKTIF & PUBLISHED</option>
                <option value="inactive">NONAKTIF / DRAFT</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Info Venue</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Lokasi / Cabang</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center"># Lapangan</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $venue)
                    <tr class="hover:bg-gray-50/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.venues.hub', $venue->slug) }}" wire:navigate>
                                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-display font-black text-xl group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                                        {{ substr($venue->name, 0, 1) }}
                                    </div>
                                </a>
                                <div>
                                    <a href="{{ route('admin.venues.hub', $venue->slug) }}" wire:navigate>
                                        <h4 class="text-sm font-black text-gray-900 hover:text-indigo-600 transition-colors">{{ $venue->name }}</h4>
                                    </a>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mt-0.5">{{ $venue->sport_type }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-gray-600">{{ $venue->city }}</p>
                            <p class="text-[11px] text-gray-400 font-medium truncate max-w-[200px] mt-1">{{ $venue->address }}</p>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <a href="{{ route('admin.venues.courts', $venue->slug) }}" wire:navigate class="inline-flex items-center justify-center px-3 py-1 bg-gray-100 rounded-lg text-xs font-black text-gray-600 hover:bg-indigo-600 hover:text-white transition-all">
                                {{ $venue->courts_count }}
                            </a>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @if($venue->is_active)
                                <span class="px-3 py-1.5 bg-emerald-100 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm">AKTIF</span>
                            @else
                                <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded-lg text-[10px] font-black uppercase tracking-widest">NONAKTIF</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.venues.media', $venue->slug) }}" wire:navigate title="Galeri Foto" class="p-2.5 bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </a>
                                <a href="{{ route('admin.venues.operating-hours', $venue->slug) }}" wire:navigate title="Jam Operasional" class="p-2.5 bg-gray-50 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </a>
                                <a href="{{ route('admin.venues.courts', $venue->slug) }}" wire:navigate title="Kelola Lapangan" class="p-2.5 bg-gray-50 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                                </a>
                                <a href="{{ route('admin.venues.blackouts', $venue->slug) }}" wire:navigate title="Venue Blackout" class="p-2.5 bg-gray-50 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </a>
                                <a href="{{ route('admin.venues.edit', $venue->slug) }}" wire:navigate title="Edit Venue" class="p-2.5 bg-gray-50 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </a>
                                <a href="#" class="p-2.5 bg-gray-50 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all shadow-sm">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="bg-gray-50 rounded-[2.5rem] p-12 inline-block border-2 border-dashed border-gray-200">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                <p class="text-gray-400 font-bold">Tidak ada venue yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/20">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
