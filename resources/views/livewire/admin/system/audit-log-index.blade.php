<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Audit <span class="text-indigo-600">Logs</span></h1>
            <p class="text-gray-500 font-bold mt-1 tracking-tight">Rekam jejak semua aktivitas dan perubahan penting di dalam sistem.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 flex flex-col md:flex-row gap-6 items-center">
        <div class="relative flex-1 group">
            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari aktor, aksi, atau model..." 
                class="w-full pl-16 pr-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
        </div>

        <div class="w-full md:w-64">
            <select wire:model.live="action" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black text-gray-900 focus:ring-2 focus:ring-indigo-600 appearance-none cursor-pointer">
                <option value="">SEMUA AKSI</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}">{{ strtoupper(str_replace('.', ' ', $act)) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Waktu</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Aktor</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Aksi</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Rincian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-gray-900">{{ $log->created_at->translatedFormat('d F Y') }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-500 border border-gray-200 uppercase">
                                    {{ substr($log->actor->name ?? 'SYS', 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-gray-700">{{ $log->actor->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-indigo-100">
                                {{ str_replace('.', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col gap-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</p>
                                @if($log->meta)
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($log->meta as $key => $val)
                                            <span class="text-[9px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded border border-gray-200">
                                                {{ $key }}: {{ is_array($val) ? json_encode($val) : $val }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <p class="text-gray-400 font-bold italic">Belum ada aktifitas tercatat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/20">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
