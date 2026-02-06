<div
    x-data="{ 
        messages: [],
        remove(id) {
            this.messages = this.messages.filter(m => m.id !== id)
        },
        add(message, type = 'success') {
            const id = Date.now()
            this.messages.push({ id, message, type })
            setTimeout(() => this.remove(id), 5000)
        }
    }"
    @toast.window="add($event.detail.message, $event.detail.type)"
    class="fixed top-5 right-5 z-50 flex flex-col gap-2 w-full max-w-sm"
>
    <template x-for="message in messages" :key="message.id">
        <div 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-8"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-8"
            :class="{
                'bg-emerald-600': message.type === 'success',
                'bg-rose-600': message.type === 'error',
                'bg-indigo-600': message.type === 'info'
            }"
            class="text-white px-4 py-4 rounded-2xl shadow-2xl flex items-start justify-between border border-white/20 backdrop-blur-sm"
        >
            <div class="flex items-center gap-3">
                <template x-if="message.type === 'success'">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </template>
                <template x-if="message.type === 'error'">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </template>
                <template x-if="message.type === 'info'">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </template>
                <span x-text="message.message" class="text-sm font-bold tracking-tight"></span>
            </div>
            <button @click="remove(message.id)" class="shrink-0 opacity-70 hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap round="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </template>
</div>
