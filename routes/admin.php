<?php

use App\Http\Controllers\Admin\CarouselItemController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoomImageController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('room-types', RoomTypeController::class)->except(['show']);
        Route::resource('facilities', FacilityController::class)->except(['show']);
        Route::resource('posts', PostController::class)->except(['show']);
        Route::resource('carousel-items', CarouselItemController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('posts/{post}/preview', [PostController::class, 'show'])->name('posts.show');

        Route::post('room-types/{roomType}/images', [RoomImageController::class, 'store'])->name('room-images.store');
        Route::patch('images/{image}/cover', [RoomImageController::class, 'setCover'])->name('room-images.cover');
        Route::delete('images/{image}', [RoomImageController::class, 'destroy'])->name('room-images.destroy');

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
