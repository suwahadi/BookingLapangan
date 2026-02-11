<div class="space-y-10 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.venues.index') }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">Daftar Venue</a>
                <span class="text-gray-300">/</span>
                @if($isEdit)
                    <a href="{{ route('admin.venues.hub', $venue->slug) }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">{{ $venue->name }}</a>
                    <span class="text-gray-300">/</span>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Sunting</span>
                @else
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Tambah Baru</span>
                @endif
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">
                {{ $isEdit ? 'Edit' : 'Tambah' }} <span class="text-indigo-600">Venue</span>
            </h1>
            <p class="text-gray-500 mt-1 tracking-tight">
                {{ $isEdit ? 'Perbarui informasi venue' : 'Daftarkan tempat olahraga baru ke sistem' }}
            </p>
        </div>
        <a href="{{ route('admin.venues.index') }}" wire:navigate class="text-sm font-black text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-[0.2em] flex items-center gap-2">
            &larr; KEMBALI
        </a>
    </div>

    <form wire:submit="save" class="space-y-8">
        <!-- Section 1: Basic Info -->
        <div class="bg-white p-10 rounded-[3rem] shadow-2xl border border-gray-50 space-y-8">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight italic">Informasi <span class="text-indigo-600">Dasar</span></h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Nama Venue</label>
                    <input wire:model.blur="name" type="text" placeholder="Contoh: Gelora Futsal Perkasa" 
                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all">
                    @error('name') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Slug (URL)</label>
                    <input wire:model="slug" type="text" placeholder="gelora-futsal-perkasa" 
                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all opacity-70">
                    @error('slug') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Tipe Olahraga Utama</label>
                <select wire:model="sport_type" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 appearance-none">
                    <option value="">Pilih Tipe</option>
                    <option value="Futsal">Futsal</option>
                    <option value="Badminton">Badminton</option>
                    <option value="Basket">Basket</option>
                    <option value="Mini Soccer">Mini Soccer</option>
                    <option value="Tennis">Tennis</option>
                    <option value="Voli">Voli</option>
                    <option value="Padel">Padel</option>
                </select>
                @error('sport_type') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Deskripsi Venue</label>
                <textarea wire:model="description" rows="7" placeholder="Ceritakan tentang keunggulan venue Anda..." 
                    class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-600 transition-all"></textarea>
                @error('description') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Section 2: Location -->
        <div class="bg-white p-10 rounded-[3rem] shadow-2xl border border-gray-50 space-y-8">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <h3 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight italic">Lokasi & <span class="text-indigo-600">Alamat</span></h3>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Alamat Lengkap</label>
                <input wire:model="address" type="text" placeholder="Jl. Raya Olahraga No. 123..." 
                    class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all">
                @error('address') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Kota</label>
                    <input wire:model="city" type="text" placeholder="Contoh: Jakarta Selatan" 
                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all">
                    @error('city') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Provinsi</label>
                    <input wire:model="province" type="text" placeholder="Contoh: DKI Jakarta" 
                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all">
                    @error('province') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Kode Pos</label>
                    <input wire:model="postal_code" type="text" placeholder="12345" 
                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all">
                    @error('postal_code') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Section 3: Contact & Status -->
        <div class="bg-white p-10 rounded-[3rem] shadow-2xl border border-gray-50 space-y-8">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight italic">Kontak & <span class="text-indigo-600">Status</span></h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">WhatsApp / Phone</label>
                    <input wire:model="phone" type="text" placeholder="081234567890" 
                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all">
                    @error('phone') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Email Bisnis</label>
                    <input wire:model="email" type="email" placeholder="admin@venue.com" 
                        class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl text-sm font-bold focus:ring-2 focus:ring-indigo-600 transition-all">
                    @error('email') <span class="text-[10px] text-rose-500 font-bold ml-2">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="pt-6 flex items-center gap-4">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                    <div class="w-14 h-8 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600"></div>
                    <span class="ml-3 text-sm font-black text-gray-900 uppercase tracking-widest">Venue Aktif & Publik</span>
                </label>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-6 pt-4">
            <a href="{{ route('admin.venues.index') }}" wire:navigate class="text-sm font-black text-gray-500 hover:text-gray-900 transition-colors uppercase tracking-widest">
                BATALKAN
            </a>
            <button type="submit" wire:loading.attr="disabled"
                class="bg-gray-900 text-white px-10 py-5 rounded-[2rem] font-black text-sm tracking-[0.2em] hover:bg-black transition-all transform active:scale-95 shadow-2xl shadow-indigo-100 flex items-center gap-3">
                <span wire:loading.remove>{{ $isEdit ? 'PERBARUI VENUE' : 'TERBITKAN VENUE' }}</span>
                <span wire:loading class="flex items-center gap-2 italic uppercase">
                    <svg class="animate-spin h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    PROSES...
                </span>
            </button>
        </div>
    </form>
</div>
