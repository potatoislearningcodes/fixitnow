<?php

use App\Http\Controllers\Admin\BookingAssignmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TechnicianVerificationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\TechnicianDashboardController;
use App\Http\Controllers\TechnicianProfileController;
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

    // Technician or admin — authorization is checked inside the controller
    // since it depends on whether the user is the assigned technician.
    Route::patch('/bookings/{booking}/payment', [BookingController::class, 'recordPayment'])
        ->name('bookings.record-payment');
});

// ---- Technician routes ----
Route::middleware(['auth', 'role:technician'])->group(function () {
    Route::get('/technician/dashboard', [TechnicianDashboardController::class, 'index'])
        ->name('technician.dashboard');

    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])
        ->name('bookings.update-status');

    // A technician completes/edits their own profile and document here.
    Route::get('/technician/profile', [TechnicianProfileController::class, 'edit'])
        ->name('technician.profile.edit');
    Route::patch('/technician/profile', [TechnicianProfileController::class, 'update'])
        ->name('technician.profile.update');
});

// ---- Admin routes ----
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/technicians', [TechnicianVerificationController::class, 'index'])->name('technicians.index');
    Route::patch('/technicians/{technician}/approve', [TechnicianVerificationController::class, 'approve'])->name('technicians.approve');
    Route::patch('/technicians/{technician}/reject', [TechnicianVerificationController::class, 'reject'])->name('technicians.reject');

    Route::get('/bookings/unassigned', [BookingAssignmentController::class, 'index'])->name('bookings.unassigned');
    Route::patch('/bookings/{booking}/assign', [BookingAssignmentController::class, 'assign'])->name('bookings.assign');
});
