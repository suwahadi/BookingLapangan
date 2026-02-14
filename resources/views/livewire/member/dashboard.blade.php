<div class="max-w-6xl mx-auto py-12 px-4">
    <!-- Header -->
    <div class="mb-10">
        <h3 class="text-xl md:text-2xl font-black text-text-light dark:text-text-dark tracking-tight uppercase italic">
            Halo, <span class="text-primary">{{ auth()->user()->name }}</span>
        </h3>
        <p class="text-muted-light mt-2 uppercase text-xs tracking-widest">Selamat datang di dashboard member</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Wallet Balance -->
        <a href="{{ route('member.wallet') }}" class="bg-primary/90 rounded-[2rem] p-6 text-white shadow-card hover:shadow-2xl transition-all transform hover:-translate-y-1 relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <span class="material-symbols-outlined text-[8rem]">account_balance_wallet</span>
            </div>
            
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined">account_balance_wallet</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/80 mb-1">Saldo Wallet</p>
                    <p class="text-2xl font-black font-display italic">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                </div>
            </div>
        </a>

        <!-- Upcoming Bookings -->
        <a href="{{ route('member.bookings') }}" class="bg-surface-light dark:bg-surface-dark rounded-[2rem] p-6 shadow-card border border-gray-100 dark:border-gray-700 hover:border-primary/50 transition-all hover:shadow-2xl relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <span class="material-symbols-outlined text-[8rem] text-primary">calendar_clock</span>
            </div>

            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                    <span class="material-symbols-outlined">event_available</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted-light mb-1">Booking Aktif</p>
                    <p class="text-2xl font-black text-text-light dark:text-text-dark">{{ $upcomingBookings->count() }}</p>
                </div>
            </div>
        </a>

        <!-- Notifications -->
        <a href="{{ route('member.notifications') }}" class="bg-surface-light dark:bg-surface-dark rounded-[2rem] p-6 shadow-card border border-gray-100 dark:border-gray-700 hover:border-primary/50 transition-all hover:shadow-2xl relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <span class="material-symbols-outlined text-[8rem] text-primary">notifications</span>
            </div>

            <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <span class="material-symbols-outlined">notifications_active</span>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted-light mb-1">Notifikasi Baru</p>
                    <div class="flex items-center gap-2">
                         <p class="text-2xl font-black text-text-light dark:text-text-dark">{{ $unreadNotifications }}</p>
                         @if($unreadNotifications > 0)
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                            </span>
                         @endif
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Upcoming Bookings -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] shadow-card border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between">
                <h2 class="text-lg font-black text-text-light dark:text-text-dark uppercase tracking-tight">Booking Mendatang</h2>
                <a href="{{ route('member.bookings') }}" class="text-[10px] font-black uppercase tracking-widest text-primary hover:text-primary-dark hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($upcomingBookings as $booking)
                    <a href="{{ route('bookings.show', $booking) }}" class="block p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-primary/10 flex flex-col items-center justify-center shrink-0 border border-primary/20 group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="text-lg font-black text-primary group-hover:text-white transition-colors">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d') }}</span>
                                <span class="text-[8px] font-bold uppercase text-primary/70 group-hover:text-white/80 transition-colors">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-text-light dark:text-text-dark uppercase truncate group-hover:text-primary transition-colors">{{ $booking->venue->name ?? 'Venue' }}</p>
                                <div class="flex flex-wrap items-center gap-1 mt-1">
                                    @foreach($booking->grouped_slots as $slot)
                                        <span class="inline-flex items-center px-2 py-0.5 bg-primary/10 text-primary rounded text-[10px] font-bold font-mono">
                                            {{ $slot['start'] }} - {{ $slot['end'] }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest border shadow-sm
                                {{ $booking->status->value === 'CONFIRMED' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800' }}">
                                {{ $booking->status->value }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-3xl">event_busy</span>
                        </div>
                        <p class="text-muted-light text-sm">Belum ada booking mendatang</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] shadow-card border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-50 dark:border-gray-800">
                <h2 class="text-lg font-black text-text-light dark:text-text-dark uppercase tracking-tight">Aktivitas Terbaru</h2>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($recentBookings as $booking)
                    <a href="{{ route('bookings.show', $booking) }}" class="block p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl 
                                {{ $booking->status->value === 'CONFIRMED' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : ($booking->status->value === 'CANCELLED' ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400' : 'bg-gray-100 dark:bg-gray-800 text-muted-light') }} 
                                flex items-center justify-center shrink-0">
                                @if($booking->status->value === 'CONFIRMED')
                                    <span class="material-symbols-outlined text-lg">check_circle</span>
                                @elseif($booking->status->value === 'CANCELLED')
                                    <span class="material-symbols-outlined text-lg">cancel</span>
                                @else
                                    <span class="material-symbols-outlined text-lg">schedule</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-text-light dark:text-text-dark uppercase group-hover:text-primary transition-colors flex items-center gap-2">
                                     <span class="material-symbols-outlined text-xs text-muted-light">receipt</span>
                                    {{ $booking->booking_code }}
                                </p>
                                <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest mt-1">
                                    {{ $booking->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <p class="text-sm font-black text-primary font-display italic">
                                Rp {{ number_format($booking->payable_amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center">
                         <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-3xl">history</span>
                        </div>
                        <p class="text-muted-light text-sm">Belum ada aktivitas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Review Prompts Section -->
    @if(isset($eligibleReviews) && $eligibleReviews->isNotEmpty())
        <div class="mb-10 w-full" x-data="{
            activeSlide: 0,
            slidesCount: {{ $eligibleReviews->count() }},
            itemsPerSlide: 1,
            init() {
                this.updateItemsPerSlide();
                window.addEventListener('resize', () => this.updateItemsPerSlide());
            },
            updateItemsPerSlide() {
                if (window.innerWidth >= 1024) this.itemsPerSlide = 3;
                else if (window.innerWidth >= 768) this.itemsPerSlide = 2;
                else this.itemsPerSlide = 1;
                // Correct activeSlide if it goes out of bounds after resize
                if (this.activeSlide > this.slidesCount - this.itemsPerSlide) {
                    this.activeSlide = Math.max(0, this.slidesCount - this.itemsPerSlide);
                }
            },
            next() {
                if (this.activeSlide < this.slidesCount - this.itemsPerSlide) {
                    this.activeSlide++;
                }
            },
            prev() {
                 if (this.activeSlide > 0) {
                    this.activeSlide--;
                }
            }
        }">
            <div class="flex items-center justify-end mb-6">
                <!-- Navigation -->
                <div class="flex gap-2" x-show="slidesCount > itemsPerSlide">
                    <button @click="prev()" 
                        :class="{'opacity-50 cursor-not-allowed': activeSlide === 0, 'hover:border-primary hover:text-primary': activeSlide > 0}"
                        :disabled="activeSlide === 0"
                        class="w-8 h-8 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-muted-light flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">chevron_left</span>
                    </button>
                    <button @click="next()" 
                        :class="{'opacity-50 cursor-not-allowed': activeSlide >= slidesCount - itemsPerSlide, 'hover:border-primary hover:text-primary': activeSlide < slidesCount - itemsPerSlide}"
                        :disabled="activeSlide >= slidesCount - itemsPerSlide"
                        class="w-8 h-8 rounded-lg bg-surface-light dark:bg-surface-dark border border-gray-200 dark:border-gray-700 text-muted-light flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">chevron_right</span>
                    </button>
                </div>
            </div>

            <div class="overflow-hidden p-1 -m-1">
                <div class="flex transition-transform duration-500 ease-out"
                     :style="`transform: translateX(-${activeSlide * (100 / itemsPerSlide)}%)`">
                    @foreach($eligibleReviews as $booking)
                        <div class="shrink-0 px-2 transition-all duration-300"
                             :style="`width: ${100 / itemsPerSlide}%`">
                             <livewire:member.review-prompt-card :booking="$booking" :wire:key="'review-'.$booking->id" class="h-full" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="mt-10 bg-text-light dark:bg-black rounded-[2.5rem] p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-2xl relative overflow-hidden group">
        <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
        <div class="absolute -right-10 -bottom-10 opacity-10 rotate-12 group-hover:scale-110 transition-transform duration-700">
             <span class="material-symbols-outlined text-[12rem] text-white">emoji_events</span>
        </div>
        
        <div class="relative z-10 text-center md:text-left">
            <h3 class="text-2xl font-black text-white uppercase tracking-tight italic">Mau Main Lagi?</h3>
            <p class="text-white/60 font-bold mt-1 text-sm">Temukan lapangan terbaik di sekitarmu</p>
        </div>
        <a href="{{ route('home') }}" class="relative z-10 px-8 py-4 bg-primary text-white text-sm font-black uppercase tracking-widest rounded-2xl hover:bg-primary-dark transition-all shadow-lg shadow-black/50 transform hover:-translate-y-1 flex items-center gap-3">
             <span class="material-symbols-outlined">search</span>
            Cari Lapangan
        </a>
    </div>
</div>
