<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $pageId ? 'Edit Halaman' : 'Buat Halaman Baru' }}</h1>
            <p class="text-sm text-slate-500">{{ $pageId ? 'Perbarui konten halaman statis' : 'Tambahkan halaman statis baru' }}</p>
        </div>
        <a href="{{ route('admin.pages.index') }}" wire:navigate class="text-sm font-semibold text-gray-500 hover:text-gray-900">
            &larr; Kembali ke List
        </a>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Title -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <label class="block text-sm font-bold text-gray-900 mb-2">Judul Halaman <span class="text-red-500">*</span></label>
                <input type="text" wire:model.live="title" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-sm font-semibold transition-colors placeholder-gray-400" placeholder="Contoh: Tentang Kami">
                @error('title') <span class="text-xs text-red-500 font-bold mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Content -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100" wire:ignore>
                <label class="block text-sm font-bold text-gray-900 mb-2">Konten <span class="text-red-500">*</span></label>
                <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
                <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

                <div x-data="{
                    value: @entangle('content_html'),
                    isFocused: false,
                    init() {
                        let trix = this.$refs.trix;
                        
                        // Function to set initial value
                        // Initialize value
                        let setValue = () => {
                            if (this.value && trix.editor) {
                                trix.editor.loadHTML(this.value);
                            }
                        };
                        
                        // Handle initial load
                        if (trix.editor) setValue();
                        else trix.addEventListener('trix-initialize', setValue);

                        // Handle updates from Livewire (if any external change happens)
                        this.$watch('value', (newValue) => {
                             // Only update editor if it's not focused to prevent cursor jumping
                             if(document.activeElement !== trix && !trix.contains(document.activeElement)) {
                                 const currentVal = trix.value;
                                 if(newValue !== currentVal) {
                                     trix.editor.loadHTML(newValue);
                                 }
                             }
                        });

                        // Handle updates from Trix to Livewire
                        trix.addEventListener('trix-change', (e) => {
                            this.value = trix.value; 
                        });
                    }
                }"
                class="min-h-[350px]">
                    <trix-editor x-ref="trix" class="trix-content min-h-[300px] rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"></trix-editor>
                </div>
                
                <style>
                    .trix-button-group--file-tools { display: none !important; }
                    trix-editor { min-height: 300px; border-radius: 0.75rem; border-color: #e5e7eb; padding: 1rem; }
                    trix-editor:focus { border-color: #6366f1; ring: 2px; outline: none; }
                    trix-toolbar .trix-button--icon { width: 1.8rem; height: 1.8rem; }
                </style>
            </div>
            @error('content_html') <span class="text-xs text-red-500 font-bold mt-1 block px-6">{{ $message }}</span> @enderror
        </div>

        <!-- Sidebar / Settings -->
        <div class="space-y-6">
            <!-- Publication -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-4">Publikasi</h3>
                
                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Status</label>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="$set('is_active', true)" class="flex-1 py-2 rounded-lg border {{ $is_active ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }} text-xs font-bold transition-all">
                            Aktif
                        </button>
                        <button type="button" wire:click="$set('is_active', false)" class="flex-1 py-2 rounded-lg border {{ !$is_active ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }} text-xs font-bold transition-all">
                            Draf
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Slug URL</label>
                    <input type="text" wire:model.live="slug" class="w-full px-3 py-2 rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-xs font-mono text-gray-600 bg-gray-50" placeholder="auto-generate">
                    @error('slug') <span class="text-xs text-red-500 font-bold mt-1">{{ $message }}</span> @enderror
                    <p class="text-[10px] text-gray-400 mt-1">URL: {{ url('/') }}/<span class="text-indigo-500 font-semibold">{{ $slug ?: '...' }}</span></p>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-[0.98]">
                        {{ $pageId ? 'Simpan Perubahan' : 'Terbitkan Halaman' }}
                    </button>
                </div>
            </div>

            <!-- SEO -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-4">SEO (Opsional)</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Meta Description</label>
                        <textarea wire:model="meta_description" rows="3" class="w-full px-3 py-2 rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-xs transition-colors"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Meta Keywords</label>
                        <input type="text" wire:model="meta_keywords" class="w-full px-3 py-2 rounded-lg border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 text-xs transition-colors" placeholder="keyword1, keyword2">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
