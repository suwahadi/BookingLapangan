<div class="max-w-6xl mx-auto py-12 px-4">
    <!-- Header -->
    <div class="mb-10">
        <h1 class="text-4xl font-black text-gray-900 tracking-tight uppercase italic">
            Halo, <span class="text-indigo-600">{{ auth()->user()->name }}</span>
        </h1>
        <p class="text-gray-500 font-bold mt-2 uppercase text-xs tracking-widest">Selamat datang di dashboard member</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Wallet Balance -->
        <a href="{{ route('member.wallet') }}" class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-6 text-white shadow-xl hover:shadow-2xl transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-white/60">Saldo Wallet</p>
                    <p class="text-2xl font-black">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                </div>
            </div>
        </a>

        <!-- Upcoming Bookings -->
        <a href="{{ route('member.bookings') }}" class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 hover:shadow-2xl transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Booking Aktif</p>
                    <p class="text-2xl font-black text-gray-900">{{ $upcomingBookings->count() }}</p>
                </div>
            </div>
        </a>

        <!-- Notifications -->
        <a href="{{ route('member.notifications') }}" class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 hover:shadow-2xl transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Notifikasi Baru</p>
                    <p class="text-2xl font-black text-gray-900">{{ $unreadNotifications }}</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Upcoming Bookings -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">Booking Mendatang</h2>
                <a href="{{ route('member.bookings') }}" class="text-xs font-black uppercase tracking-widest text-indigo-600 hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($upcomingBookings as $booking)
                    <a href="{{ route('bookings.show', $booking) }}" class="block p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-100 flex flex-col items-center justify-center shrink-0">
                                <span class="text-lg font-black text-indigo-600">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d') }}</span>
                                <span class="text-[8px] font-bold uppercase text-indigo-400">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M') }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-gray-900 uppercase truncate">{{ $booking->venue->name ?? 'Venue' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $booking->start_time }} - {{ $booking->end_time }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest 
                                {{ $booking->status->value === 'CONFIRMED' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $booking->status->value }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500 font-bold">Belum ada booking mendatang</p>
                        <a href="{{ route('home') }}" class="inline-block mt-4 px-6 py-3 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-colors">
                            Cari Lapangan
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50">
                <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">Aktivitas Terbaru</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentBookings as $booking)
                    <a href="{{ route('bookings.show', $booking) }}" class="block p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl 
                                {{ $booking->status->value === 'CONFIRMED' ? 'bg-emerald-100' : ($booking->status->value === 'CANCELLED' ? 'bg-rose-100' : 'bg-gray-100') }} 
                                flex items-center justify-center shrink-0">
                                @if($booking->status->value === 'CONFIRMED')
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                @elseif($booking->status->value === 'CANCELLED')
                                    <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-gray-900 uppercase">{{ $booking->booking_code }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                    {{ $booking->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <p class="text-sm font-black text-gray-900">
                                Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center">
                        <p class="text-gray-500 font-bold">Belum ada aktivitas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-10 bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl p-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <h3 class="text-2xl font-black text-white uppercase tracking-tight italic">Mau Main Lagi?</h3>
            <p class="text-slate-400 font-bold mt-1">Temukan lapangan terbaik di sekitarmu</p>
        </div>
        <a href="{{ route('home') }}" class="px-8 py-4 bg-indigo-600 text-white text-sm font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/30">
            Cari Lapangan
        </a>
    </div>
</div>
