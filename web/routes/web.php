<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/gioi-thieu', [PageController::class, 'about'])->name('about');
Route::get('/danh-gia-khach-hang', [PageController::class, 'testimonials'])->name('testimonials');
Route::get('/cau-hoi-thuong-gap', [PageController::class, 'faq'])->name('faq');
Route::get('/lien-he', [PageController::class, 'contact'])->name('contact');

Route::get('/dich-vu', [ServiceController::class, 'index'])->name('services.index');
Route::get('/dich-vu/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/bang-gia', PriceController::class)->name('price');

Route::get('/du-an', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/du-an/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/tin-tuc', [PostController::class, 'index'])->name('posts.index');
Route::get('/tin-tuc/danh-muc/{slug}', [PostController::class, 'category'])->name('posts.category');
Route::get('/tin-tuc/{slug}', [PostController::class, 'show'])->name('posts.show');
