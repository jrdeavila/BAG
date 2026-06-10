<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\AreaAccessController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'home' : 'login');
});



Auth::routes();


Route::middleware(['auth', 'platform.access'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::put('/activities/{activity}/finish', [ActivityController::class, 'finish'])->name('activities.finish');
    Route::get('/activities/{activity}/details', [ActivityController::class, 'show'])->name('activities.show');
    Route::get('/activities-employees/search', [ActivityController::class, 'searchEmployees'])->name('activities.employees.search');
    Route::resource('activities', ActivityController::class)->names('activities')->except(['show']);
    Route::get('/show-user-details/{user}', [ActivityController::class, 'showUserDetails'])->name('show-user-details');
    Route::get('/reports', ReportController::class)->name('reports.index');

    Route::middleware('can:manage-areas')->prefix('admin')->name('admin.')->group(function () {
        Route::get('areas', [AreaAccessController::class, 'index'])->name('areas.index');
        Route::post('areas/{area}/toggle', [AreaAccessController::class, 'toggleArea'])->name('areas.toggle');
        Route::post('areas/{area}/responsibles', [AreaAccessController::class, 'addResponsible'])->name('areas.responsibles.add');
        Route::delete('areas/{area}/responsibles/{user}', [AreaAccessController::class, 'removeResponsible'])->name('areas.responsibles.remove');
        Route::post('special-users', [AreaAccessController::class, 'addSpecial'])->name('special-users.add');
        Route::delete('special-users/{user}', [AreaAccessController::class, 'removeSpecial'])->name('special-users.remove');
        Route::post('blocked-users', [AreaAccessController::class, 'addBlocked'])->name('blocked-users.add');
        Route::delete('blocked-users/{user}', [AreaAccessController::class, 'removeBlocked'])->name('blocked-users.remove');
    });
});
