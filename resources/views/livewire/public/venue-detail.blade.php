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
        
        <!-- Gallery Section -->
        @php 
            $media = $venue->media; 
            $images = $media->map(fn($m) => Storage::url($m->file_path))->values();
            if($images->isEmpty()) {
                $images = collect(['https://ui-avatars.com/api/?name='.urlencode($venue->name).'&background=random']);
            }
            $mainImage = $media->first();
            $sideImages = $media->skip(1)->take(2);
        @endphp

        <div class="mb-10" 
             x-data="{ 
                galleryOpen: false,
                activeGallery: 0,
                images: {{ $images }},
                touchStartX: 0,
                touchEndX: 0,
                nextGallery() { this.activeGallery = (this.activeGallery === this.images.length - 1) ? 0 : this.activeGallery + 1 },
                prevGallery() { this.activeGallery = (this.activeGallery === 0) ? this.images.length - 1 : this.activeGallery - 1 },
                handleSwipe() {
                    if (this.touchEndX < this.touchStartX - 50) this.nextGallery();
                    if (this.touchEndX > this.touchStartX + 50) this.prevGallery();
                }
             }"
             @keydown.escape.window="galleryOpen = false">

            <!-- Mobile Slider -->
            <div class="md:hidden relative h-64 rounded-[2rem] overflow-hidden group shadow-lg" 
                 x-data="{ 
                    active: 0, 
                    images: {{ $images }},
                    touchStartX: 0,
                    touchEndX: 0,
                    next() { this.active = (this.active === this.images.length - 1) ? 0 : this.active + 1 },
                    prev() { this.active = (this.active === 0) ? this.images.length - 1 : this.active - 1 },
                    handleSwipe() {
                        if (this.touchEndX < this.touchStartX - 50) this.next();
                        if (this.touchEndX > this.touchStartX + 50) this.prev();
                    }
                 }"
                 @touchstart="touchStartX = $event.changedTouches[0].screenX"
                 @touchend="touchEndX = $event.changedTouches[0].screenX; handleSwipe()">
                
                <template x-for="(img, index) in images" :key="index">
                    <img :src="img" 
                         x-show="active === index"
                         x-transition:enter="transition opacity duration-500"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition opacity duration-500"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-0 w-full h-full object-cover">
                </template>

                <!-- Navigation Arrows -->
                <button @click.stop="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-white/40 transition-colors z-20">
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                </button>
                <button @click.stop="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center bg-white/20 backdrop-blur-md rounded-full text-white hover:bg-white/40 transition-colors z-20">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                </button>
            </div>

            <!-- Desktop Grid (Original) -->
            <div class="hidden md:grid grid-cols-4 grid-rows-2 gap-4 h-[500px]">
                <!-- Main Image (Left Big) -->
                <div class="col-span-2 row-span-2 relative rounded-[2rem] overflow-hidden group shadow-lg"
                     @click="galleryOpen = true; activeGallery = 0">
                    @if($mainImage)
                        <img src="{{ Storage::url($mainImage->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 cursor-pointer">
                    @else
                        <div class="w-full h-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center">
                            <span class="material-symbols-outlined text-6xl text-gray-400">image</span>
                        </div>
                    @endif
                </div>

                <!-- Side Images (Right Stack) -->
                @forelse($sideImages as $index => $img)
                    <div class="col-span-1 row-span-1 relative rounded-[2rem] overflow-hidden group shadow-lg"
                         @click="galleryOpen = true; activeGallery = {{ $index + 1 }}">
                        <img src="{{ Storage::url($img->file_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 cursor-pointer">
                    </div>
                @empty
                    <div class="col-span-1 row-span-1 bg-gray-100 dark:bg-gray-800 rounded-[2rem] flex items-center justify-center">
                         <span class="material-symbols-outlined text-4xl text-gray-300">image</span>
                    </div>
                    <div class="col-span-1 row-span-1 bg-gray-100 dark:bg-gray-800 rounded-[2rem] flex items-center justify-center">
                         <span class="material-symbols-outlined text-4xl text-gray-300">image</span>
                    </div>
                @endforelse

                <!-- View All Button Overlay -->
                <div class="col-span-1 row-span-1 relative rounded-[2rem] overflow-hidden group cursor-pointer bg-black"
                     @click="galleryOpen = true; activeGallery = 0">
                    @if($media->count() > 3)
                       <img src="{{ Storage::url($media[3]->file_path) }}" class="w-full h-full object-cover opacity-60 group-hover:opacity-40 transition-opacity">
                    @else
                       <div class="w-full h-full bg-gray-900/50"></div>
                    @endif
                    
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-white p-4 text-center">
                        <span class="material-symbols-outlined text-3xl mb-1 group-hover:scale-110 transition-transform">grid_view</span>
                        <span class="text-xs font-black uppercase tracking-widest">Lihat Semua Foto</span>
                    </div>
                </div>
            </div>

            <!-- Fullscreen Gallery Modal -->
            <div x-show="galleryOpen" 
                 style="display: none;"
                 class="fixed inset-0 z-[60] bg-black/95 backdrop-blur-sm flex items-center justify-center"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                <!-- Close Button -->
                <button @click="galleryOpen = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors z-50">
                    <span class="material-symbols-outlined text-4xl">close</span>
                </button>

                <!-- Navigation -->
                <button @click.stop="prevGallery()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center bg-white/10 rounded-full text-white hover:bg-white/20 transition-colors z-50">
                    <span class="material-symbols-outlined text-3xl">chevron_left</span>
                </button>
                <button @click.stop="nextGallery()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center bg-white/10 rounded-full text-white hover:bg-white/20 transition-colors z-50">
                    <span class="material-symbols-outlined text-3xl">chevron_right</span>
                </button>

                <!-- Main Image -->
                <div class="w-full h-full flex items-center justify-center p-4"
                     @touchstart="touchStartX = $event.changedTouches[0].screenX"
                     @touchend="touchEndX = $event.changedTouches[0].screenX; handleSwipe()">
                    <template x-for="(img, index) in images" :key="index">
                        <img :src="img" 
                             x-show="activeGallery === index"
                             x-transition:enter="transition opacity duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition opacity duration-300"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="max-w-full max-h-full object-contain absolute select-none">
                    </template>
                </div>
                
                <!-- Counter -->
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 px-4 py-2 bg-black/50 rounded-full text-white text-sm font-bold tracking-widest">
                    <span x-text="activeGallery + 1"></span> / <span x-text="images.length"></span>
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
                        <div class="flex items-center gap-1 text-muted-light text-sm">
                             <span class="material-symbols-outlined text-sm">location_on</span>
                             {{ $venue->city }}
                        </div>
                        <span class="text-gray-300">|</span>
                        <div class="flex items-center gap-1 text-muted-light text-sm">
                           {{ $venue->courts->pluck('sport')->unique()->implode(', ') ?: 'Olahraga' }}
                        </div>
                    </div>
                </div>

                <!-- Description & Rules -->
                <div x-data="{ expanded: false }" class="space-y-4 border-b border-gray-100 dark:border-gray-800 pb-10">
                    <h3 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic">Deskripsi</h3>
                    <div class="relative">
                        <p class="text-sm text-muted-light leading-relaxed" 
                           :class="expanded ? '' : 'line-clamp-3'">
                            {{ $venue->description ?? 'Tidak ada deskripsi tersedia.' }}
                            <br>
                        </p>
                        <button @click="expanded = !expanded" class="text-primary text-xs hover:underline">
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
                                 <p class="text-xs font-black uppercase">Lokasi Venue</p>
                                 <p class="text-sm text-text-light dark:text-text-dark line-clamp-1">{{ $venue->address }}</p>
                             </div>
                        </div>
                        <a href="https://maps.google.com/?q={{ urlencode($venue->address) }}" target="_blank" class="text-primary text-xs flex items-center gap-1">
                            Peta
                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                        </a>
                    </div>
                </div>

                <!-- Facilities -->
                <div class="border-b border-gray-100 dark:border-gray-800 pb-10">
                    <h3 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic mb-6">Fasilitas</h3>
                    @if($venue->amenities->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($venue->amenities as $amenity)
                                @php
                                    $iconName = strtolower($amenity->name);
                                    $icon = match(true) {
                                        Str::contains($iconName, ['mobil', 'parking', 'car']) => 'directions_car',
                                        Str::contains($iconName, ['motor', 'sepeda']) => 'two_wheeler',
                                        Str::contains($iconName, ['toilet', 'wc', 'restroom']) => 'wc',
                                        Str::contains($iconName, ['kantin', 'canteen', 'cafe', 'makan', 'minum']) => 'restaurant',
                                        Str::contains($iconName, ['wifi', 'wi-fi', 'internet']) => 'wifi',
                                        Str::contains($iconName, ['musholla', 'mushola', 'masjid', 'prayer']) => 'mosque',
                                        Str::contains($iconName, ['loker', 'locker']) => 'lock',
                                        Str::contains($iconName, ['ganti', 'changing']) => 'checkroom',
                                        Str::contains($iconName, ['ac', 'air conditioner']) => 'ac_unit',
                                        Str::contains($iconName, ['shower', 'mandi']) => 'shower',
                                        Str::contains($iconName, ['cctv', 'kamera']) => 'videocam',
                                        default => $amenity->icon ?? 'check_circle'
                                    };
                                @endphp
                                <div class="flex items-center gap-2 text-muted-light dark:text-gray-400">
                                    @if($icon === 'directions_car') <span class="material-symbols-outlined text-xl">directions_car</span>
                                    @elseif($icon === 'wc') <span class="material-symbols-outlined text-xl">wc</span>
                                    @elseif($icon === 'restaurant') <span class="material-symbols-outlined text-xl">restaurant</span>
                                    @elseif($icon === 'two_wheeler') <span class="material-symbols-outlined text-xl">two_wheeler</span>
                                    @elseif($icon === 'wifi') <span class="material-symbols-outlined text-xl">wifi</span>
                                    @elseif($icon === 'mosque') <span class="material-symbols-outlined text-xl">mosque</span>
                                    @else <span class="material-symbols-outlined text-xl">{{ $icon }}</span>
                                    @endif
                                    <span class="text-sm font-medium text-text-light dark:text-text-dark capitalize">{{ $amenity->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-muted-light">Fasilitas standar tersedia.</p>
                    @endif
                </div>

                <!-- Court Selection & Schedule -->
                <div class="space-y-6" id="court-selection">
                    <div class="flex items-center justify-between">
                         <div class="flex items-center gap-2">
                             <span class="material-symbols-outlined text-primary text-2xl animate-pulse">play_arrow</span>
                             <h2 class="text-2xl font-black text-text-light dark:text-text-dark uppercase italic">Pilih Lapangan</h2>
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
                                        @php
                                            // Get venue media by index corresponding to loop iteration
                                            $courtImage = $venue->media->get($loop->index); 
                                            $courtImageUrl = $courtImage ? Storage::url($courtImage->file_path) : 'https://placehold.co/400x300?text=No+Image';
                                        @endphp
                                        <img src="{{ $courtImageUrl }}" class="w-full h-full object-cover">
                                    </div>
                                    
                                    <!-- Middle: Info -->
                                    <div class="flex-1 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-xl font-black text-text-light dark:text-text-dark uppercase italic">{{ $court->name }}</h3>
                                            <a href="{{ route('courts.schedule', ['venue' => $venue->slug, 'venueCourt' => $court->id]) }}" class="hidden md:flex items-center gap-1 text-[10px] font-black uppercase text-text-light dark:text-text-dark hover:text-primary">
                                                Detail 
                                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                            </a>
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-2 text-[10px] font-bold text-muted-light uppercase tracking-wide">
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">{{ \App\Models\Venue::sportIcon($court->sport) }}</span> {{ $court->sport }}</span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">roofing</span> Indoor</span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">texture</span> {{ $court->floor_type ?? 'Karpet' }}</span>
                                        </div>

                                        <div class="pt-4 mt-2 border-t border-gray-50 dark:border-gray-800">
                                            <a href="{{ route('courts.schedule', ['venue' => $venue->slug, 'venueCourt' => $court->id]) }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 group-hover:scale-105 active:scale-95">
                                                <span class="material-symbols-outlined text-sm">schedule</span>
                                                Lihat & Pilih Jadwal
                                            </a>
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
                                 <span class="material-symbols-outlined fill-current">star</span>
                             </div>
                             <span class="text-xs text-muted-light tracking-wide">Berdasarkan 127 ulasan</span>
                         </div>
                         <div class="ml-auto">
                              <button class="text-[10px] font-black text-rose-500 hover:underline uppercase tracking-widest">Semua Ulasan</button>
                         </div>
                     </div>

                     <!-- Review Cards (Mock) -->
                     <div class="space-y-4">
                         <div class="p-6 bg-surface-light dark:bg-surface-dark rounded-[2rem] border border-gray-100 dark:border-gray-700">
                             <div class="flex items-center gap-4 mb-4">
                                 <div class="w-10 h-10 rounded-full bg-gray-200"></div>
                                 <div>
                                     <h5 class="font-bold text-sm">Indra Putra</h5>
                                     <p class="text-[10px] text-muted-light">Diulas 7 Februari 2026</p>
                                 </div>
                                 <div class="ml-auto px-2 py-1 bg-amber-100 text-amber-600 rounded-lg text-[10px] font-black flex items-center gap-1">
                                     <span class="material-symbols-outlined text-xs">star</span> 4.9
                                 </div>
                             </div>
                             <p class="text-xs text-muted-light leading-relaxed">"Mantaps, lapangannya enak dan AC nya dingin. Tempat parkir juga luas. Ayo semangat cari keringat hehehe..."</p>
                         </div>
                     </div>
                </div>

            </div>

            <!-- Right Column: Sticky Sidebar -->
            <div class="lg:col-span-1 lg:sticky lg:top-24 space-y-6">
                <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] p-8 shadow-2xl border border-gray-100 dark:border-gray-700">
                    <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest mb-1">Mulai Dari</p>
                    <div class="flex items-end gap-1 mb-6">
                        <h3 class="text-3xl font-black text-primary italic font-display">
                            Rp {{ number_format($venue->pricings()->min('price_per_hour') ?? 50000, 0, ',', '.') }}
                        </h3>
                        <span class="text-xs font-bold text-muted-light mb-1">/ Sesi</span>
                    </div>

                    <a href="#court-selection" onclick="document.querySelector('.court-selection-container')?.scrollIntoView({behavior: 'smooth'})" class="block w-full py-4 bg-primary text-white rounded-2xl font-black text-sm uppercase tracking-widest text-center hover:bg-primary-dark transition-all shadow-lg shadow-primary/30 transform hover:-translate-y-1 mb-8">
                        Cek Ketersediaan
                    </a>

                    <div class="space-y-4">
                        <h5 class="font-black text-sm text-text-light dark:text-text-dark">Booking lewat aplikasi lebih banyak keuntungan!</h5>
                        <ul class="space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-primary text-base">check_circle</span>
                                <span class="text-xs text-muted-light leading-snug">Opsi pembayaran down payment (DP)*</span>
                            </li>
                             <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-primary text-base">check_circle</span>
                                <span class="text-xs text-muted-light leading-snug">Reschedule jadwal booking*</span>
                            </li>
                             <li class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-primary text-base">check_circle</span>
                                <span class="text-xs text-muted-light leading-snug">Lebih banyak promo & voucher</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800 text-center">
                         <button 
                             @click="navigator.share({ title: '{{ $venue->name }}', text: 'Cek venue {{ $venue->name }} di Yomabar!', url: window.location.href })"
                             class="w-full flex items-center justify-center gap-2 px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl text-primary font-bold text-xs uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                         >
                             <span class="material-symbols-outlined text-sm">share</span>
                             Bagikan Venue
                         </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
