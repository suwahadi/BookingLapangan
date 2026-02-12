<div class="space-y-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Kelola <span class="text-indigo-600">Voucher</span></h1>
            <p class="text-gray-500 mt-1 tracking-tight">Buat dan kelola voucher diskon untuk pelanggan</p>
        </div>

        <div class="flex items-center gap-4 bg-white p-2 rounded-[1.5rem] shadow-xl border border-gray-50">
            <div class="px-6 py-2">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Total Voucher</p>
                <p class="text-xl font-black text-indigo-600 font-display italic">{{ $totalVouchers }}</p>
            </div>
            <div class="w-px h-10 bg-gray-100"></div>
            <div class="px-6 py-2">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Aktif</p>
                <p class="text-xl font-black text-emerald-600 font-display italic">{{ $activeVouchers }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-[2.5rem] shadow-2xl border border-gray-50 flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 group">
            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>
            <input wire:model.live.debounce.300ms="q" type="text" placeholder="Cari kode atau nama voucher..."
                class="w-full pl-16 pr-6 py-5 bg-gray-50 border-none rounded-[1.5rem] text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600 transition-all">
        </div>

        <div class="w-full md:w-52">
            <select wire:model.live="statusFilter"
                class="w-full px-6 py-5 bg-gray-50 border-none rounded-[1.5rem] text-sm font-black text-gray-900 focus:ring-2 focus:ring-indigo-600 transition-all appearance-none cursor-pointer">
                <option value="">SEMUA STATUS</option>
                <option value="active">AKTIF</option>
                <option value="inactive">NONAKTIF</option>
            </select>
        </div>

        <button wire:click="openCreate" class="w-full md:w-auto px-8 py-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[1.5rem] text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-500/30 whitespace-nowrap">
            + Tambah Voucher
        </button>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kode</th>
                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Nama</th>
                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Tipe Diskon</th>
                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Nilai</th>
                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Min. Order</th>
                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Kuota</th>
                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Berlaku</th>
                        <th class="px-6 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vouchers as $voucher)
                    <tr class="hover:bg-gray-50/30 transition-colors group">
                        <td class="px-10 py-8">
                            <span class="text-sm font-black text-indigo-600 tracking-tighter">{{ $voucher->code }}</span>
                            @if($voucher->scope !== 'global')
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                    {{ $voucher->scope === 'venue' ? 'Venue' : 'Lapangan' }}
                                    @if($voucher->venue) &middot; {{ $voucher->venue->name }} @endif
                                </p>
                            @endif
                        </td>
                        <td class="px-6 py-8">
                            <span class="text-sm font-black text-gray-900">{{ $voucher->name }}</span>
                        </td>
                        <td class="px-6 py-8">
                            <span class="text-[10px] font-black uppercase tracking-widest {{ $voucher->discount_type === \App\Enums\VoucherDiscountType::PERCENTAGE ? 'text-purple-600' : 'text-blue-600' }}">
                                {{ $voucher->discount_type->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-8">
                            <span class="text-sm font-black text-gray-900 tracking-tighter">
                                @if($voucher->discount_type === \App\Enums\VoucherDiscountType::FIXED)
                                    Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                                @else
                                    {{ $voucher->discount_value }}%
                                @endif
                            </span>
                            @if($voucher->discount_type === \App\Enums\VoucherDiscountType::PERCENTAGE && $voucher->max_discount_amount)
                                <p class="text-[10px] font-bold text-gray-400 mt-1">Maks. Rp {{ number_format($voucher->max_discount_amount, 0, ',', '.') }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-8">
                            <span class="text-sm font-black text-gray-900 tracking-tighter">
                                @if($voucher->min_order_amount)
                                    Rp {{ number_format($voucher->min_order_amount, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-8">
                            <span class="text-sm font-black text-gray-900 tracking-tighter">{{ $voucher->usage_count_total }} / {{ $voucher->max_usage_total }}</span>
                            <p class="text-[10px] font-bold text-gray-400 mt-1">Maks. {{ $voucher->max_usage_per_user }}/user</p>
                        </td>
                        <td class="px-6 py-8">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-gray-900 tracking-tight">{{ $voucher->valid_from->format('d M Y') }}</span>
                                <span class="text-[10px] font-bold text-gray-400">s.d. {{ $voucher->valid_until->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-8 text-center">
                            @if($voucher->is_active)
                                <span class="inline-flex px-4 py-2 bg-emerald-100 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm">Aktif</span>
                            @else
                                <span class="inline-flex px-4 py-2 bg-gray-100 text-gray-500 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="toggleActive({{ $voucher->id }})" class="text-[10px] font-black uppercase tracking-widest {{ $voucher->is_active ? 'text-amber-500 hover:text-amber-700' : 'text-emerald-500 hover:text-emerald-700' }} transition-colors">
                                    {{ $voucher->is_active ? 'OFF' : 'ON' }}
                                </button>
                                <button wire:click="openEdit({{ $voucher->id }})" class="text-[10px] font-black text-indigo-500 uppercase tracking-widest hover:text-gray-900 transition-colors">
                                    EDIT
                                </button>
                                <button wire:click="confirmDelete({{ $voucher->id }})" class="text-[10px] font-black text-rose-500 uppercase tracking-widest hover:text-rose-700 transition-colors">
                                    HAPUS
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-10 py-32 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-200">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                                </div>
                                <p class="text-gray-400 font-bold italic">Belum ada data voucher yang sesuai filter.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vouchers->hasPages())
        <div class="px-10 py-8 border-t border-gray-50 bg-gray-50/20">
            {{ $vouchers->links() }}
        </div>
        @endif
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-[70] flex items-center justify-center p-4" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:remove="document.body.classList.remove('overflow-hidden')">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="px-10 py-8 border-b border-gray-100">
                <h2 class="text-2xl font-black text-gray-900 font-display italic uppercase">{{ $editingId ? 'Edit' : 'Tambah' }} <span class="text-indigo-600">Voucher</span></h2>
            </div>

            <form wire:submit="save" class="px-10 py-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kode Voucher</label>
                        <input wire:model="code" type="text" maxlength="20" placeholder="DISKON2024" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600 uppercase">
                        @error('code') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nama Voucher</label>
                        <input wire:model="name" type="text" maxlength="100" placeholder="Diskon Akhir Tahun" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600">
                        @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Deskripsi (Opsional)</label>
                    <textarea wire:model="description" rows="2" maxlength="500" placeholder="Keterangan voucher..." class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600 resize-none"></textarea>
                    @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tipe Diskon</label>
                        <select wire:model.live="discount_type" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-600 appearance-none cursor-pointer">
                            <option value="FIXED">Potongan Tetap (Rp)</option>
                            <option value="PERCENTAGE">Persentase (%)</option>
                        </select>
                        @error('discount_type') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nilai Diskon</label>
                        <input wire:model="discount_value" type="number" min="1" step="1" placeholder="{{ $discount_type === 'PERCENTAGE' ? 'Contoh: 10' : 'Contoh: 50000' }}" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600">
                        @error('discount_value') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Maks. Potongan (Rp)</label>
                        <input wire:model="max_discount_amount" type="number" min="0" step="1" placeholder="Opsional" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600 {{ $discount_type !== 'PERCENTAGE' ? 'opacity-50' : '' }}" {{ $discount_type !== 'PERCENTAGE' ? 'disabled' : '' }}>
                        @error('max_discount_amount') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Min. Order (Rp)</label>
                        <input wire:model="min_order_amount" type="number" min="0" step="1" placeholder="Opsional" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600">
                        @error('min_order_amount') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Cakupan</label>
                        <select wire:model.live="scope" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-600 appearance-none cursor-pointer">
                            <option value="global">Global (Semua Venue)</option>
                            <option value="venue">Venue Tertentu</option>
                            <option value="court">Lapangan Tertentu</option>
                        </select>
                        @error('scope') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if(in_array($scope, ['venue', 'court']))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Venue</label>
                        <select wire:model.live="venue_id" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-600 appearance-none cursor-pointer">
                            <option value="">-- Pilih Venue --</option>
                            @foreach($this->venues as $venue)
                                <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                            @endforeach
                        </select>
                        @error('venue_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @if($scope === 'court')
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pilih Lapangan</label>
                        <select wire:model="venue_court_id" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-black focus:ring-2 focus:ring-indigo-600 appearance-none cursor-pointer">
                            <option value="">-- Pilih Lapangan --</option>
                            @foreach($this->courts as $court)
                                <option value="{{ $court->id }}">{{ $court->name }}</option>
                            @endforeach
                        </select>
                        @error('venue_court_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endif
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kuota Total</label>
                        <input wire:model="max_usage_total" type="number" min="1" step="1" placeholder="Contoh: 100" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600">
                        @error('max_usage_total') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kuota Per User</label>
                        <input wire:model="max_usage_per_user" type="number" min="1" step="1" placeholder="Contoh: 1" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600">
                        @error('max_usage_per_user') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Berlaku Dari</label>
                        <input wire:model="valid_from" type="datetime-local" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600">
                        @error('valid_from') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Berlaku Sampai</label>
                        <input wire:model="valid_until" type="datetime-local" class="w-full px-5 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-indigo-600">
                        @error('valid_until') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                    <button type="button" wire:click="$set('showModal', false)" class="px-8 py-4 text-sm font-black text-gray-500 uppercase tracking-widest hover:text-gray-900 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-500/30">
                        {{ $editingId ? 'Simpan Perubahan' : 'Buat Voucher' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($showDeleteModal)
    <div class="fixed inset-0 z-[70] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="$set('showDeleteModal', false)"></div>
        <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-10 text-center">
            <div class="w-16 h-16 bg-rose-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </div>
            <h3 class="text-xl font-black text-gray-900 font-display italic uppercase mb-2">Hapus Voucher?</h3>
            <p class="text-gray-500 text-sm mb-8">Voucher yang dihapus tidak dapat dikembalikan. Apakah Anda yakin?</p>
            <div class="flex items-center justify-center gap-4">
                <button wire:click="$set('showDeleteModal', false)" class="px-8 py-4 text-sm font-black text-gray-500 uppercase tracking-widest hover:text-gray-900 transition-colors">
                    Batal
                </button>
                <button wire:click="delete" class="px-10 py-4 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-rose-500/30">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
