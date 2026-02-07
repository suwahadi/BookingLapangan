<div class="min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full max-w-7xl pointer-events-none z-0">
        <div class="absolute top-10 left-10 opacity-5 animate-pulse">
            <span class="material-symbols-outlined text-[10rem] text-primary">sports_soccer</span>
        </div>
        <div class="absolute bottom-10 right-10 opacity-5 animate-pulse delay-700">
            <span class="material-symbols-outlined text-[12rem] text-primary">sports_basketball</span>
        </div>
    </div>

    <div class="max-w-md w-full relative z-10">
        <div class="bg-surface-light dark:bg-surface-dark p-8 md:p-10 rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
            
            <!-- Header -->
            <div class="text-center mb-10 relative">
                <div class="w-20 h-20 bg-primary/10 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-lg shadow-primary/20 group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-4xl text-primary">lock_open</span>
                </div>
                <h2 class="text-3xl font-black text-text-light dark:text-text-dark tracking-tight italic uppercase">
                    Welcome <span class="text-primary">Back!</span>
                </h2>
                <p class="mt-2 text-sm text-muted-light font-bold tracking-wide uppercase">
                    Masuk untuk kelola jadwal mainmu
                </p>
            </div>

            <form class="space-y-6" wire:submit="login">
                <div class="space-y-5">
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

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                             <label for="password" class="block text-[10px] font-black text-muted-light uppercase tracking-widest ml-1">Password</label>
                             <a href="#" class="text-[10px] font-black text-primary hover:text-primary-dark transition-colors uppercase tracking-wider">Lupa Password?</a>
                        </div>
                        
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-light group-focus-within:text-primary transition-colors material-symbols-outlined">lock</span>
                            <input wire:model="password" id="password" name="password" type="password" autocomplete="current-password" required 
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
                </div>

                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer gap-3 group">
                        <div class="relative flex items-center">
                             <input wire:model="remember" id="remember-me" name="remember-me" type="checkbox" class="peer sr-only">
                             <div class="w-5 h-5 border-2 border-gray-300 rounded-lg peer-checked:bg-primary peer-checked:border-primary transition-all"></div>
                             <span class="material-symbols-outlined text-white text-[14px] absolute inset-0 m-auto opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none font-bold">check</span>
                        </div>
                        <span class="text-xs text-muted-light font-bold group-hover:text-primary transition-colors select-none">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="group relative w-full flex justify-center py-4 px-4 bg-primary text-white text-sm font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-primary-dark transition-all transform active:scale-[0.98] shadow-lg shadow-primary/30">
                        <span wire:loading.remove class="flex items-center gap-2 group-hover:gap-3 transition-all">
                            MASUK
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <span class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
                            MEMPROSES...
                        </span>
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-100 dark:border-gray-800 text-center">
                <p class="text-xs text-muted-light font-bold">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" wire:navigate class="font-black text-primary hover:text-primary-dark transition-colors uppercase tracking-wider ml-1">
                        Daftar Sekarang
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
