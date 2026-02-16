<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Laporan <span class="text-indigo-600">Keuangan</span></h1>
            <p class="text-gray-500 mt-1 tracking-tight">Pantau performa pendapatan dari semua venue</p>
        </div>
        
        <button wire:click="exportCsv" class="bg-gray-900 text-white px-8 py-4 rounded-[1.5rem] font-black text-sm tracking-widest hover:bg-black transition-all transform active:scale-95 shadow-2xl shadow-gray-200 inline-flex items-center gap-2 group">
            <svg class="w-5 h-5 group-hover:translate-y-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
            EKSPOR CSV
        </button>
    </div>

    <!-- Filters & Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Filters Card -->
        <div class="lg:col-span-3 bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 flex flex-col md:flex-row gap-6 md:items-end">
            <div class="w-full md:flex-1 space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Mulai Tanggal</label>
                <input wire:model.live="startDate" type="date" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
            </div>
            <div class="w-full md:flex-1 space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Sampai Tanggal</label>
                <input wire:model.live="endDate" type="date" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
            </div>
            <div class="w-full md:flex-1 space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Filter Venue</label>
                <select wire:model.live="venueId" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black text-gray-900 focus:ring-2 focus:ring-indigo-600 appearance-none cursor-pointer">
                    <option value="">SEMUA VENUE</option>
                    @foreach($venues as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- summary revenue card -->
        <div class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-2xl shadow-indigo-200 text-white relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200 mb-2">Total Pendapatan</p>
                <h3 class="text-3xl font-black font-display italic tracking-tight">IDR {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <div class="mt-4 flex items-center gap-2">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-100">Verified Settlements</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet & Liability -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-gray-900 p-8 rounded-[2.5rem] shadow-2xl text-white relative overflow-hidden group">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Total Kewajiban Saldo (Liability)</p>
                    <h3 class="text-3xl font-black font-display italic tracking-tight">IDR {{ number_format($totalLiability, 0, ',', '.') }}</h3>
                    <p class="text-[10px] font-bold text-gray-400 mt-2 italic uppercase tracking-wider">— Total dana user di dalam sistem</p>
                </div>
                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-6 italic">Top 5 Saldo Terbesar</h3>
            <div class="space-y-4">
                @foreach($topWallets as $wallet)
                <div class="flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-[10px] font-black text-indigo-600">
                            {{ substr($wallet->user->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="text-sm font-bold text-gray-700">{{ $wallet->user->name ?? 'Deleted User' }}</span>
                    </div>
                    <span class="text-sm font-black text-gray-900">IDR {{ number_format($wallet->balance, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Small Daily Chart Placeholder -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50">
        <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-6 italic">Tren Harian <span class="text-gray-900">— {{ count($dailyRevenue) }} Hari Terdata</span></h3>
        <div class="flex items-end gap-2 h-32">
            @php($max = $dailyRevenue->max('total') ?: 1)
            @foreach($dailyRevenue as $day)
                <div class="flex-1 bg-indigo-50 hover:bg-indigo-600 rounded-lg relative group transition-all" style="height: {{ ($day->total / $max) * 100 }}%">
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-[8px] font-black rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                        {{ number_format($day->total/1000, 0) }}k
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Rincian Transaksi</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Venue & Lapangan</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Metode</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payments as $p)
                    <tr class="hover:bg-gray-50/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $p->provider_order_id }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                    {{ $p->paid_at?->translatedFormat('d F Y, H:i') ?? $p->created_at->translatedFormat('d F Y, H:i') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-gray-700">{{ $p->venue_name }}</p>
                            <p class="text-[10px] text-indigo-500 font-black uppercase tracking-widest mt-0.5">#{{ $p->court_name }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg border border-gray-200">
                                {{ $p->payment_method }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <span class="text-lg font-black font-display italic text-gray-900">IDR {{ number_format($p->amount, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <p class="text-gray-400 font-bold italic">Tidak ada transaksi ditemukan pada periode ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/20">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
