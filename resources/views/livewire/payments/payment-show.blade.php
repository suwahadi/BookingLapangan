@php
    $resp = $payment->payload_response ?? [];
    $vaNumbers = $resp['va_numbers'] ?? [];
    $permataVa = $resp['permata_va_number'] ?? null;
    $actions = $resp['actions'] ?? [];
@endphp

<div class="max-w-3xl mx-auto px-4 py-12">
    <div class="bg-surface-light dark:bg-surface-dark rounded-[2.5rem] shadow-card overflow-hidden border border-gray-100 dark:border-gray-700">
        {{-- Header --}}
        <div class="bg-primary/5 dark:bg-surface-dark px-8 py-10 relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-10 group-hover:scale-110 transition-transform duration-700">
                <span class="material-symbols-outlined text-[10rem] text-primary">receipt_long</span>
            </div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <span class="px-4 py-1.5 bg-white dark:bg-gray-800 rounded-full text-[10px] font-black tracking-widest uppercase text-muted-light shadow-sm">
                        Instruksi Pembayaran
                    </span>
                    <span class="text-[10px] text-muted-light font-mono bg-white dark:bg-gray-800 px-3 py-1 rounded-full border border-gray-100 dark:border-gray-700">
                        ID: {{ $payment->provider_order_id }}
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-text-light dark:text-text-dark tracking-tight uppercase italic mb-2">Penyelesaian <span class="text-primary">Pesanan</span></h1>
                <p class="text-muted-light text-sm font-medium">Silakan selesaikan pembayaran sesuai rincian di bawah ini.</p>
            </div>
        </div>

        <div class="p-8 space-y-10">
            {{-- Order Summary --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 p-6 bg-gray-50 dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <div>
                    <div class="text-[10px] font-black text-muted-light uppercase tracking-widest mb-1">Total Tagihan</div>
                    <div class="text-3xl md:text-4xl font-black text-primary font-display italic">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </div>
                </div>
                <div class="md:text-right flex flex-col md:items-end">
                    <div class="text-[10px] font-black text-muted-light uppercase tracking-widest mb-2">Metode Pembayaran</div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl shadow-sm">
                        <span class="font-black text-text-light dark:text-text-dark text-xs uppercase tracking-wider">{{ strtoupper($payment->payment_method) }}</span>
                    </div>
                </div>
            </div>

            {{-- Virtual Account Section --}}
            @if(!empty($vaNumbers) || $permataVa)
                <div>
                    <h2 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic tracking-tight mb-6 flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                             <span class="material-symbols-outlined text-lg">account_balance</span>
                        </div>
                        Virtual Account
                    </h2>

                    <div class="space-y-4">
                        @foreach($vaNumbers as $va)
                            <div class="relative p-8 border-2 border-primary/20 rounded-[2rem] bg-primary/5 overflow-hidden group hover:border-primary hover:bg-white dark:hover:bg-gray-800 transition-all duration-300">
                                <div class="absolute -top-6 -right-6 opacity-5 rotate-12 group-hover:opacity-10 transition-opacity">
                                    <span class="material-symbols-outlined text-[8rem] text-primary">account_balance_wallet</span>
                                </div>
                                <div class="relative">
                                    <div class="text-[10px] font-black text-primary uppercase tracking-widest mb-2">
                                        Nomor {{ strtoupper($va['bank'] ?? 'VIRTUAL ACCOUNT') }}
                                    </div>
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="text-2xl md:text-3xl font-mono font-black text-text-light dark:text-text-dark tracking-tighter select-all" id="va-number-{{ $loop->index }}">
                                            {{ $va['va_number'] ?? '-' }}
                                        </div>
                                        <button class="px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-xl transition-all shadow-sm border border-gray-200 dark:border-gray-600 text-primary font-bold text-xs uppercase tracking-wider flex items-center gap-2" 
                                                onclick="navigator.clipboard.writeText('{{ $va['va_number'] }}'); this.innerHTML = '<span class=\'material-symbols-outlined text-sm\'>check</span> DISALIN!'; setTimeout(() => { this.innerHTML = '<span class=\'material-symbols-outlined text-sm\'>content_copy</span> SALIN'; }, 2000);">
                                            <span class="material-symbols-outlined text-sm">content_copy</span>
                                            SALIN
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($permataVa)
                            <div class="relative p-8 border-2 border-primary/20 rounded-[2rem] bg-primary/5 overflow-hidden group hover:border-primary hover:bg-white dark:hover:bg-gray-800 transition-all duration-300">
                                <div class="relative">
                                    <div class="text-[10px] font-black text-primary uppercase tracking-widest mb-2">Nomor PERMATA</div>
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="text-2xl md:text-3xl font-mono font-black text-text-light dark:text-text-dark tracking-tighter select-all">
                                            {{ $permataVa }}
                                        </div>
                                        <button class="px-4 py-2 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-xl transition-all shadow-sm border border-gray-200 dark:border-gray-600 text-primary font-bold text-xs uppercase tracking-wider flex items-center gap-2"
                                                onclick="navigator.clipboard.writeText('{{ $permataVa }}'); this.innerHTML = '<span class=\'material-symbols-outlined text-sm\'>check</span> DISALIN!'; setTimeout(() => { this.innerHTML = '<span class=\'material-symbols-outlined text-sm\'>content_copy</span> SALIN'; }, 2000);">
                                            <span class="material-symbols-outlined text-sm">content_copy</span>
                                            SALIN
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Actions / E-Wallet Section --}}
            @if(!empty($actions))
                <div>
                    <h2 class="text-lg font-black text-text-light dark:text-text-dark uppercase italic tracking-tight mb-6 flex items-center gap-3">
                        <div class="w-8 h-8 bg-sky-100 dark:bg-sky-900 rounded-lg flex items-center justify-center text-sky-600 dark:text-sky-400">
                            <span class="material-symbols-outlined text-lg">qr_code_scanner</span>
                        </div>
                        Konfirmasi E-Wallet
                    </h2>

                    <div class="grid gap-4">
                        @foreach($actions as $action)
                            @if($action['name'] !== 'generate-qr-code') 
                                <a href="{{ $action['url'] }}" target="_blank" 
                                   class="flex items-center justify-between p-6 border-2 border-gray-100 dark:border-gray-700 rounded-2xl hover:border-primary hover:bg-primary/5 transition-all group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                            <span class="material-symbols-outlined">smartphone</span>
                                        </div>
                                        <div>
                                            <div class="font-black text-text-light dark:text-text-dark uppercase tracking-tight">{{ strtoupper($action['name']) }}</div>
                                            <div class="text-xs font-medium text-muted-light mt-0.5">Klik untuk membuka aplikasi</div>
                                        </div>
                                    </div>
                                    <span class="material-symbols-outlined text-gray-300 group-hover:text-primary transition-colors">arrow_forward_ios</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Fallback --}}
            @if(empty($vaNumbers) && empty($permataVa) && empty($actions))
                <div class="p-8 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-[2rem] bg-gray-50 dark:bg-gray-800/50">
                    <span class="material-symbols-outlined text-4xl text-gray-300 mb-3">hourglass_empty</span>
                    <div class="text-muted-light mb-4 font-bold text-sm">Data instruksi pembayaran belum tersedia.</div>
                    <button onclick="window.location.reload()" class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-700 text-primary font-black text-xs uppercase tracking-widest rounded-xl hover:shadow-lg transition-all border border-gray-100 dark:border-gray-600">
                        <span class="material-symbols-outlined text-sm">refresh</span>
                        Refresh Halaman
                    </button>
                </div>
            @endif

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 grid md:grid-cols-2 gap-6">
                <div class="flex items-start gap-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-900/40">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-800 flex items-center justify-center text-emerald-600 dark:text-emerald-300 shrink-0">
                        <span class="material-symbols-outlined text-sm font-bold">check</span>
                    </div>
                    <p class="text-xs font-medium text-emerald-800 dark:text-emerald-200 leading-relaxed">
                        Pembayaran akan diverifikasi secara <span class="font-black">otomatis</span> oleh sistem dalam 1-2 menit setelah Anda transfer.
                    </p>
                </div>
                <div class="flex items-start gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-900/40">
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-300 shrink-0">
                        <span class="material-symbols-outlined text-sm font-bold">verified</span>
                    </div>
                    <p class="text-xs font-medium text-blue-800 dark:text-blue-200 leading-relaxed">
                        Setelah terkonfirmasi, status pesanan akan menjadi <span class="font-black">CONFIRMED</span>. Cek status di menu <a href="#" class="underline hover:text-blue-500">Booking Saya</a>.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800/80 px-8 py-6 flex justify-between items-center border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('bookings.show', ['booking' => $payment->booking_id]) }}" class="text-xs font-bold text-muted-light hover:text-primary transition-colors flex items-center gap-2 uppercase tracking-wider">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Kembali ke Booking
            </a>
            <div class="flex gap-2">
                <div class="text-[10px] text-muted-light font-mono bg-white dark:bg-gray-700 px-2 py-1 rounded border border-gray-200 dark:border-gray-600">
                    STATUS: {{ $payment->status->value }}
                </div>
            </div>
        </div>
    </div>
</div>
