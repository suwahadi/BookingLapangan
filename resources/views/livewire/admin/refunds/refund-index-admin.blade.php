<div class="space-y-10">
    <!-- Header -->
    <div class="flex items-end justify-between">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight italic uppercase">Kelola <span class="text-indigo-600">Refund</span></h1>
            <p class="text-gray-500 font-bold mt-1 uppercase text-[10px] tracking-[0.2em]">Permintaan pengembalian dana</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
        <select wire:model.live="status" class="px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
            <option value="">Semua Status</option>
            <option value="PENDING">Pending</option>
            <option value="APPROVED">Approved</option>
            <option value="REJECTED">Rejected</option>
            <option value="EXECUTED">Executed</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Booking</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">User</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Jumlah</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                        <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($refunds as $refund)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-gray-900 uppercase">{{ $refund->booking->booking_code ?? '-' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $refund->booking->venue->name ?? '-' }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-bold text-gray-900">{{ $refund->booking->user->name ?? '-' }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-gray-900">Rp {{ number_format($refund->refund_amount, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest 
                                    {{ $refund->status->value === 'PENDING' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $refund->status->value === 'APPROVED' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $refund->status->value === 'REJECTED' ? 'bg-rose-100 text-rose-700' : '' }}
                                    {{ $refund->status->value === 'EXECUTED' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                    {{ $refund->status->value }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $refund->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                @if($refund->status->value === 'PENDING')
                                    <button wire:click="approve({{ $refund->id }})" wire:confirm="Approve refund ini?"
                                        class="text-[10px] font-black uppercase tracking-widest text-emerald-600 hover:underline mr-3">
                                        Approve
                                    </button>
                                    <button wire:click="reject({{ $refund->id }})" wire:confirm="Tolak refund ini?"
                                        class="text-[10px] font-black uppercase tracking-widest text-rose-600 hover:underline">
                                        Reject
                                    </button>
                                @elseif($refund->status->value === 'APPROVED')
                                    <button wire:click="execute({{ $refund->id }})" wire:confirm="Eksekusi refund ke wallet user?"
                                        class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:underline">
                                        Execute
                                    </button>
                                @else
                                    <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-16 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-500 font-bold">Belum ada permintaan refund</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($refunds->hasPages())
            <div class="p-8 border-t border-gray-50">
                {{ $refunds->links() }}
            </div>
        @endif
    </div>
</div>
