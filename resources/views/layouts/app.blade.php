<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>{{ $title ?? 'Yomabar' }}</title>
    
    <!-- Alpine.js -->
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#A90A2E",
                        "primary-dark": "#8B001F",
                        "background-light": "#F3F4F6",
                        "background-dark": "#111827",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1F2937",
                        "text-light": "#1F2937",
                        "text-dark": "#F3F4F6",
                        "muted-light": "#6B7280",
                        "muted-dark": "#9CA3AF",
                    },
                    fontFamily: {
                        display: ["Poppins", "sans-serif"],
                        sans: ["Poppins", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                        xl: "1rem",
                        "2xl": "1.5rem",
                    },
                    boxShadow: {
                        'card': '0 2px 8px rgba(0, 0, 0, 0.05)',
                    }
                },
            },
        };
    </script>
    <style type="text/tailwindcss">
        .custom-pattern {
            background-color: #A90A2E;
            background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuDqmsPzRzmtF21fkQGx5MwimKSl-cUJ-lC8PTV4LLNz2C8Po99Avw_7uEAABwkQOscd0NxH5_yeSmRzP1bSAGz4S4YY-hdkbRfx_QXCpR7o9VCc-IMVdUMvKlLUsL9rPKdj5dz4koepgix7CrwHzBqsCARhs9YUqlehctQJyPUn62EjxUWfNTR01WJZiGE_tBvroACeSO68g57r3TisTaaABlutPZ03ZPQbq9BxqAiTGuJwg5LDzkrjYWb75-oNFK8TPCwuZ8Ltiq5z);
            background-size: cover;
            background-position: center;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        body {
            min-height: 100dvh;
        }
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
    
    <header class="sticky top-0 z-40 bg-primary shadow-lg lg:py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16 lg:h-20">
            <div class="flex items-center gap-8">
                <a href="/" wire:navigate class="font-black italic text-2xl tracking-tighter text-white">YOMABAR</a>
            </div>
            <div class="flex items-center gap-2 sm:gap-4">
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
