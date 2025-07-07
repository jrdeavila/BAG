<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\FinishActivityController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Auth::routes();


Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::put('/activities/{activity}/finish', [ActivityController::class, 'finish'])->name('activities.finish');
    Route::get('/activities/{activity}/details', [ActivityController::class, 'show'])->name('activities.show');
    Route::resource('activities', ActivityController::class)->names('activities')->except(['show']);
    Route::get('/show-user-details/{user}', [ActivityController::class, 'showUserDetails'])->name('show-user-details');
    Route::get('/reports', ReportController::class)->name('reports.index');
});
