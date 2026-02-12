<div>
    @if($booking->status === \App\Enums\BookingStatus::HOLD)
    <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] p-8 shadow-card border border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic tracking-tight mb-6">
            Kode <span class="text-primary">Voucher</span>
        </h3>

        @if($booking->voucher_id)
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-800 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">confirmation_number</span>
                        </span>
                        <div>
                            <p class="text-xs font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-widest">{{ $booking->voucher_code }}</p>
                            <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                                Hemat Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="removeVoucher" wire:loading.attr="disabled"
                            class="text-rose-500 hover:text-rose-700 transition-colors p-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-900/20"
                            title="Hapus voucher">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            </div>
        @else
            <div class="flex gap-2">
                <input type="text" 
                       wire:model="voucherCode"
                       wire:keydown.enter="applyVoucher"
                       placeholder="Masukkan kode voucher"
                       class="flex-1 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-bold text-text-light dark:text-text-dark placeholder:text-gray-400 focus:border-primary focus:ring-1 focus:ring-primary/20 outline-none transition-all uppercase tracking-wider">
                <button wire:click="applyVoucher" wire:loading.attr="disabled"
                        class="px-5 py-3 bg-primary hover:bg-primary/90 text-white rounded-xl font-black text-[10px] uppercase tracking-widest transition-all active:scale-95 shrink-0 flex items-center gap-2">
                    <span wire:loading.remove wire:target="applyVoucher">Pakai</span>
                    <span wire:loading wire:target="applyVoucher" class="material-symbols-outlined text-sm animate-spin">progress_activity</span>
                </button>
            </div>
        @endif

        @if($errorMessage)
            <div class="mt-4 flex items-start gap-2 p-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-xl">
                <span class="material-symbols-outlined text-rose-500 text-base mt-0.5 shrink-0">error</span>
                <p class="text-xs font-bold text-rose-600 dark:text-rose-400">{{ $errorMessage }}</p>
            </div>
        @endif

        @if($successMessage)
            <div class="mt-4 flex items-start gap-2 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-xl">
                <span class="material-symbols-outlined text-emerald-500 text-base mt-0.5 shrink-0">check_circle</span>
                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ $successMessage }}</p>
            </div>
        @endif
    </div>
    @endif
</div>
