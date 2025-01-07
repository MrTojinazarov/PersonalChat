<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/', [MainController::class, 'index'])->name('main.index');
    Route::get('/chat/{receiver_id}', [MainController::class, 'chat'])->name('chat'); 
    Route::post('/chat/{chat_id}/message', [MainController::class, 'sendMessage'])->name('sendMessage');
});

require __DIR__.'/auth.php';