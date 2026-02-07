<div class="space-y-0 pb-20">
    <!-- Header/Breadcrumb Bar (Optional per reference info, simpler header) -->
    <div class="bg-surface-light dark:bg-surface-dark border-b border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-muted-light">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a>
                <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                <span class="text-text-light dark:text-text-dark">{{ $venue->name }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Gallery Grid (1 Large, 2 Small Side) -->
        <div class="grid grid-cols-1 md:grid-cols-4 grid-rows-2 gap-4 h-[400px] md:h-[500px] mb-10">
             @php 
                $media = $venue->media; 
                $mainImage = $media->first();
                $sideImages = $media->skip(1)->take(2);
            @endphp

            <!-- Main Image (Left Big) -->
            <div class="md:col-span-2 md:row-span-2 relative rounded-[2rem] overflow-hidden group shadow-lg">
                @if($mainImage)
                    <img src="{{ Storage::url($mainImage->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                @else
                    <div class="w-full h-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center">
                        <span class="material-symbols-outlined text-6xl text-gray-400">image</span>
                    </div>
                @endif
            </div>

            <!-- Side Images (Right Stack) -->
            @forelse($sideImages as $img)
                <div class="md:col-span-1 md:row-span-1 relative rounded-[2rem] overflow-hidden group shadow-lg">
                    <img src="{{ Storage::url($img->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                </div>
            @empty
                <div class="md:col-span-1 md:row-span-1 bg-gray-100 dark:bg-gray-800 rounded-[2rem] flex items-center justify-center">
                     <span class="material-symbols-outlined text-4xl text-gray-300">image</span>
                </div>
                <div class="md:col-span-1 md:row-span-1 bg-gray-100 dark:bg-gray-800 rounded-[2rem] flex items-center justify-center">
                     <span class="material-symbols-outlined text-4xl text-gray-300">image</span>
                </div>
            @endforelse

            <!-- View All Button Overlay (Placed on the last available slot visually or over main) -->
            <div class="md:col-span-1 md:row-span-1 relative rounded-[2rem] overflow-hidden group cursor-pointer">
                @if($media->count() > 3)
                   <img src="{{ Storage::url($media[3]->file_path) }}" class="w-full h-full object-cover blur-sm brightness-50">
                @else
                   <div class="w-full h-full bg-gray-900/50"></div>
                @endif
                
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white p-4 text-center hover:bg-black/20 transition-colors">
                    <span class="material-symbols-outlined text-3xl mb-1">grid_view</span>
                    <span class="text-xs font-black uppercase tracking-widest">Lihat Semua Foto</span>
                </div>
            </div>
        </div>

        <!-- 2 Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
            
            <!-- Left Column: Info & Content -->
            <div class="lg:col-span-2 space-y-12">
                
                <!-- Venue Intro -->
                <div class="space-y-4">
                    <h1 class="text-3xl md:text-4xl font-black text-text-light dark:text-text-dark tracking-tight uppercase italic leading-none">{{ $venue->name }}</h1>
                    
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1 text-amber-500">
                            <span class="material-symbols-outlined text-lg fill-current">star</span>
                            <span class="font-black text-lg text-text-light dark:text-text-dark">4.9</span>
                        </div>
                        <span class="text-gray-300">|</span>
                        <div class="flex items-center gap-1 text-muted-light text-sm font-bold uppercase tracking-wide">
                             <span class="material-symbols-outlined text-sm">location_on</span>
                             {{ $venue->city }}
                        </div>
                        <span class="text-gray-300">|</span>
                        <div class="px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-lg text-[10px] font-black uppercase tracking-widest text-text-light dark:text-text-dark">
                            Badminton
                        </div>
                    </div>
                </div>

                <!-- Description & Rules -->
                <div x-data="{ expanded: false }" class="space-y-4 border-b border-gray-100 dark:border-gray-800 pb-10">
                    <h3 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic">Deskripsi</h3>
                    <div class="relative">
                        <p class="text-sm font-medium text-muted-light leading-relaxed" 
                           :class="expanded ? '' : 'line-clamp-3'">
                            {{ $venue->description ?? 'Tidak ada deskripsi tersedia.' }}
                            <br><br>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                        </p>
                        <button @click="expanded = !expanded" class="text-primary font-bold text-xs uppercase tracking-widest mt-2 hover:underline">
                            <span x-text="expanded ? 'Sembunyikan' : 'Baca Selengkapnya'"></span>
                        </button>
                    </div>

                    <!-- Location Box Preview -->
                     <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 flex items-center justify-between mt-6">
                        <div class="flex items-center gap-3">
                             <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                <span class="material-symbols-outlined text-xl">map</span>
                             </div>
                             <div>
                                 <p class="text-xs font-black uppercase text-gray-400 tracking-widest mb-0.5">Lokasi Venue</p>
                                 <p class="text-sm font-bold text-text-light dark:text-text-dark line-clamp-1">{{ $venue->address }}</p>
                             </div>
                        </div>
                        <a href="https://maps.google.com/?q={{ urlencode($venue->address) }}" target="_blank" class="text-primary font-black text-xs uppercase tracking-widest hover:underline flex items-center gap-1">
                            Buka Peta
                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                        </a>
                    </div>
                </div>

                <!-- Facilities -->
                <div class="border-b border-gray-100 dark:border-gray-800 pb-10">
                    <h3 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic mb-6">Fasilitas</h3>
                    @if($venue->amenities->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-y-6 gap-x-4">
                            @foreach($venue->amenities as $amenity)
                                @php
                                    $iconName = strtolower($amenity->name);
                                    $icon = match(true) {
                                        Str::contains($iconName, ['parkir', 'parking', 'car']) => 'directions_car',
                                        Str::contains($iconName, ['motor', 'bike']) => 'two_wheeler',
                                        Str::contains($iconName, ['toilet', 'wc', 'restroom']) => 'wc',
                                        Str::contains($iconName, ['kantin', 'canteen', 'cafe', 'makan', 'minum']) => 'restaurant',
                                        Str::contains($iconName, ['wifi', 'wi-fi', 'internet']) => 'wifi',
                                        Str::contains($iconName, ['musholla', 'mushola', 'masjid', 'prayer']) => 'mosque',
                                        Str::contains($iconName, ['loker', 'locker']) => 'lock',
                                        Str::contains($iconName, ['ganti', 'changing']) => 'checkroom',
                                        Str::contains($iconName, ['ac', 'air conditioner']) => 'ac_unit',
                                        Str::contains($iconName, ['shower', 'mandi']) => 'shower',
                                        default => $amenity->icon ?? 'check_circle'
                                    };
                                @endphp
                                <div class="flex items-center gap-3 p-3 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border border-transparent hover:border-gray-100 dark:hover:border-gray-700">
                                    <div class="text-primary">
                                         @if($icon === 'directions_car') <span class="material-symbols-outlined text-2xl">directions_car</span>
                                         @elseif($icon === 'wc') <span class="material-symbols-outlined text-2xl">wc</span>
                                         @elseif($icon === 'restaurant') <span class="material-symbols-outlined text-2xl">restaurant</span>
                                         @elseif($icon === 'two_wheeler') <span class="material-symbols-outlined text-2xl">two_wheeler</span>
                                         @elseif($icon === 'wifi') <span class="material-symbols-outlined text-2xl">wifi</span>
                                         @elseif($icon === 'mosque') <span class="material-symbols-outlined text-2xl">mosque</span>
                                         @else <span class="material-symbols-outlined text-2xl">{{ $icon }}</span>
                                         @endif
                                    </div>
                                    <span class="text-sm font-bold text-text-light dark:text-text-dark capitalize tracking-wide">{{ $amenity->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-muted-light">Fasilitas standar tersedia.</p>
                    @endif
                </div>

                <!-- Membership (Placeholder based on image) -->
                <div class="border-b border-gray-100 dark:border-gray-800 pb-10">
                     <h3 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic mb-6">Paket Membership</h3>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <!-- Card 1 -->
                         <div class="p-6 rounded-3xl border border-gray-100 dark:border-gray-700 bg-surface-light dark:bg-surface-dark relative overflow-hidden group hover:border-primary/50 transition-all">
                             <div class="flex justify-between items-start mb-4">
                                 <div>
                                     <h4 class="font-black text-text-light dark:text-text-dark">Paket 3 Bulan</h4>
                                     <ul class="mt-2 text-[10px] text-muted-light space-y-1 font-bold">
                                         <li>• Langganan 2 slot per minggu</li>
                                         <li>• Perpanjang setiap 3 bulan</li>
                                     </ul>
                                 </div>
                             </div>
                             <div class="flex justify-between items-center mt-4 border-t border-gray-100 dark:border-gray-800 pt-4">
                                 <span class="text-xs font-black text-rose-500 uppercase">Diskon Rp 50rb</span>
                                 <button class="px-4 py-2 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-primary-dark">Beli Paket</button>
                             </div>
                         </div>
                         <!-- Card 2 -->
                         <div class="p-6 rounded-3xl border border-gray-100 dark:border-gray-700 bg-surface-light dark:bg-surface-dark relative overflow-hidden group hover:border-primary/50 transition-all">
                             <div class="flex justify-between items-start mb-4">
                                 <div>
                                     <h4 class="font-black text-text-light dark:text-text-dark">Paket 1 Bulan</h4>
                                      <ul class="mt-2 text-[10px] text-muted-light space-y-1 font-bold">
                                         <li>• Langganan 1 slot per minggu</li>
                                         <li>• Perpanjang setiap 1 bulan</li>
                                     </ul>
                                 </div>
                             </div>
                             <div class="flex justify-between items-center mt-4 border-t border-gray-100 dark:border-gray-800 pt-4">
                                 <span class="text-xs font-black text-rose-500 uppercase">Diskon 5%</span>
                                 <button class="px-4 py-2 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-primary-dark">Beli Paket</button>
                             </div>
                         </div>
                     </div>
                </div>

                <!-- Court Selection & Schedule -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                         <div class="flex items-center gap-2">
                             <span class="material-symbols-outlined text-primary text-2xl animate-pulse">play_arrow</span>
                             <h2 class="text-2xl font-black text-text-light dark:text-text-dark uppercase italic">Pilih Lapangan</h2>
                         </div>
                    </div>

                    <!-- Date Picker Strip -->
                    <div class="bg-surface-light dark:bg-surface-dark p-4 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                             <div class="flex gap-2 overflow-x-auto no-scrollbar pb-2">
                                @foreach(range(0, 6) as $day)
                                    @php 
                                        $d = \Carbon\Carbon::now()->addDays($day); 
                                        $isActive = $d->format('Y-m-d') === $date;
                                    @endphp
                                    <button wire:click="$set('date', '{{ $d->format('Y-m-d') }}')" 
                                            class="min-w-[70px] flex flex-col items-center justify-center p-3 rounded-2xl transition-all border {{ $isActive ? 'bg-primary text-white border-primary shadow-lg shadow-primary/30 transform scale-105' : 'bg-transparent text-muted-light border-transparent hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                        <span class="text-[10px] font-bold uppercase tracking-wider mb-1">{{ $d->translatedFormat('D') }}</span>
                                        <span class="text-lg font-black leading-none">{{ $d->format('d') }}</span>
                                        <span class="text-[8px] font-bold mt-1">{{ $d->format('M') }}</span>
                                    </button>
                                @endforeach
                             </div>
                             <div class="h-8 w-[1px] bg-gray-200 dark:bg-gray-700 mx-2"></div>
                             <button class="p-3 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 text-muted-light transition-colors">
                                 <span class="material-symbols-outlined">calendar_month</span>
                             </button>
                        </div>
                    </div>

                    <!-- Court Cards Loop -->
                    <div class="space-y-6">
                        @forelse($venue->courts as $court)
                            <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] p-6 border border-gray-100 dark:border-gray-700 shadow-card hover:shadow-xl transition-all duration-300 group">
                                <div class="flex flex-col md:flex-row gap-6">
                                    <!-- Left: Image -->
                                    <div class="w-full md:w-48 h-40 rounded-3xl overflow-hidden relative shrink-0">
                                        <div class="absolute inset-0 bg-primary/10 group-hover:bg-transparent transition-colors z-10"></div>
                                        <img src="https://ui-avatars.com/api/?name={{ $court->name }}&background=random&size=400" class="w-full h-full object-cover">
                                        <!-- Nav Icons (Mock) -->
                                        <div class="absolute top-1/2 left-2 -translate-y-1/2 w-6 h-6 bg-white/50 rounded-full flex items-center justify-center cursor-pointer hover:bg-white text-xs backdrop-blur-sm z-20">❮</div>
                                        <div class="absolute top-1/2 right-2 -translate-y-1/2 w-6 h-6 bg-white/50 rounded-full flex items-center justify-center cursor-pointer hover:bg-white text-xs backdrop-blur-sm z-20">❯</div>
                                        <div class="absolute bottom-2 right-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded-lg text-[8px] font-bold text-white uppercase tracking-wider">
                                            Lihat semua foto
                                        </div>
                                    </div>
                                    
                                    <!-- Middle: Info -->
                                    <div class="flex-1 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-xl font-black text-text-light dark:text-text-dark uppercase italic">{{ $court->name }}</h3>
                                            <a href="{{ route('courts.schedule', ['venueCourt' => $court->id]) }}" class="hidden md:flex items-center gap-1 text-[10px] font-black uppercase text-text-light dark:text-text-dark hover:text-primary">
                                                Detail 
                                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                            </a>
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-2 text-[10px] font-bold text-muted-light uppercase tracking-wide">
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">sports_tennis</span> Badminton</span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">roofing</span> Indoor</span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">texture</span> {{ $court->floor_type ?? 'Karpet' }}</span>
                                        </div>

                                        <div class="pt-4 mt-2 border-t border-gray-50 dark:border-gray-800">
                                            <a href="{{ route('courts.schedule', ['venueCourt' => $court->id]) }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 group-hover:scale-105 active:scale-95">
                                                <span class="material-symbols-outlined text-sm">schedule</span>
                                                Lihat & Pilih Jadwal
                                            </a>
                                            <!-- Visual Placeholder for Slots (To match image intent) -->
                                            <div class="mt-4 grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 opacity-50 pointer-events-none grayscale">
                                                 <!-- Mock slots to show 'preview' look -->
                                                <div class="p-2 border rounded-lg text-center bg-gray-50 border-gray-100">
                                                    <div class="text-[8px] font-bold">08:00</div>
                                                </div>
                                                <div class="p-2 border rounded-lg text-center bg-gray-50 border-gray-100">
                                                    <div class="text-[8px] font-bold">09:00</div>
                                                </div>
                                                <div class="p-2 border rounded-lg text-center bg-gray-50 border-gray-100">
                                                    <div class="text-[8px] font-bold">10:00</div>
                                                </div>
                                                <!-- ... -->
                                            </div>
                                            <p class="text-[8px] text-muted-light mt-1 italic text-center md:text-left">*Klik tombol merah untuk melihat slot real-time</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-10 text-center border-2 border-dashed border-gray-200 rounded-[3rem]">
                                Belum ada lapangan.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Reviews -->
                <div class="pt-10 border-t border-gray-100 dark:border-gray-800">
                     <div class="flex items-center gap-2 mb-6">
                             <span class="material-symbols-outlined text-primary text-2xl animate-pulse">star</span>
                             <h2 class="text-2xl font-black text-text-light dark:text-text-dark uppercase italic">Ulasan</h2>
                     </div>
                     <div class="flex items-center gap-4 mb-8">
                         <span class="text-5xl font-black text-text-light dark:text-text-dark">4.9</span>
                         <div class="flex flex-col">
                             <div class="flex text-amber-500 text-sm">
                                 <span class="material-symbols-outlined fill-current">star</span>
                                 <span class="material-symbols-outlined fill-current">star</span>
                                 <span class="material-symbols-outlined fill-current">star</span>
                                 <span class="material-symbols-outlined fill-current">star</span>
                                 <span class="material-symbols-outlined fill-current">star_half</span>
                             </div>
                             <span class="text-xs font-bold text-muted-light uppercase tracking-wide">Berdasarkan 86 ulasan</span>
                         </div>
                         <div class="ml-auto">
                              <button class="text-[10px] font-black text-rose-500 hover:underline uppercase tracking-widest">Lihat Semua Ulasan</button>
                         </div>
                     </div>

                     <!-- Review Cards (Mock) -->
                     <div class="space-y-4">
                         <div class="p-6 bg-surface-light dark:bg-surface-dark rounded-[2rem] border border-gray-100 dark:border-gray-700">
                             <div class="flex items-center gap-4 mb-4">
                                 <div class="w-10 h-10 rounded-full bg-gray-200"></div>
                                 <div>
                                     <h5 class="font-bold text-sm">Indra Putra Medan</h5>
                                     <p class="text-[10px] text-muted-light">Diulas 30 Januari 2024</p>
                                 </div>
                                 <div class="ml-auto px-2 py-1 bg-amber-100 text-amber-700 rounded-lg text-[10px] font-black flex items-center gap-1">
                                     <span class="material-symbols-outlined text-xs">star</span> 4.5
                                 </div>
                             </div>
                             <p class="text-xs font-bold text-muted-light leading-relaxed">"Baik nya kalau masih kurang 10 menit yg main dijam berikutnya ga masuk dulu... soalnya risih banget hahaha"</p>
                         </div>
                     </div>
                </div>

            </div>

            <!-- Right Column: Sticky Sidebar -->
            <div class="lg:col-span-1 lg:sticky lg:top-24 space-y-6">
                <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] p-8 shadow-2xl border border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest mb-1">Mulai Dari</p>
                    <div class="flex items-end gap-1 mb-6">
                        <h3 class="text-3xl font-black text-primary italic font-display">Rp 70.000</h3>
                        <span class="text-xs font-bold text-muted-light mb-1">/ Sesi</span>
                    </div>

                    <a href="#court-selection" onclick="document.querySelector('.court-selection-container')?.scrollIntoView({behavior: 'smooth'})" class="block w-full py-4 bg-primary text-white rounded-2xl font-black text-sm uppercase tracking-widest text-center hover:bg-primary-dark transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1 mb-8">
                        Cek Ketersediaan
                    </a>

                    <div class="space-y-4">
                        <h5 class="font-black text-sm text-text-light dark:text-text-dark">Booking lewat aplikasi lebih banyak keuntungan!</h5>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                <span class="text-xs font-bold text-muted-light leading-snug">Opsi pembayaran down payment (DP)*</span>
                            </li>
                             <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                <span class="text-xs font-bold text-muted-light leading-snug">Reschedule jadwal booking*</span>
                            </li>
                             <li class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                <span class="text-xs font-bold text-muted-light leading-snug">Lebih banyak promo & voucher</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 text-center">
                         <button class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                             Bagikan Venue
                         </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
