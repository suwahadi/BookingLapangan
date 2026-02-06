@php
    $resp = $payment->payload_response ?? [];
    $vaNumbers = $resp['va_numbers'] ?? [];
    $permataVa = $resp['permata_va_number'] ?? null;
    $actions = $resp['actions'] ?? [];
@endphp

<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        {{-- Header --}}
        <div class="bg-gray-900 px-6 py-8 text-white">
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-semibold tracking-wider uppercase">
                    Instruksi Pembayaran
                </span>
                <span class="text-xs text-gray-400 font-mono">
                    ID: {{ $payment->provider_order_id }}
                </span>
            </div>
            <h1 class="text-3xl font-bold mb-2">Penyelesaian Pesanan</h1>
            <p class="text-gray-400 text-sm">Silakan selesaikan pembayaran sesuai rincian di bawah ini.</p>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            {{-- Order Summary --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <div>
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Total Bayar</div>
                    <div class="text-3xl font-black text-gray-900">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Metode</div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <span class="font-bold text-gray-800">{{ strtoupper($payment->payment_method) }}</span>
                    </div>
                </div>
            </div>

            {{-- Virtual Account Section --}}
            @if(!empty($vaNumbers) || $permataVa)
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Virtual Account
                    </h2>

                    <div class="space-y-4">
                        @foreach($vaNumbers as $va)
                            <div class="relative p-6 border-2 border-indigo-100 rounded-2xl bg-indigo-50/30 overflow-hidden group">
                                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                    <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M4 4h16v16H4V4zm2 2v12h12V6H6zm2 2h8v8H8V8z"/></svg>
                                </div>
                                <div class="relative">
                                    <div class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">
                                        Nomor {{ strtoupper($va['bank'] ?? 'VIRTUAL ACCOUNT') }}
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-2xl md:text-3xl font-mono font-black text-gray-900 tracking-tighter" id="va-number-{{ $loop->index }}">
                                            {{ $va['va_number'] ?? '-' }}
                                        </div>
                                        <button class="p-2 hover:bg-white rounded-lg transition-colors text-indigo-600" 
                                                onclick="navigator.clipboard.writeText('{{ $va['va_number'] }}'); alert('Nomor VA berhasil disalin');">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($permataVa)
                            <div class="relative p-6 border-2 border-indigo-100 rounded-2xl bg-indigo-50/30 overflow-hidden group">
                                <div class="relative">
                                    <div class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Nomor PERMATA</div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-2xl md:text-3xl font-mono font-black text-gray-900 tracking-tighter">
                                            {{ $permataVa }}
                                        </div>
                                        <button class="p-2 hover:bg-white rounded-lg transition-colors text-indigo-600"
                                                onclick="navigator.clipboard.writeText('{{ $permataVa }}'); alert('Nomor VA berhasil disalin');">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
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
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Konfirmasi E-Wallet
                    </h2>

                    <div class="grid gap-3">
                        @foreach($actions as $action)
                            @if($action['name'] !== 'generate-qr-code') {{-- QR code biasanya sulit di-render manual dari link --}}
                                <a href="{{ $action['url'] }}" target="_blank" 
                                   class="flex items-center justify-between p-4 border rounded-xl hover:border-indigo-600 hover:bg-indigo-50/30 transition-all group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-indigo-600">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ strtoupper($action['name']) }}</div>
                                            <div class="text-xs text-gray-500">Klik untuk membuka aplikasi / halaman pembayaran</div>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-300 group-hover:text-indigo-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Fallback --}}
            @if(empty($vaNumbers) && empty($permataVa) && empty($actions))
                <div class="p-6 text-center border-2 border-dashed rounded-2xl bg-gray-50">
                    <div class="text-gray-400 mb-2 font-medium">Data instruksi pembayaran belum tersedia.</div>
                    <button onclick="window.location.reload()" class="text-sm font-bold text-indigo-600 hover:underline inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Refresh Halaman
                    </button>
                </div>
            @endif

            <div class="space-y-4 pt-4 border-t border-gray-100">
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <p>Pembayaran akan diverifikasi secara <span class="font-bold">otomatis</span> oleh sistem dalam 1-2 menit.</p>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p>Setelah terkonfirmasi, status pesanan Anda akan berubah menjadi <span class="font-bold">CONFIRMED</span> dan Anda dapat melihat voucher di menu <a href="#" class="text-indigo-600 hover:underline">Booking Saya</a>.</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
            <a href="{{ route('bookings.show', ['booking' => $payment->booking_id]) }}" class="text-sm font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1">
                &larr; Lihat Detail Booking
            </a>
            <div class="flex gap-2">
                {{-- Kita bisa tambah tombol bantuan WA di sini --}}
                <div class="text-[10px] text-gray-400 font-mono">Status Internal: {{ $payment->status->value }}</div>
            </div>
        </div>
    </div>
</div>
