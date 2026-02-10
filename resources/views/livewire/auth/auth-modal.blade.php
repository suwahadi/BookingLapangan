<div>
    <!-- Modal Backdrop & Container -->
    @if($showModal)
    <div 
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-data
        x-cloak
    >
        <!-- Backdrop -->
        <div 
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"
            wire:click="closeModal"
        ></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden">
            <!-- Close Button -->
            <button 
                type="button"
                wire:click="closeModal"
                class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors z-10"
            >
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>

            <!-- Modal Body -->
            <div class="p-6 sm:p-8">
                <!-- Header -->
                <div class="text-center mb-6">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">
                        {{ $mode === 'login' ? 'Masuk' : 'Daftar' }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-500">
                        @if($mode === 'login')
                            Belum punya akun? 
                            <button type="button" wire:click="switchMode('register')" class="text-primary font-bold hover:underline">Daftar</button>
                        @else
                            Sudah punya akun? 
                            <button type="button" wire:click="switchMode('login')" class="text-primary font-bold hover:underline">Masuk</button>
                        @endif
                    </p>
                </div>

                <!-- Login Form -->
                @if($mode === 'login')
                <form wire:submit.prevent="login" class="space-y-5">
                    <!-- Email Field -->
                    <div>
                        <label for="login-email" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Email
                        </label>
                        <input 
                            wire:model="email" 
                            type="email" 
                            id="login-email"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm font-medium placeholder-gray-400"
                            placeholder="email@example.com"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="login-password" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Password
                        </label>
                        <input 
                            wire:model="password" 
                            type="password" 
                            id="login-password"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm font-medium placeholder-gray-400"
                            placeholder="••••••••"
                        >
                        @error('password')
                            <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            wire:model="remember" 
                            type="checkbox" 
                            id="login-remember"
                            class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary"
                        >
                        <label for="login-remember" class="ml-2 text-xs font-medium text-gray-600 dark:text-gray-400">
                            Ingat saya
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full py-3.5 px-4 bg-primary hover:bg-primary-dark text-white font-black rounded-xl transition-all transform active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-sm uppercase tracking-wider shadow-lg shadow-primary/25"
                        wire:loading.attr="disabled"
                        wire:target="login"
                    >
                        <span wire:loading.remove wire:target="login">Masuk</span>
                        <span wire:loading wire:target="login" class="flex items-center gap-2">
                            <span class="material-symbols-outlined animate-spin text-lg">progress_activity</span>
                            Memproses...
                        </span>
                    </button>
                </form>
                @endif

                <!-- Register Form -->
                @if($mode === 'register')
                <form wire:submit.prevent="register" class="space-y-5">
                    <!-- Name Field -->
                    <div>
                        <label for="register-name" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Nama Lengkap
                        </label>
                        <input 
                            wire:model="name" 
                            type="text" 
                            id="register-name"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm font-medium placeholder-gray-400"
                            placeholder="Nama lengkap Anda"
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="register-email" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Email
                        </label>
                        <input 
                            wire:model="email" 
                            type="email" 
                            id="register-email"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm font-medium placeholder-gray-400"
                            placeholder="email@example.com"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="register-password" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Password
                        </label>
                        <input 
                            wire:model="password" 
                            type="password" 
                            id="register-password"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm font-medium placeholder-gray-400"
                            placeholder="Minimal 8 karakter"
                        >
                        @error('password')
                            <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="register-password-confirm" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Konfirmasi Password
                        </label>
                        <input 
                            wire:model="password_confirmation" 
                            type="password" 
                            id="register-password-confirm"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm font-medium placeholder-gray-400"
                            placeholder="Ulangi password"
                        >
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full py-3.5 px-4 bg-primary hover:bg-primary-dark text-white font-black rounded-xl transition-all transform active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-sm uppercase tracking-wider shadow-lg shadow-primary/25"
                        wire:loading.attr="disabled"
                        wire:target="register"
                    >
                        <span wire:loading.remove wire:target="register">Daftar Sekarang</span>
                        <span wire:loading wire:target="register" class="flex items-center gap-2">
                            <span class="material-symbols-outlined animate-spin text-lg">progress_activity</span>
                            Memproses...
                        </span>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
