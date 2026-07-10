<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    public function index()
    {
        return view('admin.checkin.index');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $decoded = json_decode($request->qr_data, true);

        if (!$decoded || !isset($decoded['order_id']) || !isset($decoded['sig'])) {
            return response()->json(['status' => 'invalid', 'message' => 'Format QR tidak dikenali.'], 400);
        }

        $orderId = $decoded['order_id'];
        $signature = $decoded['sig'];

        if (!Transaction::verifySignature($orderId, $signature)) {
            return response()->json(['status' => 'invalid', 'message' => 'QR tidak valid / dipalsukan.'], 400);
        }

        $transaction = Transaction::with('event')->where('order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['status' => 'invalid', 'message' => 'Transaksi tidak ditemukan.'], 404);
        }

        if ($transaction->status !== 'Success') {
            return response()->json(['status' => 'invalid', 'message' => 'Tiket ini belum lunas / statusnya ' . $transaction->status], 400);
        }

        if ($transaction->checked_in_at) {
            return response()->json([
                'status' => 'already_checked_in',
                'message' => 'Tiket sudah check-in sebelumnya pada ' . $transaction->checked_in_at->format('d M Y, H:i'),
                'data' => [
                    'name' => $transaction->customer_name,
                    'event' => $transaction->event->title ?? '-',
                    'order_id' => $transaction->order_id,
                ],
            ]);
        }

        $transaction->update(['checked_in_at' => now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in berhasil!',
            'data' => [
                'name' => $transaction->customer_name,
                'event' => $transaction->event->title ?? '-',
                'order_id' => $transaction->order_id,
            ],
        ]);
    }
}