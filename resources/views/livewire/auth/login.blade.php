<div class="min-h-[calc(100vh-64px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-2xl border border-gray-100">
        <div>
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-black text-gray-900 tracking-tight italic">
                Sapa <span class="text-indigo-600">Lapangan!</span>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-500 font-medium tracking-tight">
                Masuk ke akun Anda untuk mulai booking
            </p>
        </div>

        <form class="mt-8 space-y-6" wire:submit="login">
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 ml-1">Email Address</label>
                    <input wire:model="email" id="email" name="email" type="email" autocomplete="email" required 
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all bg-gray-50/50" 
                        placeholder="nama@email.com">
                    @error('email') <span class="text-xs text-rose-500 font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 ml-1">Password</label>
                    <input wire:model="password" id="password" name="password" type="password" autocomplete="current-password" required 
                        class="appearance-none relative block w-full px-4 py-3 border border-gray-200 placeholder-gray-400 text-gray-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm transition-all bg-gray-50/50" 
                        placeholder="••••••••">
                    @error('password') <span class="text-xs text-rose-500 font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input wire:model="remember" id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded-lg">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-700 font-medium tracking-tight">
                        Ingat saya
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-bold text-indigo-500 hover:text-indigo-600 tracking-tight transition-colors">
                        Lupa password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" wire:loading.attr="disabled"
                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-black rounded-2xl text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform active:scale-[0.98] shadow-xl shadow-indigo-100">
                    <span wire:loading.remove>MASUK SEKARANG &rarr;</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        MEMPROSES...
                    </span>
                </button>
            </div>
        </form>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 font-medium tracking-tight">
                Belum punya akun? 
                <a href="{{ route('register') }}" wire:navigate class="font-black text-indigo-600 hover:text-indigo-700 transition-colors">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </div>
</div>
