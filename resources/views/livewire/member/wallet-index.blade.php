<div class="space-y-10 pb-20">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-indigo-400 font-black text-[10px] uppercase tracking-[0.2em] mb-4 hover:text-gray-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali ke Beranda
                </a>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Dompet <span class="text-indigo-600">Saya</span></h1>
                <p class="text-gray-500 font-bold mt-2 tracking-tight">Kelola saldo dan riwayat transaksi booking Anda.</p>
            </div>
            
            <a href="{{ route('member.wallet.withdraw') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-indigo-600 text-indigo-600 rounded-2xl font-black text-xs tracking-[0.2em] hover:bg-indigo-600 hover:text-white transition-all shadow-xl hover:shadow-indigo-200">
                TARIK SALDO (WITHDRAW)
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Sidebar: Wallet Card -->
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-gradient-to-br from-indigo-900 to-indigo-800 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                         <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M21 18v1c0 1.1-.9 2-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v1h-9a2 2 0 00-2 2v8a2 2 0 002 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                    </div>

                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-200 mb-2">Total Saldo Aktif</p>
                        <p class="text-4xl lg:text-5xl font-black tracking-tight font-display italic">
                            Rp {{ number_format($wallet->balance, 0, ',', '.') }}
                        </p>
                        <p class="text-xs font-medium text-indigo-200 mt-6 leading-relaxed">
                            Saldo ini dapat digunakan untuk pembayaran booking lapangan secara instan tanpa perlu transfer bank.
                        </p>
                    </div>
                </div>

                <!-- Filters for Mobile (Optional or Stacked) -->
                <div class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-50 lg:sticky lg:top-8">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4">Filter Transaksi</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Cari</label>
                            <input wire:model.live="search" type="text" placeholder="Deskripsi..." class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-600 transition-shadow">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Tipe</label>
                            <select wire:model.live="type" class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-600 cursor-pointer">
                                <option value="">Semua</option>
                                <option value="CREDIT">Pemasukan (+)</option>
                                <option value="DEBIT">Pengeluaran (-)</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Dari</label>
                                <input wire:model.live="startDate" type="date" class="w-full px-3 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-600 cursor-pointer">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Sampai</label>
                                <input wire:model.live="endDate" type="date" class="w-full px-3 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-600 cursor-pointer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main: Transactions -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-50 overflow-hidden min-h-[500px]">
                    <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                         <h2 class="text-xl font-black text-gray-900 uppercase italic tracking-tight">Riwayat <span class="text-indigo-600">Mutasi</span></h2>
                         <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                             {{ $entries->total() }} Transaksi
                         </div>
                    </div>

                    <div class="divide-y divide-gray-50">
                        @forelse($entries as $entry)
                            <div class="p-6 hover:bg-gray-50 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-6 group">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl {{ $entry->type === 'CREDIT' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                        @if($entry->type === 'CREDIT')
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900 uppercase tracking-wide">{{ $entry->description ?? ($entry->type === 'CREDIT' ? 'Topup Saldo' : 'Pembayaran Booking') }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest {{ $entry->type === 'CREDIT' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                {{ $entry->type === 'CREDIT' ? 'IN' : 'OUT' }}
                                            </span>
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                {{ $entry->created_at->format('d M Y • H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right pl-14 md:pl-0">
                                    <p class="text-xl font-black font-display italic {{ $entry->type === 'CREDIT' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $entry->type === 'CREDIT' ? '+' : '-' }} Rp {{ number_format($entry->amount, 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                        Sisa: Rp {{ number_format($entry->balance_after, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-20 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-black text-gray-900 uppercase italic">Belum Ada Transaksi</h3>
                                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mt-2 max-w-xs">
                                    Semua riwayat pengisian dan penggunaan saldo akan muncul di sini.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    @if($entries->hasPages())
                    <div class="p-8 border-t border-gray-50">
                        {{ $entries->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
