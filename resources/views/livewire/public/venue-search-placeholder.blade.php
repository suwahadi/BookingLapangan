@php
    $sportCategories = [
        ['key' => '', 'name' => 'Semua', 'icon' => 'emoji_events', 'color' => 'gray'],
        ['key' => 'futsal', 'name' => 'Futsal', 'icon' => 'sports_soccer', 'color' => 'red'],
        ['key' => 'badminton', 'name' => 'Badminton', 'icon' => 'sports_tennis', 'color' => 'blue'],
        ['key' => 'basket', 'name' => 'Basket', 'icon' => 'sports_basketball', 'color' => 'orange'],
        ['key' => 'mini soccer', 'name' => 'Mini Soccer', 'icon' => 'sports_soccer', 'color' => 'green'],
        ['key' => 'tennis', 'name' => 'Tenis', 'icon' => 'sports_tennis', 'color' => 'lime'],
        ['key' => 'padel', 'name' => 'Padel', 'icon' => 'sports_tennis', 'color' => 'emerald'],
        ['key' => 'voli', 'name' => 'Voli', 'icon' => 'sports_volleyball', 'color' => 'yellow'],
    ];
@endphp

<div class="min-h-screen animate-pulse">
    <!-- Hero Section Placeholder -->
    <div class="relative min-h-[85vh] flex flex-col bg-gray-200">
        <!-- Background Image Skeleton -->
        <div class="absolute inset-0 z-0 bg-gray-300"></div>

        <!-- Hero Content Skeleton -->
        <div class="relative z-10 flex-1 flex items-center">
            <div class="max-w-7xl mx-auto px-4 lg:px-8 w-full py-16">
                <div class="max-w-2xl space-y-4">
                    <!-- Title -->
                    <div class="h-16 w-3/4 bg-gray-400 rounded-lg"></div>
                    <div class="h-16 w-1/2 bg-gray-400 rounded-lg"></div>
                    
                    <!-- Subtitle -->
                    <div class="h-6 w-full bg-gray-400 rounded mt-6"></div>
                    <div class="h-6 w-2/3 bg-gray-400 rounded"></div>

                    <!-- App Store Buttons -->
                    <div class="flex flex-wrap gap-4 mt-8">
                        <div class="w-40 h-14 bg-gray-400 rounded-xl"></div>
                        <div class="w-40 h-14 bg-gray-400 rounded-xl"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar Skeleton (Bottom) -->
        <div class="relative z-20 -mb-10">
            <div class="max-w-5xl mx-auto px-4">
                <div class="bg-white rounded-2xl p-3 md:p-4 flex flex-col md:flex-row items-stretch gap-3 shadow-2xl h-24">
                   <!-- Fake Search Bar -->
                   <div class="flex-1 bg-gray-100 rounded-xl"></div>
                   <div class="flex-1 bg-gray-100 rounded-xl"></div>
                   <div class="flex-1 bg-gray-100 rounded-xl"></div>
                   <div class="w-24 bg-gray-100 rounded-xl"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Skeleton -->
    <div class="max-w-7xl mx-auto px-4 lg:px-6 pt-20 pb-16">
        <div class="flex flex-col lg:flex-row gap-12">
            
            <!-- Sidebar Skeleton -->
            <aside class="hidden md:block lg:w-64 flex-shrink-0 space-y-4">
                <div class="h-6 w-32 bg-gray-200 rounded mb-4"></div>
                @for($i=0; $i<8; $i++)
                    <div class="h-12 w-full bg-gray-100 rounded-xl"></div>
                @endfor
                <div class="h-48 w-full bg-gray-200 rounded-xl mt-6"></div>
            </aside>

            <!-- Grid Skeleton -->
            <section class="flex-1">
                <!-- Mobile Category Scroll Skeleton -->
                <div class="lg:hidden mb-8 -mx-4 px-4 flex gap-3 overflow-hidden">
                     @for($i=0; $i<4; $i++)
                        <div class="w-24 h-10 bg-gray-200 rounded-full"></div>
                     @endfor
                </div>

                <!-- Header Skeleton -->
                <div class="flex items-center justify-between mb-6">
                    <div class="space-y-2">
                        <div class="h-8 w-48 bg-gray-200 rounded"></div>
                        <div class="h-4 w-32 bg-gray-100 rounded"></div>
                    </div>
                </div>

                <!-- Venue Grid Skeleton -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @for($i=0; $i<6; $i++)
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden h-96 flex flex-col">
                            <div class="h-48 bg-gray-200 w-full animate-pulse"></div>
                            <div class="p-5 flex-1 space-y-3">
                                <div class="h-6 w-3/4 bg-gray-200 rounded"></div>
                                <div class="flex gap-2">
                                    <div class="h-4 w-12 bg-gray-200 rounded"></div>
                                    <div class="h-4 w-24 bg-gray-200 rounded"></div>
                                </div>
                                <div class="mt-auto pt-4 border-t border-gray-50 flex justify-between items-center">
                                    <div class="space-y-1">
                                        <div class="h-3 w-16 bg-gray-200 rounded"></div>
                                        <div class="h-6 w-24 bg-gray-200 rounded"></div>
                                    </div>
                                    <div class="h-8 w-20 bg-gray-200 rounded-xl"></div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </section>

        </div>
    </div>
</div>
