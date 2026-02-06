<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Booking Lapangan' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

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

    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-40 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <a href="/" wire:navigate class="font-bold text-xl text-indigo-600 tracking-tighter flex items-center gap-2">
                             <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-100">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                             </div>
                             <span class="hidden sm:block">BookingLapangan</span>
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    @auth
                        <a href="#" class="text-sm font-bold text-gray-600 hover:text-indigo-600 transition-colors tracking-tight">Booking Saya</a>
                        <div class="h-6 w-px bg-gray-100"></div>
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <div class="text-xs font-black text-gray-900 leading-none mb-0.5 uppercase tracking-wider">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest leading-none">Member</div>
                            </div>
                            <livewire:auth.logout />
                        </div>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-sm font-bold text-gray-600 hover:text-indigo-600 transition-colors tracking-tight">Masuk</a>
                        <a href="{{ route('register') }}" wire:navigate class="px-5 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-black hover:bg-black transition-all transform active:scale-[0.98] shadow-lg shadow-gray-200">Daftar</a>
                    @endauth
                </div>
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
