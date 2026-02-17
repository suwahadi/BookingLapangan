<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Platform booking lapangan olahraga online termudah dan terlengkap. Cari futsal, badminton, basket, dan lainnya di sekitarmu.">
    <meta name="keywords" content="booking lapangan, sewa lapangan, futsal, badminton, basket, olahraga, venue">
    <meta name="theme-color" content="#A90A2E">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Yomabar - Sport Booking Platform' }}">
    <meta property="og:description" content="Platform booking lapangan olahraga online termudah dan terlengkap.">
    <meta property="og:image" content="{{ asset('og-image.jpg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? 'Yomabar - Sport Booking Platform' }}">
    <meta property="twitter:description" content="Platform booking lapangan olahraga online termudah dan terlengkap.">
    <meta property="twitter:image" content="{{ asset('og-image.jpg') }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <title>{{ $title ?? 'Yomabar' }}</title>
    
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <!-- Alpine.js -->
    <!-- Tailwind CSS (Vite Build) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    <style>
         #filter-drawer:checked ~ #filter-content {
            display: block;
        }
        #filter-drawer:checked ~ #overlay {
            display: block;
        }
    </style>
    
    @livewireStyles
</head>

<body class="bg-background-light dark:bg-background-dark font-sans text-text-light dark:text-text-dark antialiased">
    <x-toast />
    
    <!-- Auth Modal -->
    <livewire:auth.auth-modal />
    
    <header class="sticky top-0 z-30 bg-primary shadow-lg lg:py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16 lg:h-20">
            <div class="flex items-center gap-8">
                <a href="/" wire:navigate class="font-black italic text-2xl tracking-tighter text-white">YOMABAR</a>
            </div>
            <div class="flex items-center gap-2 sm:gap-4">
                
                <!-- Dark Mode Toggle -->
                <button
                    x-data="{
                        darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                        toggle() {
                            this.darkMode = !this.darkMode;
                            if (this.darkMode) {
                                localStorage.theme = 'dark';
                                document.documentElement.classList.add('dark');
                            } else {
                                localStorage.theme = 'light';
                                document.documentElement.classList.remove('dark');
                            }
                        }
                    }"
                    @click="toggle()"
                    class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/50"
                    aria-label="Toggle Dark Mode"
                >
                    <span x-show="!darkMode" class="material-symbols-outlined text-xl text-yellow-500">light_mode</span>
                    <span x-show="darkMode" class="material-symbols-outlined text-xl text-blue-300" style="display: none;">dark_mode</span>
                </button>

                @auth
                    <!-- Notification Bell -->
                    <livewire:member.notification-bell />

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                            class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 text-primary transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/50">
                            <span class="material-symbols-outlined text-2xl">person</span>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.outside="open = false" 
                            x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
                            x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 bg-surface-light dark:bg-surface-dark rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 py-2 z-50 text-text-light dark:text-text-dark">
                            
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 mb-2">
                                <div class="text-sm font-bold">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-muted-light">Member</div>
                            </div>
                            
                            <a href="{{ route('member.dashboard') }}" wire:navigate class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Dashboard</a>
                            <a href="{{ route('member.bookings') }}" wire:navigate class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Booking Saya</a>
                            <a href="{{ route('member.profile') }}" wire:navigate class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">Profil</a>
                            
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" wire:navigate class="block px-4 py-2 text-sm text-primary font-bold hover:bg-gray-50 dark:hover:bg-gray-700">Admin Panel</a>
                            @endif

                             <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>
                            
                            <livewire:auth.logout />
                        </div>
                    </div>
                @else
                    <button 
                        type="button"
                        onclick="Livewire.dispatch('openAuthModal', { mode: 'login' })"
                        class="relative inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 text-primary transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary/50"
                    >
                        <span class="material-symbols-outlined text-2xl">login</span>
                    </button>
                @endauth
            </div>
        </div>
    </header>
    <main>
        {{ $slot }}
    </main>

    <livewire:partials.footer />
    
    @livewireScripts
    <script>
        document.addEventListener('livewire:navigated', () => {
            @if(session('success'))
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: "{{ session('success') }}", type: 'success' } }));
            @endif
            @if(session('error'))
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: "{{ session('error') }}", type: 'error' } }));
            @endif

            // Open auth modal if redirected from /login or /register
            @if(session('openAuth'))
                Livewire.dispatch('openAuthModal', { mode: "{{ session('openAuth') }}" });
            @endif
        });
    </script>

</body>

</html>
