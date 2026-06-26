@extends('layouts.nav')

@section('content')
<main class="max-w-md mx-auto px-6 py-20 text-center">
    <div class="bg-white rounded-[2.5rem] border border-slate-200 p-8 shadow-2xl space-y-6">
        <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto animate-pulse">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
        </div>

        <div>
            <h1 class="text-2xl font-black text-slate-800">Selesaikan Pembayaran</h1>
            <p class="text-slate-500 text-sm mt-1">Sistem sedang membuka gerbang pembayaran aman Midtrans.</p>
        </div>

        <div class="bg-slate-50 rounded-2xl p-4 text-left space-y-2 border border-slate-100">
            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Order ID: <span class="font-mono text-slate-700">{{ $transaction->order_id }}</span></p>
            <p class="font-extrabold text-slate-800 line-clamp-1">{{ $transaction->event->title }}</p>
            <div class="flex justify-between items-center pt-2 border-t text-sm">
                <span class="text-slate-500 font-medium">Total Bayar:</span>
                <span class="text-indigo-600 font-black text-lg">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <button id="pay-button" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">
            Bayar Sekarang
        </button>
        
        <a href="{{ route('home') }}" class="block text-sm text-slate-400 hover:text-slate-600 font-medium underline">
            Kembali ke Beranda
        </a>
    </div>
</main>

<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script type="text/javascript">
    var payButton = document.getElementById('pay-button');
    
    // Fungsi memicu popup Midtrans Snap
    function triggerPayment() {
        window.snap.pay('{{ $transaction->snap_token }}', {
            onSuccess: function(result) {
                alert("Pembayaran Berhasil!"); 
                window.location.href = "{{ route('home') }}";
            },
            onPending: function(result) {
                alert("Menunggu Pembayaran Anda."); 
                window.location.href = "{{ route('home') }}";
            },
            onError: function(result) {
                alert("Pembayaran Gagal, Silakan coba kembali.");
                window.location.href = "{{ route('home') }}";
            },
            onClose: function() {
                alert('Anda menutup halaman pembayaran sebelum selesai.');
            }
        });
    }

    // Otomatis panggil popup saat halaman termuat sempurna
    document.addEventListener("DOMContentLoaded", function() {
        triggerPayment();
    });

    // Cadangan jika popup tertutup, user bisa klik tombol manual
    payButton.onclick = function() {
        triggerPayment();
    };
</script>
@endsection