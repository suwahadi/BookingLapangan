<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.bookings.index') }}" wire:navigate class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-700 transition-colors">Daftar Pesanan</a>
                <span class="text-gray-300">/</span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Detail #{{ $booking->booking_code }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight font-display italic uppercase">Detail <span class="text-indigo-600">Pesanan</span></h1>
            <p class="text-gray-500 mt-1 tracking-tight">Rincian lengkap transaksi dan status pembayaran pelanggan</p>
        </div>

        <div class="flex flex-wrap gap-3">
            @if($booking->status === \App\Enums\BookingStatus::HOLD)
            <button wire:click="confirmManual" wire:confirm="Anda yakin ingin mengonfirmasi pesanan ini secara manual?" 
                class="bg-emerald-600 text-white px-8 py-4 rounded-[1.5rem] font-black text-sm tracking-widest hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-100 flex items-center gap-2">
                KONFIRMASI MANUAL
            </button>
            @endif

            @if(!in_array($booking->status, [\App\Enums\BookingStatus::CANCELLED, \App\Enums\BookingStatus::EXPIRED]))
            <button wire:click="cancelBooking" wire:confirm="Batalkan pesanan ini?" 
                class="bg-white border-2 border-rose-500 text-rose-500 px-8 py-3.5 rounded-[1.5rem] font-black text-sm tracking-widest hover:bg-rose-50 transition-all flex items-center gap-2">
                BATALKAN PESANAN
            </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Summary Card -->
            <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-50 overflow-hidden">
                <div class="p-10 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <h4 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight italic">Ringkasan <span class="text-indigo-600">Booking</span></h4>
                    </div>
                    <span class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest">
                        Status: {{ $booking->status->label() }}
                    </span>
                </div>
                
                <div class="p-10 grid grid-cols-2 md:grid-cols-3 gap-10">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tempat Olahraga</p>
                        <p class="text-sm font-black text-gray-900">{{ $booking->venue->name }}</p>
                        <p class="text-[10px] font-bold text-indigo-500 uppercase italic">{{ $booking->court->name }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal / Jam Main</p>
                        <p class="text-sm font-black text-gray-900">{{ $booking->booking_date->translatedFormat('l, d F Y') }}</p>
                        <div class="flex flex-wrap items-center gap-1.5 mt-1">
                            @foreach($booking->grouped_slots as $slot)
                                <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[10px] font-bold font-mono">
                                    {{ $slot['start'] }} - {{ $slot['end'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Bayar</p>
                        <p class="text-lg font-black text-gray-900 tracking-tighter italic">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                        <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Terbayar: Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</p>
                    </div>

                </div>
            </div>

            <!-- Payment List -->
            <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-50 overflow-hidden">
                <div class="p-10 border-b border-gray-50 flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight italic">Riwayat <span class="text-emerald-600">Pembayaran</span></h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Order ID</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Metode</th>
                                <th class="px-10 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Nominal (Rp)</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($booking->payments as $payment)
                            <tr class="text-sm">
                                <td class="px-10 py-6 font-bold text-gray-600">#{{ $payment->provider_order_id }}</td>
                                <td class="px-10 py-6">
                                    <span class="font-black text-gray-900 uppercase text-[10px] tracking-widest">{{ $payment->payment_method ?? 'Gateway' }}</span>
                                    <!-- <p class="text-[10px] text-gray-400">{{ $payment->type->label() }}</p> -->
                                </td>
                                <td class="px-10 py-8 text-right font-black text-gray-900 italic tracking-tighter">
                                    {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <span class="px-3 py-1 bg-{{ $payment->status->color() }}-100 text-{{ $payment->status->color() }}-600 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-10 py-10 text-center text-gray-400 italic font-bold">Belum ada data pembayaran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reschedule Requests -->
            @if($booking->rescheduleRequests->isNotEmpty())
            <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-50 overflow-hidden">
                <div class="p-10 border-b border-gray-50 flex items-center justify-between bg-amber-50/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-600 rounded-xl flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h4 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight italic">Request <span class="text-amber-600">Reschedule</span></h4>
                    </div>
                </div>
                
                <div class="p-10 space-y-8">
                    @foreach($booking->rescheduleRequests as $req)
                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 flex flex-col md:flex-row justify-between gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-{{ $req->status->color() }}-100 text-{{ $req->status->color() }}-600 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                    {{ $req->status->label() }}
                                </span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest italic">{{ $req->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-8">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal Baru</p>
                                    <p class="text-sm font-black text-gray-900">{{ $req->new_date->translatedFormat('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pukul</p>
                                    <p class="text-sm font-black text-gray-900">{{ substr($req->new_start_time, 0, 5) }} - {{ substr($req->new_end_time, 0, 5) }}</p>
                                </div>
                            </div>

                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Alasan Pelanggan</p>
                                <p class="text-xs font-bold text-gray-600 italic">"{{ $req->reason }}"</p>
                            </div>
                        </div>

                        @if($req->isPending())
                        <div class="flex flex-col gap-3 justify-center min-w-[200px]">
                            <button wire:click="processReschedule({{ $req->id }}, 'APPROVE')" wire:confirm="Setujui perubahan jadwal ini?" class="w-full bg-gray-900 text-white py-4 rounded-2xl font-black text-[10px] tracking-widest hover:bg-black transition-all shadow-xl shadow-gray-200">
                                SETUJUI RE-SCHEDULE
                            </button>
                            <button wire:click="processReschedule({{ $req->id }}, 'REJECT')" wire:confirm="Tolak perubahan jadwal ini?" class="w-full bg-white border-2 border-rose-100 text-rose-500 py-4 rounded-2xl font-black text-[10px] tracking-widest hover:bg-rose-50 transition-all">
                                TOLAK
                            </button>
                        </div>
                        @else
                        <div class="flex flex-col justify-center text-right">
                             <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Diproses oleh</p>
                             <p class="text-xs font-black text-gray-900">{{ $req->approver->name ?? 'Admin' }}</p>
                             <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase leading-none">{{ $req->approved_at?->translatedFormat('d F Y, H:i') }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Refund Requests -->
            @if($booking->refundRequests->isNotEmpty())
            <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-50 overflow-hidden">
                <div class="p-10 border-b border-gray-50 flex items-center justify-between bg-rose-50/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-rose-600 rounded-xl flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z" /></svg>
                        </div>
                        <h4 class="text-xl font-black font-display text-gray-900 uppercase tracking-tight italic">Request <span class="text-rose-600">Refund</span></h4>
                    </div>
                </div>

                <div class="p-10 space-y-8">
                    @foreach($booking->refundRequests as $req)
                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 flex flex-col md:flex-row justify-between gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-{{ $req->status->color() }}-100 text-{{ $req->status->color() }}-600 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                    {{ $req->status->label() }}
                                </span>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">Order #{{ $req->payment->provider_order_id }}</span>
                            </div>

                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nominal Refund</p>
                                <p class="text-xl font-black font-display italic text-rose-600 tracking-tighter">Rp {{ number_format($req->amount, 0, ',', '.') }}</p>
                            </div>

                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Alasan Pembatalan</p>
                                <p class="text-xs font-bold text-gray-600 italic">"{{ $req->reason }}"</p>
                            </div>
                        </div>

                        @if($req->isPending())
                        <div class="flex flex-col gap-3 justify-center min-w-[200px]">
                            <button wire:click="processRefund({{ $req->id }}, 'APPROVE')" wire:confirm="Tandai refund ini sudah selesai diproses secara manual?" class="w-full bg-rose-600 text-white py-4 rounded-2xl font-black text-[10px] tracking-widest hover:bg-rose-700 transition-all shadow-xl shadow-rose-100 uppercase">
                                SELESAIKAN REFUND
                            </button>
                            <button wire:click="processRefund({{ $req->id }}, 'REJECT')" wire:confirm="Tolak permintaan refund ini?" class="w-full bg-white border-2 border-rose-100 text-rose-500 py-4 rounded-2xl font-black text-[10px] tracking-widest hover:bg-rose-50 transition-all">
                                TOLAK
                            </button>
                        </div>
                        @else
                        <div class="flex flex-col justify-center text-right">
                             <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Diproses oleh</p>
                             <p class="text-xs font-black text-gray-900">{{ $req->processor->name ?? 'Admin' }}</p>
                             <p class="text-[9px] font-bold text-gray-400 mt-1 uppercase leading-none">{{ $req->processed_at?->translatedFormat('d F Y, H:i') }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-10">
            <!-- Customer Card -->
            <div class="bg-white p-10 rounded-[3rem] shadow-2xl border border-gray-50 space-y-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <h4 class="text-lg font-black font-display text-gray-900 uppercase tracking-tight italic">Info <span class="text-indigo-600">Pelanggan</span></h4>
                </div>

                <div class="space-y-6">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</span>
                        <span class="text-sm font-black text-gray-900">{{ $booking->customer_name }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</span>
                        <span class="text-sm font-bold text-gray-600 truncate underline">{{ $booking->customer_email }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">WhatsApp / HP</span>
                        <span class="text-sm font-black text-indigo-600 tracking-widest">{{ $booking->customer_phone }}</span>
                    </div>
                </div>

                @if($booking->notes)
                <div class="pt-6 border-t border-gray-50">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Catatan:</p>
                    <p class="text-xs font-bold text-gray-500 bg-gray-50 p-4 rounded-2xl italic">"{{ $booking->notes }}"</p>
                </div>
                @endif
            </div>

            <!-- Actions Log (Simplified) -->
            <div class="bg-gray-900 p-10 rounded-[3rem] shadow-2xl text-white space-y-6">
                 <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h4 class="text-lg font-black font-display uppercase tracking-tight italic">Timeline</h4>
                </div>
                
                <div class="space-y-6 relative before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-white/10">
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1.5 w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-gray-900"></div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Created</p>
                        <p class="text-xs font-bold">{{ $booking->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    @if($booking->paid_amount > 0)
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1.5 w-4 h-4 rounded-full bg-indigo-500 ring-4 ring-gray-900"></div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400">Payment Processed</p>
                        <p class="text-xs font-bold">{{ $booking->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
