<div class="space-y-10">
    <!-- Header -->
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.venues.index') }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">Daftar Venue</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('admin.venues.hub', $venue->slug) }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">{{ $venue->name }}</a>
            <span class="text-gray-300">/</span>
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kebijakan</span>
        </div>
        <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Kebijakan <span class="text-indigo-600">Venue</span></h1>
        <p class="text-gray-500 mt-1 tracking-tight">Atur kebijakan DP, reschedule, dan refund untuk {{ $venue->name }}</p>
    </div>

    @if($flash)
        <div class="p-5 rounded-2xl bg-emerald-50 text-emerald-700 text-sm font-bold flex items-center gap-3 border border-emerald-100 shadow-sm">
            <svg class="w-6 h-6 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ $flash }}
        </div>
    @endif
    @if($error)
        <div class="p-5 rounded-2xl bg-rose-50 text-rose-700 text-sm font-bold flex items-center gap-3 border border-rose-100 shadow-sm">
            <svg class="w-6 h-6 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ $error }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- DP Policy -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-50 relative overflow-hidden group hover:shadow-2xl transition-all">
            <div class="absolute -right-8 -top-8 opacity-5 group-hover:opacity-10 transition-opacity duration-500">
                <svg class="w-40 h-40 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <h3 class="font-black text-gray-900 uppercase text-sm tracking-tight italic">Down Payment (DP)</h3>
                    </div>
                </div>

                <div class="space-y-5">
                    <!-- Toggle -->
                    <label class="flex items-center justify-between cursor-pointer p-4 rounded-2xl border-2 transition-all {{ $allow_dp ? 'border-indigo-200 bg-indigo-50/50' : 'border-gray-100 bg-gray-50/50' }}">
                        <div>
                            <span class="text-sm font-bold text-gray-800">Aktifkan DP</span>
                            <p class="text-xs text-gray-400 mt-0.5">Izinkan user bayar sebagian di awal</p>
                        </div>
                        <div class="relative">
                            <input type="checkbox" wire:model.live="allow_dp" class="sr-only peer">
                            <div class="w-12 h-7 bg-gray-200 peer-checked:bg-indigo-600 rounded-full transition-all"></div>
                            <div class="w-5 h-5 bg-white rounded-full absolute top-1 left-1 peer-checked:translate-x-5 transition-transform shadow-md"></div>
                        </div>
                    </label>

                    @if($allow_dp)
                    <div class="space-y-2 animate-fade-in">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Minimal DP (%)</label>
                        <div class="relative">
                            <input type="number" wire:model.live="dp_min_percent" min="1" max="100" 
                                class="w-full px-4 py-3.5 border-2 border-gray-100 rounded-2xl text-sm font-bold focus:border-indigo-500 focus:ring-indigo-500 pr-12" placeholder="50">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">%</span>
                        </div>
                        <p class="text-[10px] text-gray-400 font-medium">User harus bayar minimal {{ $dp_min_percent }}% dari total harga</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Reschedule Policy -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-50 relative overflow-hidden group hover:shadow-2xl transition-all">
            <div class="absolute -right-8 -top-8 opacity-5 group-hover:opacity-10 transition-opacity duration-500">
                <svg class="w-40 h-40 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <h3 class="font-black text-gray-900 uppercase text-sm tracking-tight italic">Reschedule</h3>
                    </div>
                </div>

                <div class="space-y-5">
                    <label class="flex items-center justify-between cursor-pointer p-4 rounded-2xl border-2 transition-all {{ $reschedule_allowed ? 'border-amber-200 bg-amber-50/50' : 'border-gray-100 bg-gray-50/50' }}">
                        <div>
                            <span class="text-sm font-bold text-gray-800">Aktifkan Reschedule</span>
                            <p class="text-xs text-gray-400 mt-0.5">Izinkan user mengubah jadwal</p>
                        </div>
                        <div class="relative">
                            <input type="checkbox" wire:model.live="reschedule_allowed" class="sr-only peer">
                            <div class="w-12 h-7 bg-gray-200 peer-checked:bg-amber-500 rounded-full transition-all"></div>
                            <div class="w-5 h-5 bg-white rounded-full absolute top-1 left-1 peer-checked:translate-x-5 transition-transform shadow-md"></div>
                        </div>
                    </label>

                    @if($reschedule_allowed)
                    <div class="space-y-2 animate-fade-in">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Deadline Reschedule (Jam)</label>
                        <div class="relative">
                            <input type="number" wire:model.live="reschedule_deadline_hours" min="1" max="168"
                                class="w-full px-4 py-3.5 border-2 border-gray-100 rounded-2xl text-sm font-bold focus:border-amber-500 focus:ring-amber-500 pr-16" placeholder="24">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">jam</span>
                        </div>
                        <p class="text-[10px] text-gray-400 font-medium">Reschedule harus diajukan minimal H-{{ $reschedule_deadline_hours }} jam sebelum jadwal main</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Refund Policy -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-50 relative overflow-hidden group hover:shadow-2xl transition-all">
            <div class="absolute -right-8 -top-8 opacity-5 group-hover:opacity-10 transition-opacity duration-500">
                <svg class="w-40 h-40 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
            </div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                        </div>
                        <h3 class="font-black text-gray-900 uppercase text-sm tracking-tight italic">Refund</h3>
                    </div>
                </div>

                <div class="space-y-5">
                    <label class="flex items-center justify-between cursor-pointer p-4 rounded-2xl border-2 transition-all {{ $refund_allowed ? 'border-rose-200 bg-rose-50/50' : 'border-gray-100 bg-gray-50/50' }}">
                        <div>
                            <span class="text-sm font-bold text-gray-800">Aktifkan Refund</span>
                            <p class="text-xs text-gray-400 mt-0.5">Izinkan user ajukan pengembalian dana</p>
                        </div>
                        <div class="relative">
                            <input type="checkbox" wire:model.live="refund_allowed" class="sr-only peer">
                            <div class="w-12 h-7 bg-gray-200 peer-checked:bg-rose-500 rounded-full transition-all"></div>
                            <div class="w-5 h-5 bg-white rounded-full absolute top-1 left-1 peer-checked:translate-x-5 transition-transform shadow-md"></div>
                        </div>
                    </label>

                    @if($refund_allowed)
                    <div class="space-y-4 animate-fade-in">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Aturan Refund</label>
                        
                        <div class="space-y-3">
                            <div class="flex items-center gap-4 p-3 bg-emerald-50/50 rounded-xl border border-emerald-100">
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-700">H-72 jam atau lebih</p>
                                    <p class="text-[10px] text-gray-400">Pembatalan > 3 hari sebelumnya</p>
                                </div>
                                <div class="relative w-20">
                                    <input type="number" wire:model.live="refund_h72" min="0" max="100"
                                        class="w-full px-2 py-2 border border-gray-200 rounded-lg text-sm font-bold text-center focus:border-emerald-400 focus:ring-emerald-400">
                                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-bold">%</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 p-3 bg-amber-50/50 rounded-xl border border-amber-100">
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-700">H-24 jam</p>
                                    <p class="text-[10px] text-gray-400">Pembatalan 1-3 hari sebelumnya</p>
                                </div>
                                <div class="relative w-20">
                                    <input type="number" wire:model.live="refund_h24" min="0" max="100"
                                        class="w-full px-2 py-2 border border-gray-200 rounded-lg text-sm font-bold text-center focus:border-amber-400 focus:ring-amber-400">
                                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-bold">%</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 p-3 bg-rose-50/50 rounded-xl border border-rose-100">
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-700">Kurang dari H-24</p>
                                    <p class="text-[10px] text-gray-400">Pembatalan mendadak</p>
                                </div>
                                <div class="relative w-20">
                                    <input type="number" wire:model.live="refund_below24" min="0" max="100"
                                        class="w-full px-2 py-2 border border-gray-200 rounded-lg text-sm font-bold text-center focus:border-rose-400 focus:ring-rose-400">
                                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] font-bold">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end">
        <button type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                class="bg-gray-900 hover:bg-indigo-600 text-white font-black px-10 py-4 rounded-2xl text-sm tracking-widest uppercase transition-all transform active:scale-95 shadow-2xl shadow-gray-300 flex items-center gap-3 group">
            <svg wire:loading.class="animate-spin" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <span wire:loading.remove wire:target="save">Simpan Kebijakan</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
    </div>

    <!-- Policy Preview -->
    <div class="bg-gray-900 rounded-[2.5rem] p-8 shadow-2xl text-white relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-5">
            <svg class="w-60 h-60" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
        </div>

        <div class="relative z-10">
            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-indigo-400 mb-6 flex items-center gap-2">
                <span class="w-2 h-2 bg-indigo-400 rounded-full"></span>
                Preview Kebijakan Publik
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-800/50 rounded-2xl p-5 border border-gray-700/50">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-3 h-3 rounded-full {{ $allow_dp ? 'bg-emerald-400' : 'bg-gray-600' }}"></div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">DP</span>
                    </div>
                    @if($allow_dp)
                        <p class="text-sm font-bold">Tersedia minimal {{ $dp_min_percent }}%</p>
                        <p class="text-[10px] text-gray-500 mt-1">User bisa bayar DP lalu lunasi sisanya nanti</p>
                    @else
                        <p class="text-sm font-bold text-gray-500">Tidak tersedia</p>
                        <p class="text-[10px] text-gray-500 mt-1">User harus bayar lunas di awal</p>
                    @endif
                </div>

                <div class="bg-gray-800/50 rounded-2xl p-5 border border-gray-700/50">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-3 h-3 rounded-full {{ $reschedule_allowed ? 'bg-emerald-400' : 'bg-gray-600' }}"></div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Reschedule</span>
                    </div>
                    @if($reschedule_allowed)
                        <p class="text-sm font-bold">Bisa diubah H-{{ $reschedule_deadline_hours }} jam</p>
                        <p class="text-[10px] text-gray-500 mt-1">Pengajuan paling lambat {{ $reschedule_deadline_hours }} jam sebelum main</p>
                    @else
                        <p class="text-sm font-bold text-gray-500">Tidak tersedia</p>
                        <p class="text-[10px] text-gray-500 mt-1">Jadwal tidak bisa diubah setelah booking</p>
                    @endif
                </div>

                <div class="bg-gray-800/50 rounded-2xl p-5 border border-gray-700/50">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-3 h-3 rounded-full {{ $refund_allowed ? 'bg-emerald-400' : 'bg-gray-600' }}"></div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Refund</span>
                    </div>
                    @if($refund_allowed)
                        <p class="text-sm font-bold">Tersedia</p>
                        <p class="text-[10px] text-gray-500 mt-1">
                            &gt; 72j: {{ $refund_h72 }}% · &gt; 24j: {{ $refund_h24 }}% · &lt; 24j: {{ $refund_below24 }}%
                        </p>
                    @else
                        <p class="text-sm font-bold text-gray-500">Tidak tersedia</p>
                        <p class="text-[10px] text-gray-500 mt-1">Pembayaran tidak bisa dikembalikan</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
