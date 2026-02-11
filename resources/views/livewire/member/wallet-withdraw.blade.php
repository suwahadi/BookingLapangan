<div class="max-w-xl mx-auto py-12 px-4">
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('member.wallet') }}" class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center text-muted-light hover:text-primary transition-colors group">
            <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
        </a>
        <h1 class="text-3xl font-black text-text-light dark:text-text-dark uppercase tracking-tight italic">Tarik <span class="text-primary">Saldo</span></h1>
    </div>

    <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] p-8 shadow-card border border-gray-100 dark:border-gray-700 relative overflow-hidden">
        <div class="mb-8 p-6 bg-primary/10 rounded-2xl border border-primary/20 flex items-center justify-between relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-primary mb-1 opacity-80">Saldo Tersedia</p>
                <p class="text-3xl font-black text-primary font-display italic">Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl shadow-sm flex items-center justify-center relative z-10">
                <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
            </div>
            
            <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-700">
                  <span class="material-symbols-outlined text-[6rem] text-primary">payments</span>
            </div>
        </div>

        <form wire:submit.prevent="confirmWithdraw" class="space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-muted-light uppercase tracking-[0.2em] ml-2">Nominal Penarikan</label>
                <div class="relative group">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-muted-light font-black group-focus-within:text-primary transition-colors">Rp</span>
                    <input wire:model="amount" type="number" step="1000" class="w-full pl-14 pr-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-lg font-black text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary transition-all placeholder-muted-light/30" placeholder="0">
                </div>
                @error('amount') 
                    <div class="flex items-center gap-1 ml-2 text-rose-500">
                        <span class="material-symbols-outlined text-sm">error</span>
                        <span class="text-xs font-bold">{{ $message }}</span> 
                    </div>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-muted-light uppercase tracking-[0.2em] ml-2">Nama Bank</label>
                <div class="relative group">
                     <span class="absolute left-6 top-1/2 -translate-y-1/2 text-muted-light group-focus-within:text-primary transition-colors material-symbols-outlined">account_balance</span>
                    <input wire:model="bankName" type="text" class="w-full pl-14 pr-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary placeholder:text-muted-light/30 transition-all" placeholder="Contoh: BCA, Mandiri, BRI...">
                </div>
                @error('bankName') 
                    <div class="flex items-center gap-1 ml-2 text-rose-500">
                        <span class="material-symbols-outlined text-sm">error</span>
                        <span class="text-xs font-bold">{{ $message }}</span> 
                    </div>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-muted-light uppercase tracking-[0.2em] ml-2">Nomor Rekening</label>
                    <div class="relative group">
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-muted-light group-focus-within:text-primary transition-colors material-symbols-outlined">numbers</span>
                         <input wire:model="bankAccountNumber" type="text" class="w-full pl-14 pr-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary placeholder:text-muted-light/30 transition-all" placeholder="XXXXXXX">
                    </div>
                    @error('bankAccountNumber') 
                        <div class="flex items-center gap-1 ml-2 text-rose-500">
                            <span class="material-symbols-outlined text-sm">error</span>
                            <span class="text-xs font-bold">{{ $message }}</span> 
                        </div>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-muted-light uppercase tracking-[0.2em] ml-2">Atas Nama</label>
                    <div class="relative group">
                         <span class="absolute left-6 top-1/2 -translate-y-1/2 text-muted-light group-focus-within:text-primary transition-colors material-symbols-outlined">badge</span>
                        <input wire:model="bankAccountName" type="text" class="w-full pl-14 pr-6 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary placeholder:text-muted-light/30 transition-all" placeholder="Nama sesuai rekening">
                    </div>
                    @error('bankAccountName') 
                        <div class="flex items-center gap-1 ml-2 text-rose-500">
                            <span class="material-symbols-outlined text-sm">error</span>
                            <span class="text-xs font-bold">{{ $message }}</span> 
                        </div>
                    @enderror
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-primary text-white px-8 py-5 rounded-2xl font-black text-sm tracking-[0.2em] hover:bg-primary-dark transition-all transform active:scale-[0.98] shadow-lg hover:shadow-primary/50 uppercase flex items-center justify-center gap-3">
                     <span class="material-symbols-outlined text-lg">downloading</span>
                    Ajukan Penarikan
                </button>
                <p class="text-center text-[12px] text-muted-light font-600 mt-4 flex items-center justify-center gap-1">
                     <span class="material-symbols-outlined text-sm">info</span>
                    Dana akan diproses dalam 1-2 hari kerja
                </p>
            </div>
        </form>
    </div>

    <!-- Confirmation Modal -->
    @if($showConfirmationModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
        <div class="fixed inset-0" wire:click="$set('showConfirmationModal', false)"></div>
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] w-full max-w-sm overflow-hidden shadow-2xl relative z-10 animate-scale-up border border-gray-100 dark:border-gray-700">
            <div class="p-8 text-center space-y-6">
                <div class="w-20 h-20 bg-amber-50 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce-slow">
                    <span class="material-symbols-outlined text-4xl text-amber-500">warning_amber</span>
                </div>
                
                <div>
                    <h3 class="text-xl font-black text-text-light dark:text-text-dark uppercase italic tracking-tight mb-2">Konfirmasi Penarikan</h3>
                    <p class="text-sm text-muted-light font-medium leading-relaxed">
                        Anda akan melakukan penarikan sebesar <span class="text-text-light dark:text-text-dark font-black">Rp {{ number_format($amount, 0, ',', '.') }}</span> ke rekening:
                    </p>
                    <div class="mt-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-xl border border-gray-100 dark:border-gray-800 text-left">
                        <p class="text-[10px] font-bold text-muted-light uppercase tracking-widest mb-1">Detail Rekening</p>
                        <p class="text-sm font-bold text-text-light dark:text-text-dark">{{ $bankName }}</p>
                        <p class="text-lg font-black text-text-light dark:text-text-dark font-mono tracking-tight">{{ $bankAccountNumber }}</p>
                        <p class="text-xs font-medium text-text-light dark:text-text-dark uppercase mt-1">{{ $bankAccountName }}</p>
                    </div>
                </div>

                <div class="bg-rose-50 dark:bg-rose-900/20 p-4 rounded-xl flex gap-3 text-left border border-rose-100 dark:border-rose-800/30">
                     <span class="material-symbols-outlined text-rose-500 shrink-0">priority_high</span>
                     <p class="text-xs text-rose-600 dark:text-rose-400 font-bold leading-relaxed">
                        Pastikan nomor rekening yang Anda masukkan sudah benar. Kesalahan input data dapat menyebabkan kegagalan transfer atau dana hilang.
                     </p>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <button wire:click="$set('showConfirmationModal', false)" class="px-6 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-text-light dark:text-text-dark font-black text-xs uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Batal
                    </button>
                    <button wire:click="processWithdraw" class="px-6 py-3 rounded-xl bg-primary text-white font-black text-xs uppercase tracking-widest hover:bg-primary-dark shadow-lg hover:shadow-primary/30 transition-all transform active:scale-95">
                        Ya, Ajukan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
