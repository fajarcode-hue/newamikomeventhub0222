<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/checkout/{event}', [\App\Http\Controllers\CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');

// TAMBAHKAN BARIS INI UNTUK DETAIL EVENT PUBLIK
Route::get('/events/{event}', [\App\Http\Controllers\Admin\EventController::class, 'show'])->name('events.show');

// Rute untuk menampilkan halaman pembayaran popup
Route::get('/checkout/payment/{transaction}', [\App\Http\Controllers\CheckoutController::class, 'payment'])->name('checkout.payment');
// Rute untuk menerima laporan pembayaran dari Midtrans
Route::post('/midtrans/callback', [\App\Http\Controllers\CheckoutController::class, 'callback'])->name('midtrans.callback');

// Ini route detail yang diakses publik/user biasa

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    // Rute Login bebas akses
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Mengamankan Route Administrasi di balik tembok (Middleware)
    Route::middleware(['auth', 'admin'])->group(function () {
     Route::resource('events', EventController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('partners', PartnerController::class);
    Route::get('transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');

    });
});






// Route::prefix('admin')->name('admin.')->group(function () {
//     Route::resource('events', EventController::class);
//     Route::resource('categories', CategoryController::class);
//     Route::resource('partners', PartnerController::class);
// });
