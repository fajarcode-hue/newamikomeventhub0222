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

Route::get('/events/{event}', [\App\Http\Controllers\Admin\EventController::class, 'show'])->name('events.show');

Route::get('/checkout/payment/{transaction}', [\App\Http\Controllers\CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/midtrans/callback', [\App\Http\Controllers\CheckoutController::class, 'callback'])->name('midtrans.callback');

// Rute Cek Tiket Publik (tanpa login)
Route::get('/tickets', [\App\Http\Controllers\TicketController::class, 'lookup'])->name('tickets.lookup');
Route::post('/tickets/search', [\App\Http\Controllers\TicketController::class, 'search'])->name('tickets.search');
Route::get('/tickets/{transaction}', [\App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');

Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::resource('events', EventController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('partners', PartnerController::class);
        Route::get('transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('transactions.index');
        Route::get('checkin', [\App\Http\Controllers\Admin\CheckinController::class, 'index'])->name('checkin.index');
        Route::post('checkin/verify', [\App\Http\Controllers\Admin\CheckinController::class, 'verify'])->name('checkin.verify');
    });
});