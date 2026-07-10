@extends('layouts.nav')
@section('title', 'Hasil Pencarian Tiket')
@section('content')
<main class="max-w-2xl mx-auto px-6 py-20">
    <div class="mb-10">
        <a href="{{ route('tickets.lookup') }}" class="text-indigo-600 font-bold flex items-center gap-2 mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Cari Lagi
        </a>
        <h1 class="text-3xl font-extrabold">Tiket Ditemukan</h1>
        <p class="text-slate-500 mt-2">{{ $transactions->count() }} transaksi untuk "{{ $keyword }}"</p>
    </div>

    <div class="space-y-4">
        @foreach($transactions as $trx)
        <a href="{{ route('tickets.show', $trx->id) }}" class="block bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:border-indigo-300 hover:shadow-md transition">
            <div class="flex justify-between items-start gap-4">
                <div>
                    <p class="font-extrabold text-lg">{{ $trx->event->title ?? '-' }}</p>
                    <p class="text-slate-400 text-sm font-mono mt-1">{{ $trx->order_id }}</p>
                </div>
                @php
                    $statusColor = match($trx->status) {
                        'Success' => 'bg-emerald-50 text-emerald-600',
                        'Pending' => 'bg-amber-50 text-amber-600',
                        'Failed'  => 'bg-rose-50 text-rose-600',
                        default   => 'bg-slate-100 text-slate-500',
                    };
                @endphp
                <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $statusColor }} whitespace-nowrap">
                    {{ $trx->status }}
                </span>
            </div>
            <div class="flex justify-between items-center mt-4 pt-4 border-t text-sm">
                <span class="text-slate-500">{{ $trx->customer_name }}</span>
                <span class="text-indigo-600 font-bold">Lihat Tiket →</span>
            </div>
        </a>
        @endforeach
    </div>
</main>
@endsection