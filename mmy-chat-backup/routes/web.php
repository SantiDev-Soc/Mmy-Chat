<?php
declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedUserSessionController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

//Search contacts input
Route::get('/contacts/search', [ContactController::class, 'search'])->name('contacts.search');

Route::post('/logout', [AuthenticatedUserSessionController::class, 'destroy'])->name('logout');

require __DIR__.'/auth.php';
