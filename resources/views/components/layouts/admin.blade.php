<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Admin Panel' }}</title>
    
    <!-- Google Fonts: Poppins & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        /* Hide scrollbar for Chrome, Safari and Opera */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans text-gray-900">
    <x-toast />

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
        <!-- Sidebar Desktop -->
        <aside class="hidden md:flex md:flex-col w-72 bg-gray-900 text-white shrink-0 shadow-2xl z-50">
            <div class="px-8 py-8 flex flex-col h-full">
                <!-- Logo -->
                <div class="mb-12">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/50">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <span class="font-display text-2xl font-black tracking-tight italic">Admin<span class="text-indigo-500 font-normal">Panel</span></span>
                    </a>
                </div>

                <!-- Navigation -->
                <div class="mb-8">
                    <livewire:admin.system.global-search-admin />
                </div>

                <nav class="flex-1 min-h-0 space-y-2 overflow-y-auto pr-2 scrollbar-hide">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-4 ml-2">Main Menu</p>
                    
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold shadow-lg transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.venues.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.venues.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        <span>Kelola Venue</span>
                    </a>

                    <a href="{{ route('admin.bookings.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.bookings.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span>Booking Masuk</span>
                    </a>

                    <a href="{{ route('admin.vouchers.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.vouchers.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                        <span>Voucher</span>
                    </a>

                    <a href="{{ route('admin.reports.financial') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.reports.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-amber-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Keuangan</span>
                    </a>

                    <a href="{{ route('admin.refunds.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.refunds.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-rose-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                        <span>Refund</span>
                    </a>

                    <a href="{{ route('admin.withdraws.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.withdraws.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-yellow-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" /></svg>
                        <span>Penarikan Saldo</span>
                    </a>

                    <a href="{{ route('admin.settlements.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.settlements.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-emerald-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span>Settlement</span>
                    </a>

                    <a href="{{ route('admin.reviews.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.reviews.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-yellow-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                        <span>Reviews</span>
                    </a>

                    <a href="{{ route('admin.system.users') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.system.users') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <span>Member</span>
                    </a>
                    <a href="{{ route('admin.system.audit-logs') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.system.audit-logs') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        <span>Audit Logs</span>
                    </a>
                    
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-4 mt-12 ml-2">&nbsp;</p>

                </nav>

                <!-- User Profile Bottom -->
                <div class="mt-auto pt-8 border-t border-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center font-black text-indigo-500 border border-gray-700">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-sm text-white truncate tracking-tight">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Administrator</p>
                        </div>
                        <livewire:auth.logout />
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header Mobile -->
            <header class="bg-white border-b border-gray-100 flex items-center justify-between px-6 h-16 shrink-0 md:hidden">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                     <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                     </div>
                     <span class="font-display font-black tracking-tighter">Admin Panel</span>
                </a>
                <button @click="sidebarOpen = true" class="p-2 text-gray-500">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                </button>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-6 md:p-10">
                {{ $slot }}
            </main>
        </div>

        <!-- Sidebar Mobile Overlay -->
        <div x-show="sidebarOpen" x-cloak class="absolute inset-0 z-[60] flex md:hidden">
            <div @click="sidebarOpen = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
            <div class="relative w-72 bg-gray-900 flex flex-col h-full shadow-2xl">
                 <!-- Copy identical content from Desktop Sidebar or just small version -->
                 <div class="px-8 py-8 flex flex-col h-full">
                    <div class="mb-10 flex items-center justify-between">
                         <span class="font-display text-xl font-black text-white italic">Admin<span class="text-indigo-500 font-normal">Panel</span></span>
                         <button @click="sidebarOpen = false" class="text-gray-400">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                         </button>
                    </div>
                    <nav class="flex-1 min-h-0 space-y-2 overflow-y-auto pr-2 scrollbar-hide">
                         <!-- Navigation Items (repeating same as desktop for simplicity or use a separate component) -->
                         <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-400' }} rounded-2xl font-bold shadow-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.venues.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.venues.*') ? 'bg-gray-800 text-white' : 'text-gray-400' }} rounded-2xl font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            <span>Kelola Venue</span>
                        </a>
                        <a href="{{ route('admin.bookings.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.bookings.*') ? 'bg-gray-800 text-white' : 'text-gray-400' }} rounded-2xl font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <span>Booking Masuk</span>
                        </a>
                        <a href="{{ route('admin.vouchers.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.vouchers.*') ? 'bg-gray-800 text-white' : 'text-gray-400' }} rounded-2xl font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                            <span>Voucher</span>
                        </a>
                        <a href="{{ route('admin.reports.financial') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white' : 'text-gray-400' }} rounded-2xl font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>Keuangan</span>
                        </a>
                        <a href="{{ route('admin.refunds.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.refunds.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                        <svg class="w-5 h-5 group-hover:text-rose-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                        <span>Refund</span>
                        </a>
                        <a href="{{ route('admin.withdraws.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.withdraws.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                            <svg class="w-5 h-5 group-hover:text-yellow-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z" /></svg>
                            <span>Penarikan Saldo</span>
                        </a>
                        <a href="{{ route('admin.settlements.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.settlements.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                            <svg class="w-5 h-5 group-hover:text-emerald-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <span>Settlement</span>
                        </a>
                        <a href="{{ route('admin.reviews.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.reviews.*') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                            <svg class="w-5 h-5 group-hover:text-yellow-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                            <span>Reviews</span>
                        </a>
                        <a href="{{ route('admin.system.users') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.system.users') ? 'bg-gray-800 text-white border border-gray-700' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-2xl font-bold transition-all group">
                            <svg class="w-5 h-5 group-hover:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <span>Member</span>
                        </a>
                        <a href="{{ route('admin.system.audit-logs') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.system.audit-logs') ? 'bg-indigo-600 text-white' : 'text-gray-400' }} rounded-2xl font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            <span>Audit Logs</span>
                        </a>
                        <livewire:auth.logout />
                    </nav>
                 </div>
            </div>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => {
             // Handle flashes on navigation
        });
    </script>
</body>
</html>
