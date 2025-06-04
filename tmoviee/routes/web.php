<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ActorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminPanelController;

// API Routes
Route::prefix('api')->group(function () {
    // Movies
    Route::get('/movies/single', [MovieController::class, 'getSingleMovies']);
    Route::get('/movies/series', [MovieController::class, 'getSeriesMovies']);
    Route::get('/movies/filter', [MovieController::class, 'filter']);
    Route::get('/movies/{slug}/related', [MovieController::class, 'related']);
    Route::get('/movies/{slug}', [MovieController::class, 'show']);
    Route::get('/movies/genre/{slug}', [MovieController::class, 'byGenreSlug']);
    Route::get('/movies/country/{slug}', [MovieController::class, 'byCountrySlug']);

    // Genres
    Route::get('/genres', [GenreController::class, 'index']);
    Route::get('/genres/{slug}', [GenreController::class, 'show']);

    // Countries
    Route::get('/countries', [CountryController::class, 'index']);
    Route::get('/countries/{slug}', [CountryController::class, 'show']);

    // Actors
    Route::get('/actors', [ActorController::class, 'index']);
    Route::get('/actors/{slug}', [ActorController::class, 'show']);
});

// SPA Routes
Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/categories', function () {
    return view('layouts.app');
});

Route::get('/genre/{slug}', function () {
    return view('layouts.app');
});

// Route trả về view thành công/thất bại của VNPay (PHẢI đặt TRƯỚC wildcard)
Route::get('/payment/vnpay/return', [PaymentController::class, 'vnpayReturn']);


// Payment success/fail routes
Route::get('/payment/success', [PaymentController::class, 'handlePaymentSuccess'])->name('payment.success');

Route::get('/payment/fail', function () {
    return view('payment.fail');
})->name('payment.fail');

// Payment Routes
Route::post('/payment/zalopay/create', [PaymentController::class, 'createZaloPay'])->name('payment.zalopay.create');

// Route wildcard cho SPA (phải để cuối cùng)
Route::get('/{path}', function () {
    return view('layouts.app');
})->where('path', '.*');

Route::get('/admin', [AdminPanelController::class, 'index'])->name('admin.panel');
