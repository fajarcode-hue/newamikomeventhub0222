<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Midtrans\Notification;

class CheckoutController extends Controller
{
    public function create(Event $event)
    {
        $categories = \App\Models\Category::all();
        return view('checkout.create', compact('event', 'categories'));
    }

    public function store(Request $request, Event $event)
    {
        // 1. Validasi Input 
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        // 2. Cegah Check-out Jika Tiket Habis 
        if ($event->stock <= 0) {
            return back()->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        // 3. Generate Kode TRX (Unik) & Total Harga 
        $orderId = 'TRX-' . time() . '-' . strtoupper(Str::random(5));
        $totalPrice = $event->price + 5000; // Harga tiket + biaya layanan dummy

        // == INTEGRASI MIDTRANS START ==
        // Set konfigurasi midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Buat parameter transaksi untuk dikirim ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone' => $request->customer_phone,
            ],
            'item_details' => [
                [
                    'id' => $event->id,
                    'price' => (int) $event->price,
                    'quantity' => 1,
                    'name' => Str::limit($event->title, 45),
                ],
                [
                    'id' => 'ADMIN-FEE',
                    'price' => 5000,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan',
                ]
            ]
        ];

        try {
            // Minta Snap Token ke Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke sistem pembayaran: ' . $e->getMessage());
        }
        // == INTEGRASI MIDTRANS END ==

        // 4. Merekam Transaksi ke Database dengan snap_token 
        $transaction = Transaction::create([
            'event_id' => $event->id,
            'order_id' => $orderId,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'total_price' => $totalPrice,
            'status' => 'Pending', 
            'snap_token' => $snapToken, // Token disimpan ke database 
        ]);

        // 5. Alihkan ke halaman konfirmasi pembayaran popup 
        return redirect()->route('checkout.payment', $transaction->id);
    }

    // Fungsi baru untuk menampilkan halaman pembayaran
    public function payment(Transaction $transaction)
    {
        // Memuat relasi event agar data judul/poster bisa tampil
        $transaction->load('event');
        return view('checkout.payment', compact('transaction'));
    }

    public function callback(Request $request)
{
    // 1. Inisialisasi Kredensial Midtrans
    \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

    try {
        // 2. Tangkap notifikasi otomatis dari Midtrans
        $notif = new \Midtrans\Notification();
    } catch (\Exception $e) {
        return response()->json(['message' => 'Notifikasi tidak valid'], 400);
    }

    // 3. Ambil data status transaksi dan Order ID
    $transactionStatus = $notif->transaction_status;
    $orderId = $notif->order_id;

    // 4. Cari data transaksi di database kita berdasarkan order_id
    $transaction = \App\Models\Transaction::where('order_id', $orderId)->first();

    if (!$transaction) {
        return response()->json(['message' => 'Transaksi tidak ditemukan di database'], 444);
    }

    // 5. Logika Perubahan Status (State Handling)
    if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
        
        // JIKA SUKSES: Ubah status transaksi jadi Success
        $transaction->update(['status' => 'Success']);
        
        // OPSIONAL: Kurangi stok tiket event secara otomatis saat sukses bayar
        if ($transaction->event) {
            $transaction->event->decrement('stock');
        }

    } elseif ($transactionStatus == 'pending') {
        $transaction->update(['status' => 'Pending']);
        
    } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
        $transaction->update(['status' => 'Failed']);
    }

    return response()->json(['message' => 'Webhook berhasil diproses!']);
}
}