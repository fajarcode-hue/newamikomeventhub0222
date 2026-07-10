<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function lookup()
    {
        return view('tickets.lookup');
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $keyword = $request->query;

        $transactions = Transaction::with('event')
            ->where('customer_email', $keyword)
            ->orWhere('order_id', $keyword)
            ->latest()
            ->get();

        if ($transactions->isEmpty()) {
            return back()->with('error', 'Tidak ditemukan transaksi dengan email atau order ID tersebut.');
        }

        return view('tickets.results', compact('transactions', 'keyword'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('event');

        if ($transaction->status !== 'Success') {
            return back()->with('error', 'Tiket ini belum bisa ditampilkan karena pembayaran belum berhasil.');
        }

        $signature = $transaction->getQrSignature();
        $qrPayload = json_encode([
            'order_id' => $transaction->order_id,
            'sig' => $signature,
        ]);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrPayload);

        return view('tickets.show', compact('transaction', 'qrUrl'));
    }
}