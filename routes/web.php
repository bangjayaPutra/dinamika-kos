<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\FacilityController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PostController;
use App\Http\Controllers\Public\ReviewController;
use App\Http\Controllers\Public\RoomTypeController;
use App\Http\Controllers\WaClickController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('tipe-kamar', [RoomTypeController::class, 'index'])->name('public.room-types.index');
Route::get('tipe-kamar/{slug}', [RoomTypeController::class, 'show'])->name('public.room-types.show');

Route::get('fasilitas', [FacilityController::class, 'index'])->name('public.facilities.index');

Route::get('berita', [PostController::class, 'index'])->name('public.posts.index');
Route::get('berita/{slug}', [PostController::class, 'show'])->name('public.posts.show');

Route::get('kontak', [ContactController::class, 'index'])->name('public.contact');

Route::post('reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::post('wa-clicks', [WaClickController::class, 'store'])->name('wa-clicks.store');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
