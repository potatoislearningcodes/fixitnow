<?php

use App\Http\Controllers\Admin\TechnicianVerificationController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| FixItNow routes
|--------------------------------------------------------------------------
| Copy the contents of this file into routes/web.php (inside the
| Route::middleware(['auth'])->group() block that Breeze/Jetstream scaffolds
| for you), or require it from web.php. Keeping it in its own file makes it
| easy for the group to see all FixItNow-specific routes in one place.
*/

// ---- Customer routes ----
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
});

// ---- Shared (customer, technician, admin can all view one booking) ----
Route::middleware(['auth'])->group(function () {
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
});

// ---- Technician routes ----
Route::middleware(['auth', 'role:technician'])->group(function () {
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])
        ->name('bookings.update-status');
});

// ---- Admin routes ----
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/technicians', [TechnicianVerificationController::class, 'index'])->name('technicians.index');
    Route::patch('/technicians/{technician}/approve', [TechnicianVerificationController::class, 'approve'])->name('technicians.approve');
    Route::patch('/technicians/{technician}/reject', [TechnicianVerificationController::class, 'reject'])->name('technicians.reject');
});
