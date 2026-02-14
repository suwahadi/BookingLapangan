<div class="space-y-10 pb-20">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            <div>
                <a href="{{ route('member.dashboard') }}" class="inline-flex items-center gap-2 text-muted-light font-bold text-[10px] uppercase tracking-[0.2em] mb-4 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali ke Dashboard
                </a>
                <h1 class="text-3xl md:text-4xl font-black text-text-light dark:text-text-dark tracking-tight font-display italic uppercase">Dompet <span class="text-primary">Saya</span></h1>
                <p class="text-muted-light mt-2 tracking-tight">Kelola saldo dan riwayat transaksi booking Anda</p>
            </div>
            
            <a href="{{ route('member.wallet.withdraw') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border-2 border-primary text-primary rounded-2xl font-black text-xs tracking-[0.2em] hover:bg-primary hover:text-white transition-all shadow-lg hover:shadow-primary/30 group">
                <span class="material-symbols-outlined group-hover:-translate-y-0.5 transition-transform">payments</span>
                TARIK SALDO (WITHDRAW)
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Sidebar: Wallet Card -->
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-primary/90 rounded-[2.5rem] p-8 text-white shadow-card relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 opacity-10 group-hover:scale-110 transition-transform duration-700">
                         <span class="material-symbols-outlined text-[15rem]">account_balance_wallet</span>
                    </div>

                    <div class="relative z-10">
                        <p class="text-[10px] font-black uppercase tracking-widest text-white/80 mb-2">Total Saldo Aktif</p>
                        <p class="text-4xl lg:text-5xl font-black tracking-tight font-display italic">
                            Rp {{ number_format($wallet->balance - $pendingWithdrawAmount, 0, ',', '.') }}
                        </p>
                        @if($pendingWithdrawAmount > 0)
                            <p class="text-[10px] font-bold text-white/60 mt-1 uppercase tracking-wider">
                                Saldo Ditahan: Rp {{ number_format($pendingWithdrawAmount, 0, ',', '.') }}
                            </p>
                        @endif
                        <p class="text-xs font-medium text-white/80 mt-6 leading-relaxed flex items-center gap-2">
                             <span class="material-symbols-outlined text-lg">info</span>
                            Saldo ini dapat digunakan untuk pembayaran booking lapangan secara instan tanpa perlu transfer bank.
                        </p>
                    </div>
                </div>

                <!-- Filters for Mobile (Optional or Stacked) -->
                <div class="bg-surface-light dark:bg-surface-dark rounded-[2rem] p-6 shadow-card border border-gray-100 dark:border-gray-700 lg:sticky lg:top-8">
                    <h3 class="text-sm font-black text-text-light dark:text-text-dark uppercase tracking-widest mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">filter_alt</span>
                        Filter Transaksi
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-bold text-muted-light uppercase tracking-widest mb-1 block">Cari</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-light material-symbols-outlined text-base">search</span>
                                <input wire:model.live="search" type="text" placeholder="Deskripsi..." class="w-full pl-9 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl text-xs font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary transition-shadow">
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-muted-light uppercase tracking-widest mb-1 block">Tipe</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-light material-symbols-outlined text-base">format_list_bulleted</span>
                                <select wire:model.live="type" class="w-full pl-9 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl text-xs font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary cursor-pointer appearance-none">
                                    <option value="">Semua</option>
                                    <option value="CREDIT">Pemasukan (+)</option>
                                    <option value="DEBIT">Pengeluaran (-)</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-muted-light uppercase tracking-widest mb-1 block">Dari</label>
                                <input wire:model.live="startDate" type="date" class="w-full px-3 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl text-xs font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary cursor-pointer">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-muted-light uppercase tracking-widest mb-1 block">Sampai</label>
                                <input wire:model.live="endDate" type="date" class="w-full px-3 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-xl text-xs font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary cursor-pointer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main: Transactions -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] shadow-card border border-gray-100 dark:border-gray-700 overflow-hidden min-h-[500px]">
                    <div class="p-8 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between">
                         <h2 class="text-xl font-black text-text-light dark:text-text-dark uppercase italic tracking-tight">Riwayat <span class="text-primary">Mutasi</span></h2>
                         <div class="text-[10px] font-bold text-muted-light uppercase tracking-widest bg-gray-50 dark:bg-gray-800 px-3 py-1 rounded-lg">
                             {{ $entries->total() }} Transaksi
                         </div>
                    </div>

                    <div class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($entries as $entry)
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-6 group">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl {{ $entry->type === 'CREDIT' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400' }} flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform shadow-sm">
                                        @if($entry->type === 'CREDIT')
                                            <span class="material-symbols-outlined">add</span>
                                        @else
                                            <span class="material-symbols-outlined">remove</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-text-light dark:text-text-dark uppercase tracking-wide group-hover:text-primary transition-colors">{{ $entry->description ?? ($entry->type === 'CREDIT' ? 'Topup Saldo' : 'Pembayaran Booking') }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border
                                                {{ $entry->type === 'CREDIT' ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : 'bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800' }}">
                                                {{ $entry->type === 'CREDIT' ? 'IN' : 'OUT' }}
                                            </span>
                                            <span class="text-[10px] font-bold text-muted-light uppercase tracking-widest flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[10px]">schedule</span>
                                                {{ $entry->created_at->format('d M Y • H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right pl-14 md:pl-0">
                                    <p class="text-xl font-black font-display italic {{ $entry->type === 'CREDIT' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                        {{ $entry->type === 'CREDIT' ? '+' : '-' }} Rp {{ number_format($entry->amount, 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest mt-0.5">
                                        Sisa: Rp {{ number_format($entry->balance_after, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-20 text-center">
                                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6">
                                    <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600">receipt_long</span>
                                </div>
                                <h3 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic">Belum Ada Transaksi</h3>
                                <p class="text-muted-light text-xs tracking-widest mt-2 max-w-xs opacity-70">
                                    Semua riwayat saldo member akan muncul disini.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    @if($entries->hasPages())
                    <div class="p-8 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                        {{ $entries->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
