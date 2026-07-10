@extends('layouts.nav')
@section('title', 'Tiket - ' . $transaction->event->title)
@section('content')
<main class="max-w-md mx-auto px-6 py-20">
    <div class="mb-8">
        <a href="javascript:history.back()" class="text-indigo-600 font-bold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-2xl overflow-hidden">
        <div class="bg-indigo-600 p-8 text-white text-center">
            <p class="text-indigo-200 text-xs font-bold uppercase tracking-widest">E-Ticket</p>
            <h1 class="text-2xl font-black mt-2">{{ $transaction->event->title }}</h1>
            <p class="text-indigo-200 text-sm mt-1">
                {{ \Carbon\Carbon::parse($transaction->event->date)->format('d M Y') }} • {{ $transaction->event->location }}
            </p>
        </div>

        <div class="p-8 text-center border-b border-dashed">
            @if($transaction->checked_in_at)
            <div class="mb-4 inline-block px-4 py-2 bg-emerald-50 text-emerald-600 rounded-full text-xs font-bold">
                ✓ Sudah Check-in pada {{ $transaction->checked_in_at->format('d M Y, H:i') }}
            </div>
            @endif
            <img src="{{ $qrUrl }}" alt="QR Tiket" class="mx-auto rounded-2xl border border-slate-100">
            <p class="text-slate-400 text-xs mt-4 font-medium">Tunjukkan QR ini ke panitia saat masuk acara</p>
        </div>

        <div class="p-8 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-slate-400 font-bold uppercase tracking-wide text-xs">Order ID</span>
                <span class="font-mono font-bold text-slate-700">{{ $transaction->order_id }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-400 font-bold uppercase tracking-wide text-xs">Nama</span>
                <span class="font-bold text-slate-700">{{ $transaction->customer_name }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-slate-400 font-bold uppercase tracking-wide text-xs">Email</span>
                <span class="font-bold text-slate-700">{{ $transaction->customer_email }}</span>
            </div>
            <div class="flex justify-between text-sm pt-3 border-t">
                <span class="text-slate-400 font-bold uppercase tracking-wide text-xs">Total Bayar</span>
                <span class="font-black text-indigo-600">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</main>
@endsection