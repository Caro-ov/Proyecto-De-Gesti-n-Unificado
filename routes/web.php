<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\ProfileController;
use App\Models\Event;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'active.role'])->name('dashboard');

Route::middleware(['auth', 'active.role'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/mis-eventos', [EventController::class, 'mine'])
            ->name('mine');
        Route::get('/', [EventController::class, 'index'])
            ->can('viewAny', Event::class)
            ->name('index');
        Route::get('/create', [EventController::class, 'create'])
            ->can('create', Event::class)
            ->name('create');
        Route::post('/', [EventController::class, 'store'])
            ->can('create', Event::class)
            ->name('store');
        Route::post('/{event}/registrations', [EventRegistrationController::class, 'store'])
            ->name('registrations.store');
        Route::patch('/{event}/registrations/{registration}', [EventRegistrationController::class, 'update'])
            ->name('registrations.update');
        Route::delete('/{event}/registrations/{registration}', [EventRegistrationController::class, 'destroy'])
            ->name('registrations.destroy');
        Route::get('/{event}/edit', [EventController::class, 'edit'])
            ->can('update', 'event')
            ->name('edit');
        Route::patch('/{event}', [EventController::class, 'update'])
            ->can('update', 'event')
            ->name('update');
        Route::delete('/{event}', [EventController::class, 'destroy'])
            ->can('delete', 'event')
            ->name('destroy');
        Route::get('/{event}', [EventController::class, 'show'])
            ->can('view', 'event')
            ->name('show');
    });
});

require __DIR__.'/auth.php';
