<div class="space-y-10">
    <!-- Header -->
    <div class="flex items-end justify-between">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight italic uppercase">Settlement <span class="text-indigo-600">Venue</span></h1>
            <p class="text-gray-500 mt-1 uppercase text-[10px] tracking-[0.2em]">Pembayaran ke pemilik venue</p>
        </div>
        <button wire:click="openCreateModal" class="px-6 py-3 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Settlement
        </button>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6">
        <select wire:model.live="status" class="px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
            <option value="">Semua Status</option>
            <option value="PENDING">Pending</option>
            <option value="APPROVED">Approved</option>
            <option value="TRANSFERRED">Transferred</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Kode</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Venue</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Periode</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Booking</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Net Amount</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($settlements as $settlement)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-gray-900 uppercase">{{ $settlement->settlement_code }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-bold text-gray-900">{{ $settlement->venue->name ?? '-' }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                                    {{ $settlement->period_start->format('d M') }} - {{ $settlement->period_end->format('d M Y') }}
                                </p>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-gray-900">{{ $settlement->booking_count }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-emerald-600">Rp {{ number_format($settlement->net_amount, 0, ',', '.') }}</p>
                                <p class="text-[8px] text-gray-400">Fee: Rp {{ number_format($settlement->platform_fee, 0, ',', '.') }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest 
                                    {{ $settlement->status === 'PENDING' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $settlement->status === 'APPROVED' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $settlement->status === 'TRANSFERRED' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                    {{ $settlement->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                @if($settlement->status === 'PENDING')
                                    <button wire:click="approve({{ $settlement->id }})" wire:confirm="Approve settlement ini?"
                                        class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:underline">
                                        Approve
                                    </button>
                                @elseif($settlement->status === 'APPROVED')
                                    <button wire:click="markTransferred({{ $settlement->id }})" wire:confirm="Tandai sudah ditransfer?"
                                        class="text-[10px] font-black uppercase tracking-widest text-emerald-600 hover:underline">
                                        Mark Transferred
                                    </button>
                                @else
                                    <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Done</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-16 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-gray-500 font-bold">Belum ada settlement</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($settlements->hasPages())
            <div class="p-8 border-t border-gray-50">
                {{ $settlements->links() }}
            </div>
        @endif
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-8">
                <h3 class="text-xl font-black text-gray-900 uppercase mb-6">Buat Settlement Baru</h3>
                
                <form wire:submit="createSettlement" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Venue</label>
                        <select wire:model="venueId" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                            <option value="">Pilih Venue</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                            @endforeach
                        </select>
                        @error('venueId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Mulai</label>
                            <input type="date" wire:model="periodStart" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                            @error('periodStart') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Sampai</label>
                            <input type="date" wire:model="periodEnd" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                            @error('periodEnd') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" wire:click="$set('showCreateModal', false)" class="flex-1 px-6 py-4 bg-gray-100 text-gray-700 text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-6 py-4 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-colors">
                            Buat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
