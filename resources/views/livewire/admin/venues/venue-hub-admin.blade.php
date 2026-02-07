<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.venues.index') }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">Daftar Venue</a>
                <span class="text-gray-300">/</span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ $venue->name }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Venue <span class="text-indigo-600">Hub</span></h1>
            <p class="text-gray-500 font-bold mt-1 tracking-tight">Pusat kontrol dan ringkasan operasional untuk {{ $venue->name }}.</p>
        </div>
        
        <div class="flex items-center gap-4">
            <button wire:click="toggleActive" class="px-8 py-4 rounded-[1.5rem] font-black text-sm tracking-widest transition-all transform active:scale-95 shadow-2xl flex items-center gap-2 {{ $venue->is_active ? 'bg-rose-50 text-rose-600 shadow-rose-100 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-600 shadow-emerald-100 hover:bg-emerald-100' }}">
                @if($venue->is_active)
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    NONAKTIFKAN VENUE
                @else
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    AKTIFKAN & PUBLISH
                @endif
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Summary Card -->
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-gray-900 rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden text-white">
                <div class="absolute -right-4 -top-4 w-32 h-32 bg-indigo-500/10 rounded-full"></div>
                
                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-indigo-400 mb-8 flex items-center gap-2">
                    <span class="w-2 h-2 bg-indigo-400 rounded-full"></span>
                    Ringkasan Venue
                </h3>

                <div class="space-y-6">
                    <div class="flex items-center justify-between pb-6 border-b border-gray-800">
                        <span class="text-sm font-bold text-gray-400">Lapangan Aktif</span>
                        <div class="text-right">
                            <span class="text-2xl font-black font-display tracking-tight">{{ $summary['active_courts'] }}</span>
                            <span class="text-[10px] font-black text-gray-500 uppercase">/ {{ $summary['total_courts'] }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pb-6 border-b border-gray-800">
                        <span class="text-sm font-bold text-gray-400">Media & Cover</span>
                        <div class="text-right flex items-center gap-3">
                            <span class="text-lg font-black font-display tracking-tight">{{ $summary['media_count'] }} FOTO</span>
                            @if($summary['has_cover'])
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 rounded text-[8px] font-black tracking-widest uppercase">COVER OK</span>
                            @else
                                <span class="px-2 py-0.5 bg-rose-500/20 text-rose-400 rounded text-[8px] font-black tracking-widest uppercase">NO COVER</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Blackout Terdekat</p>
                        
                        <div class="bg-gray-800/50 p-4 rounded-2xl border border-gray-700/50">
                            <p class="text-[10px] font-bold text-gray-400 mb-1">VENUE-WIDE</p>
                            <p class="text-sm font-black italic">
                                {{ $summary['next_venue_blackout'] ? $summary['next_venue_blackout']->date->translatedFormat('d F Y') : 'Tidak ada' }}
                                @if($summary['next_venue_blackout'])
                                    <span class="block text-[10px] text-indigo-400 mt-0.5">"{{ $summary['next_venue_blackout']->reason }}"</span>
                                @endif
                            </p>
                        </div>

                        <div class="bg-gray-800/50 p-4 rounded-2xl border border-gray-700/50">
                            <p class="text-[10px] font-bold text-gray-400 mb-1">PER-LAPANGAN</p>
                            <p class="text-sm font-black italic">
                                {{ $summary['next_court_blackout'] ? $summary['next_court_blackout']->date->translatedFormat('d F Y') : 'Tidak ada' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-10">
                    @if($venue->is_active)
                        <div class="flex items-center gap-3 px-6 py-4 bg-emerald-500/10 rounded-2xl border border-emerald-500/20">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                            <span class="text-xs font-black text-emerald-400 uppercase tracking-widest">PUBLISHED & LIVE</span>
                        </div>
                    @else
                        <div class="flex items-center gap-3 px-6 py-4 bg-rose-500/10 rounded-2xl border border-rose-500/20 text-rose-400">
                             <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                             <span class="text-xs font-black uppercase tracking-widest italic">OFFLINE / DRAFT</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="bg-indigo-600 rounded-[2.5rem] p-8 shadow-2xl text-white">
                <h4 class="text-lg font-black italic uppercase tracking-tight mb-4 leading-tight">Butuh Bantuan Operasional?</h4>
                <p class="text-indigo-100 text-xs font-medium leading-relaxed mb-6 opacity-80">Pastikan jam operasional dan foto venue selalu diperbarui untuk meningkatkan minat penyewa lapangan.</p>
                <a href="#" class="inline-flex items-center gap-2 text-[10px] font-black bg-white/10 hover:bg-white/20 transition-all px-4 py-2 rounded-lg uppercase tracking-widest">
                    Baca Panduan &rarr;
                </a>
            </div>
        </div>

        <!-- Navigation Links Grid -->
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Edit & Settings -->
                <a href="{{ route('admin.venues.edit', $venue->slug) }}" wire:navigate class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 flex items-start gap-6 group hover:shadow-2xl transition-all duration-500">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-[1.5rem] flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-sm">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 italic uppercase italic tracking-tight mb-1">Informasi & Setting</h4>
                        <p class="text-gray-400 text-xs font-bold tracking-tight">Nama, alamat, jenis olahraga, dan pengaturan global venue.</p>
                    </div>
                </a>

                <!-- Courts -->
                <a href="{{ route('admin.venues.courts', $venue->slug) }}" wire:navigate class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 flex items-start gap-6 group hover:shadow-2xl transition-all duration-500">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-[1.5rem] flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 shadow-sm">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 italic uppercase tracking-tight mb-1">Daftar Lapangan</h4>
                        <p class="text-gray-400 text-xs font-bold tracking-tight">Kelola unit lapangan (court) dan tarif per jam masing-masing.</p>
                    </div>
                </a>

                <!-- Media -->
                <a href="{{ route('admin.venues.media', $venue->slug) }}" wire:navigate class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 flex items-start gap-6 group hover:shadow-2xl transition-all duration-500">
                    <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-[1.5rem] flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-all duration-500 shadow-sm">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 italic uppercase tracking-tight mb-1">Galeri & Media</h4>
                        <p class="text-gray-400 text-xs font-bold tracking-tight">Upload foto venue dan atur gambar sampul untuk listing publik.</p>
                    </div>
                </a>

                <!-- Operating Hours -->
                <a href="{{ route('admin.venues.operating-hours', $venue->slug) }}" wire:navigate class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 flex items-start gap-6 group hover:shadow-2xl transition-all duration-500">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-[1.5rem] flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500 shadow-sm">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 italic uppercase tracking-tight mb-1">Jam Operasional</h4>
                        <p class="text-gray-400 text-xs font-bold tracking-tight">Tentukan kapan venue buka dan tutup untuk setiap harinya.</p>
                    </div>
                </a>

                <!-- Blackouts -->
                <a href="{{ route('admin.venues.blackouts', $venue->slug) }}" wire:navigate class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 flex items-start gap-6 group hover:shadow-2xl transition-all duration-500">
                    <div class="w-16 h-16 bg-rose-50 text-rose-600 rounded-[1.5rem] flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-all duration-500 shadow-sm">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 italic uppercase tracking-tight mb-1">Venue Blackouts</h4>
                        <p class="text-gray-400 text-xs font-bold tracking-tight">Tutup venue pada hari tertentu (misal: Libur Nasional / Event).</p>
                    </div>
                </a>

                <!-- Amenities/Facilities -->
                <a href="{{ route('admin.venues.amenities', $venue->slug) }}" wire:navigate class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 flex items-start gap-6 group hover:shadow-2xl transition-all duration-500">
                    <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-[1.5rem] flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all duration-500 shadow-sm">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 italic uppercase tracking-tight mb-1">Fasilitas Venue</h4>
                        <p class="text-gray-400 text-xs font-bold tracking-tight">Kelola fasilitas seperti parkir, toilet, WiFi, dan lainnya.</p>
                    </div>
                </a>

                <!-- Reports (Placeholder) -->
                <a href="#" class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 flex items-start gap-6 opacity-60">
                    <div class="w-16 h-16 bg-gray-200 text-gray-400 rounded-[1.5rem] flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <div>
                        <h4 class="text-xl font-black text-gray-900 italic uppercase tracking-tight mb-1">Statistik dan Laporan</h4>
                        <p class="text-gray-400 text-xs font-bold tracking-tight italic">Fitur ini akan segera tersedia.</p>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>
