<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-7xl pointer-events-none z-0">
        <div class="absolute bottom-20 left-10 opacity-5 animate-pulse">
            <span class="material-symbols-outlined text-[10rem] text-primary">fitness_center</span>
        </div>
        <div class="absolute top-20 right-10 opacity-5 animate-pulse delay-700">
            <span class="material-symbols-outlined text-[12rem] text-primary">trophy</span>
        </div>
    </div>

    <div class="max-w-xl w-full relative z-10">
        <div class="bg-surface-light dark:bg-surface-dark p-8 md:p-10 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
            
            <!-- Header -->
            <div class="text-center mb-10 relative">
                <div class="w-20 h-20 bg-primary/10 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-4xl text-primary">person_add</span>
                </div>
                <h2 class="text-3xl font-black text-text-light dark:text-text-dark tracking-tight italic uppercase">
                    Gabung <span class="text-primary">Squad!</span>
                </h2>
                <p class="mt-2 text-sm text-muted-light font-bold tracking-wide uppercase">
                    Buat akun baru untuk mulai booking lapangan
                </p>
            </div>

            <form class="space-y-6" wire:submit="register">
                <div class="space-y-5">
                    <!-- Name -->
                     <div>
                        <label for="name" class="block text-[10px] font-black text-muted-light uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-light group-focus-within:text-primary transition-colors material-symbols-outlined">badge</span>
                            <input wire:model="name" id="name" name="name" type="text" required 
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary placeholder-muted-light/30 transition-all" 
                                placeholder="Nama Lengkap Anda">
                        </div>
                        @error('name') 
                            <p class="flex items-center gap-1 mt-2 text-xs text-rose-500 font-bold ml-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-[10px] font-black text-muted-light uppercase tracking-widest mb-2 ml-1">Email Address</label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-light group-focus-within:text-primary transition-colors material-symbols-outlined">mail</span>
                            <input wire:model="email" id="email" name="email" type="email" autocomplete="email" required 
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary placeholder-muted-light/30 transition-all" 
                                placeholder="nama@email.com">
                        </div>
                        @error('email') 
                            <p class="flex items-center gap-1 mt-2 text-xs text-rose-500 font-bold ml-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                         <!-- Password -->
                        <div>
                             <label for="password" class="block text-[10px] font-black text-muted-light uppercase tracking-widest mb-2 ml-1">Password</label>
                            
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-light group-focus-within:text-primary transition-colors material-symbols-outlined">lock</span>
                                <input wire:model="password" id="password" name="password" type="password" required 
                                    class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary placeholder-muted-light/30 transition-all" 
                                    placeholder="••••••••">
                            </div>
                            @error('password') 
                                <p class="flex items-center gap-1 mt-2 text-xs text-rose-500 font-bold ml-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span>
                                    {{ $message }}
                                </p> 
                            @enderror
                        </div>

                         <!-- Confirm Password -->
                        <div>
                             <label for="password_confirmation" class="block text-[10px] font-black text-muted-light uppercase tracking-widest mb-2 ml-1">Konfirmasi Password</label>
                            
                            <div class="relative group">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-light group-focus-within:text-primary transition-colors material-symbols-outlined">lock_reset</span>
                                <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" required 
                                    class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-sm font-bold text-text-light dark:text-text-dark focus:ring-2 focus:ring-primary placeholder-muted-light/30 transition-all" 
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" wire:loading.attr="disabled"
                        class="group relative w-full flex justify-center py-4 px-4 bg-primary text-white text-sm font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-primary-dark transition-all transform active:scale-[0.98] shadow-lg shadow-primary/30">
                        <span wire:loading.remove class="flex items-center gap-2 group-hover:gap-3 transition-all">
                            DAFTAR SEKARANG
                            <span class="material-symbols-outlined text-sm">rocket_launch</span>
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <span class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
                            MEMPROSES...
                        </span>
                    </button>
                    <p class="text-center text-[10px] text-muted-light font-bold mt-4 uppercase tracking-widest">
                        Dengan mendaftar, Anda menyetujui Syarat & Ketentuan kami.
                    </p>
                </div>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-800 text-center">
                <p class="text-xs text-muted-light font-bold">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" wire:navigate class="font-black text-primary hover:text-primary-dark transition-colors uppercase tracking-wider ml-1">
                        Masuk Di Sini
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
