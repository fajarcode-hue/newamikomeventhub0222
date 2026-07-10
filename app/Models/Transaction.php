<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'event_id', 'order_id', 'customer_name', 'customer_email', 'customer_phone', 'total_price', 'status', 'checked_in_at', 'snap_token'
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Membuat signature unik untuk order_id ini (dipakai di QR code)
    public function getQrSignature(): string
    {
        return hash_hmac('sha256', $this->order_id, config('app.key'));
    }

    // Verifikasi apakah signature dari QR yang di-scan cocok
    public static function verifySignature(string $orderId, string $signature): bool
    {
        $expected = hash_hmac('sha256', $orderId, config('app.key'));
        return hash_equals($expected, $signature);
    }
}