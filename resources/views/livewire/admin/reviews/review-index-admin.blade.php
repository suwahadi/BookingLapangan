<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Manajemen Review</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6 flex flex-col md:flex-row gap-4 items-center">
        <div class="w-full md:w-1/3">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari venue, user, atau komentar..." class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-primary focus:border-primary">
        </div>
        <div class="w-full md:w-auto">
            <select wire:model.live="filterStatus" class="border border-slate-300 rounded-lg px-4 py-2 focus:ring-primary focus:border-primary w-full">
                <option value="all">Semua Status</option>
                <option value="approved">Disetujui</option>
                <option value="pending">Pending</option>
            </select>
        </div>
        <div class="w-full md:w-auto">
            <select wire:model.live="filterRating" class="border border-slate-300 rounded-lg px-4 py-2 focus:ring-primary focus:border-primary w-full">
                <option value="">Semua</option>
                <option value="5">Bintang 5</option>
                <option value="4">Bintang 4</option>
                <option value="3">Bintang 3</option>
                <option value="2">Bintang 2</option>
                <option value="1">Bintang 1</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 uppercase text-slate-500 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Reviewer</th>
                        <th class="px-6 py-4">Venue</th>
                        <th class="px-6 py-4">Rating</th>
                        <th class="px-6 py-4">Komentar</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900">{{ $review->user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $review->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-medium">{{ $review->venue->name }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $review->court?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center text-yellow-500 bg-yellow-50 w-fit px-2 py-1 rounded-lg">
                                    <span class="font-bold mr-1">{{ $review->rating }}</span>
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate text-slate-600 italic" title="{{ $review->comment }}">
                                "{{ Str::limit($review->comment, 60) }}"
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <button wire:click="toggleApproval({{ $review->id }})" 
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 {{ $review->is_approved ? 'bg-emerald-500' : 'bg-gray-200' }}" 
                                        role="switch" aria-checked="{{ $review->is_approved }}">
                                        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $review->is_approved ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                    <span class="ml-3 text-sm font-medium {{ $review->is_approved ? 'text-emerald-600' : 'text-gray-500' }}">
                                        {{ $review->is_approved ? 'HIDUP' : 'MATI' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:confirm="Yakin hapus review ini?" wire:click="delete({{ $review->id }})" class="text-xs font-bold text-gray-400 hover:text-red-600 px-2 py-1.5 transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                    <p>Tidak ada data review ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $reviews->links() }}
        </div>
    </div>
</div>
