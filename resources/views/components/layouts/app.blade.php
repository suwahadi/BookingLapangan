<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Yomabar' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    @livewireStyles
</head>
<body class="bg-[#F8FAFC] min-h-screen font-sans">
    <x-toast />

    <!-- Navigation Header -->
    <nav class="bg-white sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Left: Logo + Menu -->
                <div class="flex items-center gap-12">
                    <!-- Logo -->
                    <a href="/" wire:navigate class="text-[#8B1538] font-black text-2xl tracking-tight italic">
                        YOMABAR
                    </a>

                    <!-- Desktop Menu -->
                    <div class="hidden lg:flex items-center gap-8">
                        <a href="/" wire:navigate class="text-sm font-semibold text-gray-700 hover:text-[#8B1538] transition-colors">Sewa Lapangan</a>
                        <a href="#" class="text-sm font-semibold text-gray-700 hover:text-[#8B1538] transition-colors">Main Bareng</a>
                        <a href="#" class="text-sm font-semibold text-gray-700 hover:text-[#8B1538] transition-colors">Sparring</a>
                        <a href="#" class="text-sm font-semibold text-gray-700 hover:text-[#8B1538] transition-colors">Partner</a>
                        <a href="#" class="text-sm font-semibold text-gray-700 hover:text-[#8B1538] transition-colors">Blog</a>
                    </div>
                </div>

                <!-- Right: Auth -->
                <div class="flex items-center gap-4">
                    @auth
                        <!-- Authenticated User Menu -->
                        <a href="{{ route('member.bookings') }}" wire:navigate class="hidden md:block text-sm font-semibold text-gray-700 hover:text-[#8B1538] transition-colors">
                            Booking Saya
                        </a>
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <div class="text-sm font-bold text-gray-900 leading-tight">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-gray-400 font-medium">Member</div>
                            </div>
                            <livewire:auth.logout />
                        </div>
                    @else
                        <!-- Guest -->
                        <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold text-gray-700 hover:text-[#8B1538] transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" wire:navigate class="px-5 py-2 bg-[#8B1538] text-white rounded-lg text-sm font-bold hover:bg-[#6B1028] transition-colors">
                            Daftar
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button type="button" class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-100">
            <div class="px-4 py-4 space-y-2">
                <a href="/" wire:navigate class="block px-4 py-3 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Sewa Lapangan</a>
                <a href="#" class="block px-4 py-3 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Main Bareng</a>
                <a href="#" class="block px-4 py-3 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Sparring</a>
                <a href="#" class="block px-4 py-3 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Partner</a>
                <a href="#" class="block px-4 py-3 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Blog</a>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>
    
    @livewireScripts
    <script>
        document.addEventListener('livewire:navigated', () => {
            @if(session('success'))
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: "{{ session('success') }}", type: 'success' } }));
            @endif
            @if(session('error'))
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: "{{ session('error') }}", type: 'error' } }));
            @endif
        });
        
        // Initial load
        window.addEventListener('DOMContentLoaded', () => {
             @if(session('success'))
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: "{{ session('success') }}", type: 'success' } }));
            @endif
            @if(session('error'))
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: "{{ session('error') }}", type: 'error' } }));
            @endif
        });
    </script>
</body>
</html>
