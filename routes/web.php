<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', function () {
    return view('welcome'); // Pastikan view welcome tersedia
});

// Rute untuk login
Route::get('/account/login', [LoginController::class, 'index'])->name('account.login');
Route::post('/account/authenticate', [LoginController::class, 'authenticate'])->name('account.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('account.logout');

// Rute untuk registrasi
Route::get('/account/register', [RegisterController::class, 'index'])->name('account.register');
Route::post('/account/register', [RegisterController::class, 'store'])->name('account.register.store');

// Rute dengan middleware untuk autentikasi
Route::middleware(['auth'])->group(function () {
    // Dashboard untuk admin atau pelanggan
    Route::get('/dashboard/owner', [DashboardController::class, 'ownerDashboard'])->name('dashboard.owner');
    Route::get('/dashboard/customer', [DashboardController::class, 'customerDashboard'])->name('dashboard.customer');

    // Fitur checkout pelanggan
    Route::get('/checkout', [DashboardController::class, 'checkout'])->name('checkout');
    Route::post('/place-order', [DashboardController::class, 'placeOrder'])->name('place.order');
});

// Rute khusus untuk pemilik (admin) dengan middleware tambahan
Route::middleware(['auth', 'can:owner-access'])->group(function () {
    Route::prefix('owner')->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'showDashboard'])->name('owner.dashboard');
        Route::get('/product', [OwnerController::class, 'products'])->name('owner.products');
        Route::get('/order', [OwnerController::class, 'order'])->name('owner.orders');
        Route::get('/report', [OwnerController::class, 'report'])->name('owner.reports');
        Route::get('/orders-queue', [OwnerController::class, 'orderQueue'])->name('owner.orders.queue');

        Route::post('/update-order-status/{orderId}', [OwnerController::class, 'updateOrderStatus'])->name('owner.updateOrderStatus');
        Route::get('/profile', [OwnerController::class, 'profileOwner'])->name('owner.profile');
        Route::post('/update-password', [OwnerController::class, 'updatePassword'])->name('owner.updatePassword');

        // Manajemen produk
        Route::put('/products/{id}', [ProductController::class, 'updateProduct'])->name('owner.updateProduct');
        Route::put('/products/{id}/stock', [ProductController::class, 'updateStock'])->name('owner.updateStock');
    });
});

// Rute untuk pelanggan
Route::middleware(['auth'])->group(function () {
    Route::prefix('customer')->group(function () {
        Route::get('/menu/{category?}', [CustomerController::class, 'menu'])->name('customer.menu');
        Route::post('/add-to-cart/{productId}', [CustomerController::class, 'addToCart'])->name('customer.addToCart');
        Route::get('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
        Route::post('/process-payment', [CustomerController::class, 'processPayment'])->name('customer.processPayment');
        Route::get('/order-status/{orderId}', [CustomerController::class, 'orderStatus'])->name('customer.order.status');
    });
});
