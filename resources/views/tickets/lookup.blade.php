@extends('layouts.nav')
@section('title', 'Cek Tiket Saya')
@section('content')
<main class="max-w-md mx-auto px-6 py-20">
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold">Cek Tiket Saya</h1>
        <p class="text-slate-500 mt-2">Masukkan email atau order ID untuk melihat tiket kamu.</p>
    </div>

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold text-sm">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
        <form action="{{ route('tickets.search') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wide">
                    Email atau Order ID
                </label>
                <input type="text" name="query" placeholder="contoh@gmail.com atau TRX-XXXXXXX"
                    class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition font-medium"
                    required value="{{ old('query') }}">
            </div>
            <button type="submit"
                class="w-full py-5 bg-indigo-600 text-white rounded-2xl font-black text-xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 active:scale-95 transition-all">
                Cari Tiket
            </button>
        </form>
    </div>
</main>
@endsection