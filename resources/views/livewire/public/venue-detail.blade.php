<div class="space-y-0 pb-20">
    <!-- Venue Hero & Gallery -->
    <div class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-indigo-400 font-black text-[10px] uppercase tracking-[0.2em] mb-8 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali ke Pencarian
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-600 rounded-full text-[8px] font-black text-white uppercase tracking-widest">
                        TERSEDIA SEKARANG
                    </div>
                    <h1 class="text-5xl md:text-6xl font-black text-white tracking-tight leading-none italic uppercase">{{ $venue->name }}</h1>
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-3 text-gray-400">
                            <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <span class="text-sm font-bold">{{ $venue->address }}, {{ $venue->city }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @php $media = $venue->media; @endphp
                    @forelse($media->take(4) as $m)
                        <div class="h-40 md:h-56 rounded-[2rem] overflow-hidden border-4 border-white/5 shadow-2xl group">
                            <img src="{{ Storage::url($m->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-80 group-hover:opacity-100">
                        </div>
                    @empty
                        @foreach(range(1, 4) as $i)
                            <div class="h-40 md:h-56 rounded-[2rem] overflow-hidden border-4 border-white/5 shadow-2xl bg-gray-800 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            
            <!-- Court List -->
            <div class="lg:col-span-2 space-y-10 py-10">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight italic uppercase">Pilihan <span class="text-indigo-600">Lapangan</span></h2>
                        <p class="text-gray-500 font-bold mt-1 uppercase text-[10px] tracking-[0.2em]">Pilih lapangan terbaik untuk tim Anda</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($venue->courts as $court)
                        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-50 group hover:border-indigo-100 transition-all">
                            <div class="flex items-start justify-between mb-8">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <span class="px-4 py-1.5 bg-gray-900 rounded-full text-[8px] font-black text-white uppercase tracking-widest leading-none">
                                    {{ $court->sport }}
                                </span>
                            </div>

                            <h3 class="text-2xl font-black text-gray-900 uppercase italic leading-none mb-2">{{ $court->name }}</h3>
                            <p class="text-gray-400 font-bold text-[10px] uppercase tracking-widest mb-8">
                                @if($court->floor_type) TIPE LANTAI: {{ $court->floor_type }} @else MULTIPLATFORM @endif
                            </p>

                            <a href="{{ route('courts.schedule', ['venueCourt' => $court->id]) }}" 
                               class="block w-full text-center py-4 bg-gray-900 rounded-2xl font-black text-xs uppercase tracking-[0.2em] text-white hover:bg-indigo-600 hover:text-white transition-all transform active:scale-95 shadow-xl shadow-gray-200 hover:shadow-indigo-200">
                                CEK JADWAL SEKARANG
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full py-20 bg-gray-50 rounded-[3rem] border-2 border-dashed border-gray-200 text-center">
                            <p class="text-gray-400 font-bold italic">Belum ada lapangan aktif saat ini.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Amenities Section -->
                @if($venue->amenities->count() > 0)
                    <div class="mt-16">
                        <div class="flex items-end justify-between mb-8">
                            <div>
                                <h2 class="text-3xl font-black text-gray-900 tracking-tight italic uppercase">Fasilitas <span class="text-indigo-600">Venue</span></h2>
                                <p class="text-gray-500 font-bold mt-1 uppercase text-[10px] tracking-[0.2em]">Tersedia untuk pengunjung</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-50">
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($venue->amenities as $amenity)
                                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl">
                                        <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                                            @switch($amenity->icon)
                                                @case('wifi')
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" /></svg>
                                                    @break
                                                @case('car')
                                                @case('parking')
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8a2 2 0 012 2v10H6V9a2 2 0 012-2zm0 0V5a2 2 0 012-2h4a2 2 0 012 2v2" /></svg>
                                                    @break
                                                @case('shield')
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                                    @break
                                                @case('camera')
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                                    @break
                                                @case('coffee')
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                    @break
                                                @case('users')
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                                    @break
                                                @default
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            @endswitch
                                        </div>
                                        <span class="text-sm font-bold text-gray-700">{{ $amenity->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>


            <!-- Side Filter Card (Sticky) -->
            <div class="lg:sticky lg:top-24 pt-10">
                <div class="bg-white rounded-[3rem] p-10 shadow-2xl border border-gray-100 space-y-8">
                    <div>
                        <h4 class="text-2xl font-black text-gray-900 uppercase italic leading-none">Rekomendasi <br> <span class="text-indigo-600">Jadwal</span></h4>
                        <p class="text-gray-400 font-bold text-[10px] uppercase tracking-widest mt-2">Filter untuk melihat ketersediaan</p>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pilih Tanggal</label>
                            <input type="date" wire:model.live="date" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 text-sm font-black text-gray-900 focus:ring-2 focus:ring-indigo-600 cursor-pointer">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Mulai</label>
                                <input type="time" wire:model.live="start_time" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 text-sm font-black text-gray-900 focus:ring-2 focus:ring-indigo-600 cursor-pointer">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Selesai</label>
                                <input type="time" wire:model.live="end_time" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 text-sm font-black text-gray-900 focus:ring-2 focus:ring-indigo-600 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100 flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <p class="text-[10px] text-indigo-900 font-bold leading-relaxed uppercase tracking-widest">
                            Gunakan filter ini untuk mempercepat pengecekan jadwal di setiap lapangan.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
