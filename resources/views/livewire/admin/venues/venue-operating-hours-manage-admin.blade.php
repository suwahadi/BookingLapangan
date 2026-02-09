<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.venues.index') }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">Daftar Venue</a>
                <span class="text-gray-300">/</span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ $venue->name }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Jam <span class="text-indigo-600">Operasional</span></h1>
            <p class="text-gray-500 mt-1 tracking-tight">Atur jadwal buka dan tutup venue setiap harinya</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button wire:click="applyToAll" class="bg-white border-2 border-gray-900 px-6 py-3 rounded-2xl font-black text-xs tracking-widest hover:bg-gray-100 transition-all flex items-center gap-2">
                SAMAKAN SEMUA HARI
            </button>

            <button wire:click="save" class="bg-gray-900 text-white px-8 py-4 rounded-[1.5rem] font-black text-sm tracking-widest hover:bg-black transition-all transform active:scale-95 shadow-2xl shadow-indigo-100 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                SIMPAN JADWAL
            </button>
        </div>
    </div>

    <!-- Operating Hours List -->
    <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="p-10 border-b border-gray-50 bg-gray-50/30">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h4 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight italic">Jadwal <span class="text-indigo-600">7 Hari</span></h4>
            </div>
        </div>

        <div class="divide-y divide-gray-50">
            @foreach($days as $day => $label)
            <div class="p-8 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-8 group hover:bg-gray-50/50 transition-colors">
                <div class="w-32">
                    <span class="text-sm font-black text-gray-900 tracking-widest">{{ $label }}</span>
                </div>

                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                    <!-- Status Toggle -->
                    <div class="flex items-center gap-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="hours.{{ $day }}.is_closed" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-500"></div>
                            <span class="ml-3 text-[10px] font-black uppercase tracking-widest {{ $hours[$day]['is_closed'] ? 'text-rose-600' : 'text-gray-400' }}">
                                {{ $hours[$day]['is_closed'] ? 'TUTUP / LIBUR' : 'BUKA' }}
                            </span>
                        </label>
                    </div>

                    <!-- Time Inputs -->
                    <div class="flex items-center gap-4 md:col-span-2 {{ $hours[$day]['is_closed'] ? 'opacity-30 pointer-events-none' : '' }}">
                        <div class="flex-1 space-y-1">
                            <label class="block text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Jam Buka</label>
                            <input type="time" wire:model.defer="hours.{{ $day }}.open_time" 
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-600">
                        </div>
                        <div class="text-gray-300 font-black">/</div>
                        <div class="flex-1 space-y-1">
                            <label class="block text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Jam Tutup</label>
                            <input type="time" wire:model.defer="hours.{{ $day }}.close_time" 
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-indigo-600">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="p-10 bg-gray-900 text-white flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Informasi</p>
                    <p class="text-xs font-bold">Pastikan jam tutup lebih besar dari jam buka. Jam yang diatur di sini akan membatasi slot yang muncul di halaman publik.</p>
                </div>
            </div>
        </div>
    </div>
</div>
