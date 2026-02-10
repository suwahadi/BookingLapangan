<div class="max-w-5xl mx-auto py-12 px-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <div class="mb-4">
                <a href="{{ route('member.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 text-gray-400 font-bold text-[10px] uppercase tracking-[0.2em] hover:text-[#8B1538] transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Kembali ke Dashboard
                </a>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Profil <span class="text-[#8B1538]">Member</span></h1>
            <p class="text-gray-500 font-bold mt-1 uppercase text-xs tracking-widest">Kelola informasi akun dan keamanan Anda</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Profile Form -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 relative group overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                <span class="material-symbols-outlined text-[10rem]">manage_accounts</span>
            </div>

            <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tight mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#8B1538]">person</span>
                Informasi <span class="text-[#8B1538]">Pribadi</span>
            </h3>

            <form wire:submit="updateProfile" class="space-y-5 relative z-10">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" id="name" wire:model="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#8B1538] focus:ring-[#8B1538] transition-colors">
                    @error('name') <span class="text-rose-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" wire:model="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#8B1538] focus:ring-[#8B1538] transition-colors">
                    @error('email') <span class="text-rose-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">Nomor Telepon</label>
                    <input type="text" id="phone" wire:model="phone" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#8B1538] focus:ring-[#8B1538] transition-colors">
                    @error('phone') <span class="text-rose-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    @if (session()->has('success'))
                        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                            <span class="font-medium">Sukses!</span> {{ session('success') }}
                        </div>
                    @endif
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-3 bg-[#8B1538] text-white font-bold rounded-xl hover:bg-[#6d1029] transition-all shadow-lg shadow-[#8B1538]/20 active:scale-[0.98]">
                        <span wire:loading.remove wire:target="updateProfile">Simpan Perubahan</span>
                        <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                            <span class="material-symbols-outlined animate-spin text-sm">sync</span>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Form -->
        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 relative group overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-5 pointer-events-none">
                <span class="material-symbols-outlined text-[10rem]">lock_reset</span>
            </div>

            <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tight mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#8B1538]">lock</span>
                Ganti <span class="text-[#8B1538]">Password</span>
            </h3>

            <form wire:submit="updatePassword" class="space-y-5 relative z-10">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-bold text-gray-700 mb-2">Password Saat Ini</label>
                    <input type="password" id="current_password" wire:model="current_password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#8B1538] focus:ring-[#8B1538] transition-colors">
                    @error('current_password') <span class="text-rose-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Password Baru</label>
                    <input type="password" id="password" wire:model="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#8B1538] focus:ring-[#8B1538] transition-colors">
                    @error('password') <span class="text-rose-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password Baru</label>
                    <input type="password" id="password_confirmation" wire:model="password_confirmation" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#8B1538] focus:ring-[#8B1538] transition-colors">
                </div>

                <div class="pt-4">
                    @if (session()->has('status'))
                        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                            <span class="font-medium">Sukses!</span> {{ session('status') }}
                        </div>
                    @endif
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gray-800 text-white font-bold rounded-xl hover:bg-gray-900 transition-all shadow-lg active:scale-[0.98]">
                        <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                        <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                            <span class="material-symbols-outlined animate-spin text-sm">sync</span>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
