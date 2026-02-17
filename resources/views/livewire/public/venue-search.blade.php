@php
    $sportCategories = [
        ['key' => '', 'name' => 'Semua', 'icon' => 'emoji_events', 'color' => 'gray'],
        ['key' => 'futsal', 'name' => 'Futsal', 'icon' => 'sports_soccer', 'color' => 'red'],
        ['key' => 'badminton', 'name' => 'Badminton', 'icon' => 'sports_tennis', 'color' => 'blue'],
        ['key' => 'basket', 'name' => 'Basket', 'icon' => 'sports_basketball', 'color' => 'orange'],
        ['key' => 'mini soccer', 'name' => 'Mini Soccer', 'icon' => 'sports_soccer', 'color' => 'green'],
        ['key' => 'tennis', 'name' => 'Tenis', 'icon' => 'sports_tennis', 'color' => 'lime'],
        ['key' => 'padel', 'name' => 'Padel', 'icon' => 'sports_tennis', 'color' => 'emerald'],
        ['key' => 'voli', 'name' => 'Voli', 'icon' => 'sports_volleyball', 'color' => 'yellow'],
    ];
@endphp

<div class="min-h-screen">
    <!-- Hero Section with Background Image -->
    <div class="relative min-h-[85vh] flex flex-col">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="https://yomabar.web.id/storage/hero/hero_slide_001.webp" 
                 alt="Hero Slide" 
                 fetchpriority="high"
                 loading="eager"
                 decoding="async"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/60"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 flex-1 flex items-center">
            <div class="max-w-7xl mx-auto px-4 lg:px-8 w-full py-16">
                <div class="max-w-2xl">
                    <!-- Title -->
                    <h1 class="text-5xl lg:text-7xl font-black text-white leading-tight mb-6" style="font-style: italic;">
                       Yomabar<br>
                        Sport Booking
                    </h1>
                    
                    <!-- Subtitle -->
                    <p class="text-white/90 text-lg lg:text-xl font-medium leading-relaxed mb-10 max-w-lg">
                        Platform all-in-one untuk sewa lapangan online. Olahraga makin mudah dan menyenangkan!
                    </p>

                    <!-- App Store Buttons -->
                    <div class="flex flex-wrap gap-4">
                        <a href="#" class="flex items-center gap-3 bg-black/80 backdrop-blur-sm text-white px-5 py-3 rounded-xl hover:bg-black transition-colors border border-white/20">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                            </svg>
                            <div>
                                <div class="text-[10px] uppercase tracking-wider opacity-80">Get it on</div>
                                <div class="font-bold text-sm">Google Play</div>
                            </div>
                        </a>
                        <a href="#" class="flex items-center gap-3 bg-black/80 backdrop-blur-sm text-white px-5 py-3 rounded-xl hover:bg-black transition-colors border border-white/20">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.71,19.5C17.88,20.74 17,21.95 15.66,21.97C14.32,22 13.89,21.18 12.37,21.18C10.84,21.18 10.37,21.95 9.1,22C7.79,22.05 6.8,20.68 5.96,19.47C4.25,17 2.94,12.45 4.7,9.39C5.57,7.87 7.13,6.91 8.82,6.88C10.1,6.86 11.32,7.75 12.11,7.75C12.89,7.75 14.37,6.68 15.92,6.84C16.57,6.87 18.39,7.1 19.56,8.82C19.47,8.88 17.39,10.1 17.41,12.63C17.44,15.65 20.06,16.66 20.09,16.67C20.06,16.74 19.67,18.11 18.71,19.5M13,3.5C13.73,2.67 14.94,2.04 15.94,2C16.07,3.17 15.6,4.35 14.9,5.19C14.21,6.04 13.07,6.7 11.95,6.61C11.8,5.46 12.36,4.26 13,3.5Z"/>
                            </svg>
                            <div>
                                <div class="text-[10px] uppercase tracking-wider opacity-80">Get it on</div>
                                <div class="font-bold text-sm">App Store</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar (Bottom) -->
        <div class="relative z-20 -mb-10">
            <div class="max-w-5xl mx-auto px-4">
                <div class="bg-[#8B1538] rounded-2xl p-3 md:p-4 flex flex-col md:flex-row items-stretch gap-3 shadow-2xl">
                    
                    <!-- Aktivitas -->
                    <div class="flex-1 bg-[#7A1230] rounded-xl px-4 py-3 flex items-center gap-3 border-r border-white/10">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-white">calendar_month</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-white/60 text-[10px] font-bold uppercase tracking-wider">Aktivitas</div>
                            <div class="text-white font-bold text-sm truncate">Sewa Lapangan</div>
                        </div>
                    </div>

                    <!-- Lokasi -->
                    <div class="flex-1 bg-[#7A1230] rounded-xl px-4 py-3 flex items-center gap-3 border-r border-white/10">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-white">location_on</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label for="search-city" class="text-white/60 text-[10px] font-bold uppercase tracking-wider block">Lokasi</label>
                            <input wire:model.live.debounce.300ms="keyword" 
                                   type="text" 
                                   id="search-city"
                                   placeholder="Pilih Kota" 
                                   class="bg-transparent border-none text-white placeholder-white/80 focus:ring-0 w-full font-bold text-sm p-0">
                        </div>
                    </div>

                    <!-- Cabang Olahraga -->
                    <div class="flex-1 bg-[#7A1230] rounded-xl px-4 py-3 flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-white">groups</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label for="sport-type-select" class="text-white/60 text-[10px] font-bold uppercase tracking-wider block">Cabang Olahraga</label>
                            <select wire:model.live="sport_type" 
                                    id="sport-type-select"
                                    aria-label="Pilih Cabang Olahraga"
                                    class="bg-transparent border-none text-white focus:ring-0 w-full font-bold text-sm p-0 cursor-pointer [&>option]:text-gray-900">
                                <option value="">Pilih Cabang Olahraga</option>
                                @foreach($sportCategories as $cat)
                                    @if($cat['key'] !== '')
                                        <option value="{{ $cat['key'] }}">{{ $cat['name'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Search Button -->
                    <button wire:click="search" 
                            class="bg-white text-[#8B1538] px-8 py-4 rounded-xl font-bold text-sm hover:bg-gray-100 transition-colors flex items-center justify-center gap-2 shadow-lg whitespace-nowrap">
                        Temukan
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 lg:px-6 pt-20 pb-16">
        <div class="flex flex-col lg:flex-row gap-12">
            
            <!-- Sidebar: Categories -->
            <aside class="hidden md:block lg:w-64 flex-shrink-0">
                <div class="lg:sticky lg:top-24 space-y-4">
                    <!-- Category Title -->
                    <div class="mb-4">
                        <h2 class="font-black text-sm text-gray-900 uppercase tracking-widest">Kategori Olahraga</h2>
                    </div>

                    <!-- Category List -->
                    <div class="space-y-1.5">
                        @foreach($sportCategories as $cat)
                            <div wire:key="cat-desktop-{{ $cat['key'] }}">
                            @php
                                $isActive = $sport_type === $cat['key'];
                                $colorClasses = match($cat['color']) {
                                    'red' => 'bg-red-50 text-red-600',
                                    'blue' => 'bg-blue-50 text-blue-600',
                                    'orange' => 'bg-orange-50 text-orange-600',
                                    'green' => 'bg-green-50 text-green-600',
                                    'lime' => 'bg-lime-50 text-lime-600',
                                    'yellow' => 'bg-yellow-50 text-yellow-600',
                                    'emerald' => 'bg-emerald-50 text-emerald-600',
                                    'purple' => 'bg-purple-50 text-purple-600',
                                    'pink' => 'bg-pink-50 text-pink-600',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                                $activeColorClasses = match($cat['color']) {
                                    'red' => 'bg-red-600 text-white',
                                    'blue' => 'bg-blue-600 text-white',
                                    'orange' => 'bg-orange-600 text-white',
                                    'green' => 'bg-green-600 text-white',
                                    'lime' => 'bg-lime-600 text-white',
                                    'yellow' => 'bg-yellow-500 text-white',
                                    'emerald' => 'bg-emerald-600 text-white',
                                    'purple' => 'bg-purple-600 text-white',
                                    'pink' => 'bg-pink-600 text-white',
                                    default => 'bg-[#8B1538] text-white',
                                };
                            @endphp
                            <button wire:click="$set('sport_type', '{{ $cat['key'] }}')"
                                    class="flex items-center gap-4 w-full p-3 rounded-xl transition-all duration-200
                                           {{ $isActive 
                                               ? 'bg-[#8B1538] text-white shadow-lg shadow-[#8B1538]/20' 
                                               : 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-100 hover:border-[#8B1538]/30' 
                                           }}">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors
                                            {{ $isActive ? 'bg-white/20 text-white' : $colorClasses }}">
                                    {!! \App\Models\Venue::getSportSvg($cat['key'], 'w-6 h-6') !!}
                                </div>
                                <span class="font-bold text-sm">{{ $cat['name'] }}</span>
                                @if($isActive)
                                    <span class="material-symbols-outlined ml-auto text-lg">check_circle</span>
                                @endif
                            </button>
                            </div>
                        @endforeach
                    </div>

                    <!-- CTA Card -->
                    <div class="bg-gradient-to-br from-[#8B1538] to-[#6B1028] rounded-2xl p-5 text-white mt-6">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-2xl">travel_explore</span>
                        </div>
                        <h3 class="font-black text-lg mb-2">Punya Lapangan?</h3>
                        <p class="text-white/80 text-sm mb-4 leading-relaxed">
                            Daftarkan venue Anda dan jangkau ribuan atlet setiap harinya.
                        </p>
                        @auth
                            <a href="{{ route('member.dashboard') }}" wire:navigate class="block text-center w-full bg-white text-[#8B1538] py-3 rounded-xl text-sm font-black hover:bg-gray-100 transition-colors">
                                Member Area
                            </a>
                        @else
                            <button onclick="Livewire.dispatch('openAuthModal', { mode: 'register' })" class="w-full bg-white text-[#8B1538] py-3 rounded-xl text-sm font-black hover:bg-gray-100 transition-colors">
                                Daftar Sekarang
                            </button>
                        @endauth
                    </div>
                </div>
            </aside>

            <!-- Main Content: Venue Grid -->
            <section class="flex-1">
                <!-- Mobile Category Scroll -->
                <div class="lg:hidden mb-8 -mx-4 px-4">
                    <div class="flex gap-3 overflow-x-auto no-scrollbar pb-2">
                        @foreach($sportCategories as $cat)
                            <div wire:key="cat-mobile-{{ $cat['key'] }}">
                            @php $isActive = $sport_type === $cat['key']; @endphp
                            <button wire:click="$set('sport_type', '{{ $cat['key'] }}')"
                            class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-bold transition-all whitespace-nowrap
                               {{ $isActive 
                                   ? 'bg-[#8B1538] text-white' 
                                   : 'bg-white text-gray-600 border border-gray-200' 
                               }}">
                                {{ $cat['name'] }}
                            </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-black text-xl text-gray-900">
                            @if($sport_type)
                                Venue {{ ucfirst($sport_type) }}
                            @else
                                Semua Venue
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $venues->total() }} venue ditemukan</p>
                    </div>
                </div>

                <!-- Venue Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @forelse($venues as $venue)
                        <div wire:key="venue-{{ $venue->id }}" class="contents">
                        <a href="{{ route('public.venues.show', $venue->slug) }}" 
                           aria-label="Lihat detail venue {{ $venue->name }}"
                           class="group bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-xl hover:border-[#8B1538]/20 transition-all duration-300">
                            <!-- Image -->
                            <div class="relative h-48 bg-gray-100 overflow-hidden">
                                @php 
                                    $cov = $venue->media->firstWhere('is_cover', true) ?? $venue->media->first(); 
                                    $lazy = $loop->index > 2;
                                @endphp
                                <img alt="{{ $venue->name }}" title="{{ $venue->name }}" loading="{{ $lazy ? 'lazy' : 'eager' }}" decoding="async" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                     src="{{ $cov ? Storage::url($cov->file_path) : 'https://placehold.co/600x400?text=No+Image' }}" />
                                
                                <!-- Sport Badge -->
                                <div class="absolute top-3 left-3 bg-[#8B1538] text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider flex items-center gap-1.5 shadow-sm">
                                    <span>{{ $venue->sport_type ?? 'Multi' }}</span>
                                </div>

                                <!-- Favorite -->
                                <button class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/80 backdrop-blur-sm flex items-center justify-center text-gray-500 hover:text-red-500 hover:bg-white transition-all">
                                    <span class="material-symbols-outlined text-xl">favorite</span>
                                </button>
                            </div>

                            <!-- Content -->
                            <div class="p-5">
                                <h3 class="font-black text-lg text-gray-900 leading-tight group-hover:text-[#8B1538] transition-colors mb-2">
                                    {{ $venue->name }}
                                </h3>
                                
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                                    <span class="material-symbols-outlined text-yellow-500 text-base">star</span>
                                    <span class="font-bold text-gray-900">{{ number_format($venue->rating_avg, 1) }}</span>
                                    <span class="text-gray-300">•</span>
                                    <span class="material-symbols-outlined text-sm">location_on</span>
                                    <span class="truncate">{{ $venue->city }}</span>
                                </div>

                                <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                                    <div>
                                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Mulai dari</span>
                                        <div class="text-[#8B1538] text-xl font-black">
                                            Rp {{ number_format($venue->pricings_min_price_per_hour ?? 50000, 0, ',', '.') }}
                                            <span class="text-xs text-gray-500 font-medium">/jam</span>
                                        </div>
                                    </div>
                                    <div class="bg-[#8B1538] text-white px-4 py-2 rounded-xl text-xs font-bold group-hover:bg-[#6B1028] transition-colors">
                                        Lihat
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div> <!-- end wire:key wrapper -->
                    @empty
                        <div class="col-span-full text-center py-20 bg-gray-50 rounded-2xl">
                            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="material-symbols-outlined text-4xl text-gray-300">search_off</span>
                            </div>
                            <h3 class="font-bold text-gray-500 mb-2">Venue tidak ditemukan</h3>
                            <p class="text-sm text-gray-400">Coba ubah kata kunci atau kategori pencarian Anda.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $venues->links() }}
                </div>

                <!-- FAQ Section -->
                <div class="mt-16 pt-10 border-t border-gray-100">
                    <h2 class="text-xl font-black text-gray-900 mb-6 text-center">Tanya Jawab (FAQ)</h2>
                    <div class="max-w-2xl mx-auto space-y-2">
                        <details class="group bg-white rounded-xl border border-gray-100 overflow-hidden">
                            <summary class="flex justify-between items-center p-5 cursor-pointer list-none hover:bg-gray-50 transition-colors">
                                <h3 class="font-bold text-sm text-gray-900">Bagaimana cara memesan lapangan?</h3>
                                <span class="material-symbols-outlined text-gray-400 transition-transform group-open:rotate-180">expand_more</span>
                            </summary>
                            <div class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                                Pilih venue favorit Anda, tentukan jadwal yang tersedia, dan selesaikan pembayaran melalui berbagai metode yang tersedia.
                            </div>
                        </details>
                        <details class="group bg-white rounded-xl border border-gray-100 overflow-hidden">
                            <summary class="flex justify-between items-center p-5 cursor-pointer list-none hover:bg-gray-50 transition-colors">
                                <h3 class="font-bold text-sm text-gray-900">Apa saja metode pembayarannya?</h3>
                                <span class="material-symbols-outlined text-gray-400 transition-transform group-open:rotate-180">expand_more</span>
                            </summary>
                            <div class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                                Kami mendukung transfer bank, e-wallet (Gopay, OVO, ShopeePay), dan kartu kredit untuk memudahkan transaksi Anda.
                            </div>
                        </details>
                        <details class="group bg-white rounded-xl border border-gray-100 overflow-hidden">
                            <summary class="flex justify-between items-center p-5 cursor-pointer list-none hover:bg-gray-50 transition-colors">
                                <h3 class="font-bold text-sm text-gray-900">Apakah bisa membatalkan booking?</h3>
                                <span class="material-symbols-outlined text-gray-400 transition-transform group-open:rotate-180">expand_more</span>
                            </summary>
                            <div class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                                Ya, Anda dapat membatalkan booking sesuai dengan kebijakan pembatalan masing-masing venue. Silakan cek detail kebijakan di halaman venue.
                            </div>
                        </details>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
