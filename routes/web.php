<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Auth::routes();


Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('activities', ActivityController::class)->names('activities');
    Route::get('/show-user-details/{user}', [ActivityController::class, 'showUserDetails'])->name('show-user-details');
});
