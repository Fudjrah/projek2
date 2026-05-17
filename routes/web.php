<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// KUNCI PERBAIKAN: Formatnya diubah jadi Array agar VS Code tidak membaca sebagai error
Route::post('/chat/store', 'App\Http\Controllers\MessageController@store')->name('chat.store');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Jalur utama halaman chat memanggil fungsi index di MessageController
Route::get('/chat', 'App\Http\Controllers\MessageController@index')->middleware(['auth'])->name('chat.index');

// Route baru tambahan untuk mengambil riwayat chat grup (Biar si Ryul bisa baca chat lama)
Route::get('/chat/group/{groupId}', 'App\Http\Controllers\MessageController@getGroupMessages')->middleware(['auth'])->name('chat.groupMessages');

require __DIR__.'/auth.php';