<div class="space-y-0">
    <!-- Hero Section -->
    <div class="relative bg-gray-900 pt-32 pb-48 overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1541534741688-6078c64b52d3?auto=format&fit=crop&q=80')] bg-cover bg-center opacity-30"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/50 via-gray-900 to-[#F8FAFC]"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl md:text-7xl font-black text-white tracking-tight leading-tight mb-6">
                MAINKAN SLOT <br> <span class="bg-gradient-to-r from-indigo-400 to-emerald-400 bg-clip-text text-transparent italic uppercase">JUARA ANDA</span>
            </h1>
            <p class="text-xl text-gray-300 font-medium max-w-2xl mx-auto mb-10">
                Temukan dan sewa lapangan olahraga terbaik di sekitar Anda dengan sistem booking instan tanpa ribet.
            </p>
        </div>
    </div>

    <!-- Floating Search Container -->
    <div class="max-w-6xl mx-auto px-4 -mt-24 relative z-20">
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 md:p-10 border border-white">
            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-8 items-end">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Cabang Olahraga</label>
                    <select wire:model="sport_type" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 text-sm font-black text-gray-900 focus:ring-2 focus:ring-indigo-600 appearance-none cursor-pointer">
                        <option value="">SEMUA OLAHRAGA</option>
                        <option value="futsal">FUTSAL</option>
                        <option value="mini_soccer">MINI SOCCER</option>
                        <option value="badminton">BADMINTON</option>
                        <option value="basket">BASKET</option>
                        <option value="tenis">TENIS</option>
                        <option value="voli">VOLI</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Lokasi / Nama Venue</label>
                    <div class="relative group">
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input wire:model="keyword" type="text" placeholder="Cari lokasi atau nama lapangan..." 
                            class="w-full pl-16 pr-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                    </div>
                </div>

                <div class="md:col-span-1 lg:col-span-1">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Pilih Tanggal</label>
                    <input wire:model="date" type="date" 
                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black text-gray-900 focus:ring-2 focus:ring-indigo-600 cursor-pointer">
                </div>

                <div class="md:col-span-full lg:col-span-1">
                    <button wire:click="search" class="w-full bg-indigo-600 text-white rounded-2xl py-4 font-black shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:scale-[1.02] active:scale-[0.98] transition-all uppercase tracking-widest text-xs">
                        Cari Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 py-24 sm:px-6 lg:px-8 space-y-24">
        
        <!-- Sport Highlights (Quick Filters) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach(['futsal' => 'https://images.unsplash.com/photo-1549646876-068a5c378e97?auto=format&fit=crop&q=80', 
                      'badminton' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?auto=format&fit=crop&q=80',
                      'basket' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&q=80',
                      'tenis' => 'https://images.unsplash.com/photo-1595435066311-665e796032d1?auto=format&fit=crop&q=80',
                      'mini_soccer' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?auto=format&fit=crop&q=80',
                      'voli' => 'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?auto=format&fit=crop&q=80'] as $sport => $img)
                <button wire:click="$set('sport_type', '{{ $sport }}')" 
                    class="relative group h-32 rounded-3xl overflow-hidden border-2 {{ $sport_type === $sport ? 'border-indigo-600 ring-4 ring-indigo-50' : 'border-white' }} shadow-xl">
                    <img src="{{ $img }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 opacity-80 group-hover:opacity-100">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent text-white p-4 flex flex-col justify-end">
                        <span class="text-[10px] font-black uppercase tracking-widest">{{ str_replace('_', ' ', $sport) }}</span>
                    </div>
                </button>
            @endforeach
        </div>

        <!-- Venue Results -->
        <div>
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight italic uppercase">Venue <span class="text-indigo-600">Terdekat</span></h2>
                    <p class="text-gray-500 font-bold mt-1">Menampilkan lapangan terbaik yang tersedia untuk Anda.</p>
                </div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm">
                    {{ $venues->total() }} Venue Ditemukan
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                @forelse($venues as $venue)
                    <a href="{{ route('public.venues.show', ['venue' => $venue->id]) }}" 
                       class="group bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-50 flex flex-col hover:scale-[1.02] transition-all duration-300">
                        <div class="h-64 relative overflow-hidden bg-gray-100">
                            @php $cov = $venue->media()->where('is_cover', true)->first(); @endphp
                            <img src="{{ $cov ? Storage::url($cov->file_path) : 'https://images.unsplash.com/photo-1520333789090-1afc82db536a?auto=format&fit=crop&q=80' }}" 
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-6 right-6">
                                <span class="bg-white/90 backdrop-blur-md px-4 py-2 rounded-xl text-[10px] font-black text-gray-900 uppercase tracking-widest shadow-lg">
                                    {{ $venue->sport_type ?? 'Multi' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-8 space-y-6 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors uppercase leading-tight italic">{{ $venue->name }}</h3>
                                <div class="flex items-center gap-2 text-gray-400 mt-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <p class="text-[10px] font-bold uppercase tracking-widest">{{ $venue->city }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                                <div class="flex -space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 border-2 border-white flex items-center justify-center text-[8px] font-black text-white">
                                        {{ $venue->active_courts_count }}
                                    </div>
                                    <div class="px-4 flex items-center">
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Lp. Aktif</span>
                                    </div>
                                </div>
                                <span class="text-indigo-600 font-black text-xs uppercase tracking-widest group-hover:translate-x-1 transition-transform">Detail &rarr;</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-400 italic">Data venue tidak ditemukan.</h3>
                        <p class="text-gray-400 text-sm mt-1">Coba gunakan filter olahraga atau lokasi lain.</p>
                    </div>
                @endforelse
            </div>

            @if($venues->hasPages())
                <div class="mt-16">
                    {{ $venues->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
