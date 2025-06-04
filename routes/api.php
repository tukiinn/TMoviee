<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Api\WatchHistoryController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\Admin\DirectorController as AdminDirectorController;
use App\Http\Controllers\Admin\ActorController as AdminActorController;
use App\Http\Controllers\Api\MembershipPackageController;
use App\Http\Controllers\Admin\MembershipPackageController as AdminMembershipPackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MembershipController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\AdminPanelController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);

// Movie routes
Route::get('/movies/filter', [MovieController::class, 'filter']);
Route::get('/movies/search', [MovieController::class, 'search']);
Route::get('/movies/top10-series', [MovieController::class, 'top10Series']);
Route::get('/movies/single', [MovieController::class, 'getSingleMovies']);
Route::get('/movies/series', [MovieController::class, 'getSeriesMovies']);
Route::get('/movies/latest', [MovieController::class, 'getLatest']);
Route::get('/movies/featured', [MovieController::class, 'getFeatured']);
Route::get('/movies/{slug}', [MovieController::class, 'show']);
Route::get('/movies/{slug}/episodes', [MovieController::class, 'episodes']);
Route::get('/movies/{slug}/related', [MovieController::class, 'related']);
Route::get('/movies/{slug}/comments', [CommentController::class, 'index']);
Route::post('/movies/{slug}/comments', [CommentController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');

// Favorite routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/movies/{movie}/favorite', [FavoriteController::class, 'store']);
    Route::delete('/movies/{movie}/favorite', [FavoriteController::class, 'destroy']);
    Route::get('/movies/{movie}/is-favorite', [FavoriteController::class, 'isFavorite']);
    Route::get('/account', [UserController::class, 'index']);
    Route::get('/user/me', [UserController::class, 'me']);
    Route::post('/user/update', [UserController::class, 'update']);
    
    // Comment routes
    Route::post('/movies/{movie}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    // Like/Unlike comment
    Route::post('/comments/{comment}/like', [CommentController::class, 'like'])->middleware('auth:sanctum');
    Route::delete('/comments/{comment}/like', [CommentController::class, 'unlike'])->middleware('auth:sanctum');

    // Watch History Routes
    Route::get('/watch-history', [WatchHistoryController::class, 'index']);
    Route::post('/watch-history', [WatchHistoryController::class, 'store']);
    Route::get('/watch-history/{movieId}', [WatchHistoryController::class, 'getMovieProgress']);

    // Membership Package routes
    Route::apiResource('membership-packages', MembershipPackageController::class);

    // Payment Routes
    Route::post('/payment/create', [PaymentController::class, 'createPayment']);
    Route::get('/user/membership', [MembershipController::class, 'getUserMembership']);
    Route::get('/user/membership/check', [MembershipController::class, 'checkMembershipData']);
    Route::post('/payment/zalopay/create', [PaymentController::class, 'createZaloPay']);

    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::post('movies/import-ophim-bulk', [App\Http\Controllers\Admin\MovieController::class, 'importFromOphimBulk']);
    });
});

// Payment callback routes (no auth required)
Route::get('/payment/vnpay/return', [PaymentController::class, 'vnpayReturn']);

// Callback routes không cần auth

Route::match(['get', 'post'], '/payment/callback/momo', [PaymentController::class, 'handleMoMoCallback']);



// Admin routes
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/movies', [AdminMovieController::class, 'index']);
    Route::get('/movies/{id}', [AdminMovieController::class, 'show']);
    Route::put('/movies/{id}', [AdminMovieController::class, 'update']);
    Route::delete('/movies/{id}', [AdminMovieController::class, 'destroy']);
    Route::post('/movies/import-ophim', [AdminMovieController::class, 'importFromOphim']);
    Route::apiResource('directors', AdminDirectorController::class);
    Route::apiResource('actors', AdminActorController::class);
    Route::apiResource('membership-packages', AdminMembershipPackageController::class);
});

Route::get('/directors', [DirectorController::class, 'index']);

// Admin Panel Routes
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/stats', [AdminPanelController::class, 'getStats']);
    Route::get('/activities', [AdminPanelController::class, 'getRecentActivities']);
    Route::get('/recent-transactions', [AdminPanelController::class, 'getRecentTransactions']);
    Route::get('/revenue-chart', [AdminPanelController::class, 'getRevenueChartData']);
});

