@extends('layouts.admin', ['title' => 'Kelola Transaksi'])

@section('content')
<header class="flex justify-between items-center mb-10">
    <div>
        <h1 class="text-3xl font-black">Kelola Transaksi</h1>
        <p class="text-slate-500 font-medium">Pantau semua transaksi tiket yang masuk di sini.</p>
    </div>
</header>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">

    {{-- FORM FILTER --}}
    <div class="p-6 border-b border-slate-100">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-bold text-slate-400 mb-1 block">Cari</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nama, email, atau order ID..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 mb-1 block">Status</label>
                <select name="status" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Success" {{ request('status') == 'Success' ? 'selected' : '' }}>Success</option>
                    <option value="Failed" {{ request('status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 mb-1 block">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 mb-1 block">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition">
                Filter
            </button>

            @if(request('search') || request('status') || request('date_from') || request('date_to'))
                <a href="{{ route('admin.transactions.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">No</th>
                    <th class="px-8 py-4">Order ID</th>
                    <th class="px-8 py-4">Event</th>
                    <th class="px-8 py-4">Customer</th>
                    <th class="px-8 py-4">Total</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y border-t">
                @forelse($transactions as $index => $trx)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-8 py-6 font-bold text-slate-400">
                        {{ $transactions->firstItem() + $index }}
                    </td>
                    <td class="px-8 py-6">
                        <p class="font-mono text-xs font-bold text-slate-700">{{ $trx->order_id }}</p>
                    </td>
                    <td class="px-8 py-6">
                        <p class="font-black text-slate-800">{{ $trx->event->title ?? '-' }}</p>
                    </td>
                    <td class="px-8 py-6">
                        <p class="font-bold text-slate-800">{{ $trx->customer_name }}</p>
                        <p class="text-xs text-slate-400">{{ $trx->customer_email }}</p>
                    </td>
                    <td class="px-8 py-6">
                        <p class="font-bold text-indigo-600">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-8 py-6">
                        @php
                            $statusColor = match($trx->status) {
                                'Success' => 'bg-emerald-50 text-emerald-600',
                                'Pending' => 'bg-amber-50 text-amber-600',
                                'Failed'  => 'bg-rose-50 text-rose-600',
                                default   => 'bg-slate-100 text-slate-500',
                            };
                        @endphp
                        <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $statusColor }}">
                            {{ $trx->status }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-sm text-slate-400">
                        {{ $trx->created_at->format('d M Y, H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-8 py-12 text-center text-slate-400 font-medium">
                        Tidak ada transaksi yang cocok dengan filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
    <div class="p-6 border-t border-slate-100">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection