<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Kelola <span class="text-indigo-600">Penarikan</span></h1>
            <p class="text-gray-500 font-bold mt-1 tracking-tight">Proses permintaan withdraw saldo dari user.</p>
        </div>
        
        <div class="flex gap-2">
            @foreach(['', 'PENDING', 'APPROVED', 'PAID', 'REJECTED'] as $status)
                <button wire:click="$set('statusFilter', '{{ $status }}')" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $statusFilter === $status ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-400 hover:text-gray-900 border border-gray-100' }}">
                    {{ $status ?: 'SEMUA' }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @php
            $pendingCount = \App\Models\WithdrawRequest::where('status', 'PENDING')->count();
            $pendingAmount = \App\Models\WithdrawRequest::where('status', 'PENDING')->sum('amount');
            $approvedCount = \App\Models\WithdrawRequest::where('status', 'APPROVED')->count();
        @endphp
        
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Menunggu</p>
                <h3 class="text-3xl font-black italic text-gray-900 font-display">{{ $pendingCount }} <span class="text-sm font-bold opacity-50 not-italic">Permintaan</span></h3>
            </div>
            <div class="w-12 h-12 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-50 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Outstanding</p>
                <h3 class="text-3xl font-black italic text-gray-900 font-display">IDR {{ number_format($pendingAmount, 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
        </div>

        <div class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-2xl shadow-indigo-100 flex items-center justify-between text-white">
            <div>
                <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-1">Siap Dibayar</p>
                <h3 class="text-3xl font-black italic font-display">{{ $approvedCount }} <span class="text-sm font-bold opacity-50 not-italic">Antrean</span></h3>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">User / Waktu</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Rekening Tujuan</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nominal</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($requests as $r)
                    <tr class="hover:bg-gray-50/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-400 uppercase">
                                    {{ substr($r->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900">{{ $r->user->name ?? 'Deleted User' }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                        {{ $r->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-gray-900 italic uppercase font-display">{{ $r->bank_name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $r->bank_account_number }}</p>
                            <p class="text-[10px] text-indigo-600 font-black uppercase tracking-wider mt-1 underline decoration-dotted">{{ $r->bank_account_name }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-lg font-black font-display italic text-gray-900">IDR {{ number_format($r->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-{{ $r->status->color() }}-100 text-{{ $r->status->color() }}-600 inline-block">
                                {{ $r->status->label() }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                @if($r->status === \App\Enums\WithdrawStatus::PENDING)
                                    <button wire:click="approve({{ $r->id }})" wire:confirm="Setujui penarikan ini? Saldo user akan langsung dikurangi." class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-black transition-all">Setujui</button>
                                    <button wire:click="reject({{ $r->id }})" wire:confirm="Tolak penarikan ini?" class="px-4 py-2 bg-white text-rose-600 border border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm hover:bg-rose-50 transition-all">Tolak</button>
                                @endif

                                @if($r->status === \App\Enums\WithdrawStatus::APPROVED)
                                    <button wire:click="markPaid({{ $r->id }})" wire:confirm="Tandai sudah dibayar/transfer?" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-black transition-all italic">Mark Paid</button>
                                @endif
                                
                                @if($r->status === \App\Enums\WithdrawStatus::PAID)
                                    <div class="text-[10px] font-bold text-gray-400 italic">Diproses: {{ $r->processed_at?->format('d/m/y') }}</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <p class="text-gray-400 font-bold italic">Tidak ada permintaan penarikan ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="px-8 py-6 border-t border-gray-50 bg-gray-50/20">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>
