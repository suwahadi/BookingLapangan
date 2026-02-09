<div class="space-y-10">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.venues.hub', $venue) }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-indigo-600 transition-colors flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Kembali ke Hub
            </a>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight italic uppercase">Fasilitas <span class="text-indigo-600">{{ $venue->name }}</span></h1>
            <p class="text-gray-500 mt-1 uppercase text-[10px] tracking-[0.2em]">Pilih fasilitas yang tersedia di venue ini</p>
        </div>
        <button wire:click="save" class="px-6 py-3 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Simpan
        </button>
    </div>

    <!-- Amenities by Category -->
    <div class="space-y-8">
        @php
            $categoryLabels = [
                'parking' => 'Parkir',
                'facility' => 'Fasilitas Umum',
                'comfort' => 'Kenyamanan',
                'food' => 'Makanan & Minuman',
                'equipment' => 'Peralatan',
                'security' => 'Keamanan',
                'accessibility' => 'Aksesibilitas',
            ];
        @endphp

        @foreach($amenities as $category => $items)
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">{{ $categoryLabels[$category] ?? ucfirst($category) }}</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($items as $amenity)
                            <button wire:click="toggle({{ $amenity->id }})" type="button"
                                class="p-4 rounded-2xl border-2 transition-all text-left {{ in_array($amenity->id, $selected) ? 'border-indigo-600 bg-indigo-50' : 'border-gray-100 hover:border-gray-200 bg-gray-50' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl {{ in_array($amenity->id, $selected) ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center transition-colors">
                                        @switch($amenity->icon)
                                            @case('car')
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                                @break
                                            @case('wifi')
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" /></svg>
                                                @break
                                            @case('shield')
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                                @break
                                            @case('users')
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                                @break
                                            @case('coffee')
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                @break
                                            @case('camera')
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                @break
                                            @default
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        @endswitch
                                    </div>
                                    <span class="text-sm font-bold {{ in_array($amenity->id, $selected) ? 'text-indigo-600' : 'text-gray-700' }}">{{ $amenity->name }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Selected Summary -->
    <div class="bg-gray-900 rounded-3xl p-8">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Fasilitas Terpilih</p>
                <p class="text-3xl font-black text-white mt-1">{{ count($selected) }} <span class="text-lg text-gray-400">fasilitas</span></p>
            </div>
            <button wire:click="save" class="px-8 py-4 bg-indigo-600 text-white text-sm font-black uppercase tracking-widest rounded-2xl hover:bg-indigo-700 transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </div>
</div>
