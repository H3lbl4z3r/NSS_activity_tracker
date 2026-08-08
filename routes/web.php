<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityUpdateController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\ReportController;



Route::middleware('guest')->group(function () {
    Route::get('/signin', [LoginController::class, 'create'])->name('login');
    Route::post('/signin', [LoginController::class, 'store'])->name('login.store');
    Route::get('/signup', [LoginController::class, 'createAccount'])->name('register');
    Route::post('/signup', [LoginController::class, 'storeAccount'])->name('register.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

    // dashboard
    Route::get('/', function () {
        return view('pages.activities.index', ['title' => 'Dashboard']);
    })->name('dashboard');

    // NSS assignment: applications support team activity tracker
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
    Route::post('/activities/{activity}/updates', [ActivityUpdateController::class, 'store'])->name('activities.updates.store');

    Route::get('/handover', [HandoverController::class, 'index'])->name('handover.index');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

// profile pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

});
