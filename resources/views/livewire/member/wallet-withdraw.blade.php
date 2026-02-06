<div class="max-w-xl mx-auto py-12 px-4">
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('member.wallet') }}" class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
        </a>
        <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight italic">Tarik <span class="text-indigo-600">Saldo</span></h1>
    </div>

    <div class="bg-white rounded-[2.5rem] p-8 shadow-2xl border border-gray-50">
        <div class="mb-8 p-6 bg-indigo-50 rounded-2xl border border-indigo-100 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400 mb-1">Saldo Tersedia</p>
                <p class="text-2xl font-black text-indigo-900">Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
        </div>

        <form wire:submit="submit" class="space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Nominal Penarikan</label>
                <div class="relative">
                    <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                    <input wire:model="amount" type="number" step="1000" class="w-full pl-14 pr-6 py-4 bg-gray-50 border-none rounded-2xl text-lg font-black focus:ring-2 focus:ring-indigo-600" placeholder="0">
                </div>
                @error('amount') <span class="text-xs text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Nama Bank</label>
                <input wire:model="bankName" type="text" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 placeholder:text-gray-300" placeholder="Contoh: BCA, Mandiri, BRI...">
                @error('bankName') <span class="text-xs text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Nomor Rekening</label>
                    <input wire:model="bankAccountNumber" type="text" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 placeholder:text-gray-300" placeholder="XXXXXXX">
                    @error('bankAccountNumber') <span class="text-xs text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Atas Nama</label>
                    <input wire:model="bankAccountName" type="text" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 placeholder:text-gray-300" placeholder="Nama sesuai rekening">
                    @error('bankAccountName') <span class="text-xs text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white px-8 py-5 rounded-2xl font-black text-sm tracking-[0.2em] hover:bg-indigo-700 transition-all transform active:scale-95 shadow-xl shadow-indigo-200 uppercase">
                    Ajukan Penarikan
                </button>
                <p class="text-center text-[10px] text-gray-400 font-bold mt-4 uppercase tracking-widest">Dana akan diproses oleh admin dalam 1-2 hari kerja.</p>
            </div>
        </form>
    </div>
</div>
