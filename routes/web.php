<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\Gear;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'featured_gears' => Gear::where('is_featured', true)->latest()->get(),
    ]);
});

Route::get('/catalog', function () {
    return Inertia::render('catalog', [
        'gears' => Gear::where('is_featured', false)->latest()->get(),
    ]);
})->name('catalog');

Route::get('/camping', function () {
    return Inertia::render('camping');
})->name('camping');

Route::get('/hiking', function () {
    return Inertia::render('hiking');
})->name('hiking');

Route::get('/climbing', function () {
    return Inertia::render('climbing');
})->name('climbing');

Route::get('/faq', function () {
    return Inertia::render('faq');
})->name('faq');

use App\Http\Controllers\BookingController;

Route::get('/booking', function () {
    return Inertia::render('booking');
})->name('booking');

Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

Route::get('/booking/success', function () {
    return Inertia::render('booking-success', [
        'product_name' => request('product_name'),
        'rental_days' => request('rental_days'),
        'quantity' => request('quantity', 1),
        'remaining_balance' => request('remaining_balance'),
        'name' => request('name'),
        'phone' => request('phone'),
        'address' => request('address'),
    ]);
})->name('booking.success');

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\GearController as AdminGearController;
use App\Http\Controllers\Staff\BookingController as StaffBookingController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/gears', [AdminGearController::class, 'index'])->name('admin.gears');
    Route::post('/gears', [AdminGearController::class, 'store'])->name('admin.gears.store');
    Route::put('/gears/{gear}', [AdminGearController::class, 'update'])->name('admin.gears.update');
    Route::delete('/gears/{gear}', [AdminGearController::class, 'destroy'])->name('admin.gears.destroy');
});

Route::middleware(['auth', 'role:staff'])->prefix('staff')->group(function () {
    Route::get('/bookings', [StaffBookingController::class, 'index'])->name('staff.bookings');
    Route::post('/bookings/{booking}/validate', [StaffBookingController::class, 'validateBooking'])->name('staff.bookings.validate');
    Route::post('/bookings/{booking}/handover', [StaffBookingController::class, 'handover'])->name('staff.bookings.handover');
    Route::post('/bookings/{booking}/return', [StaffBookingController::class, 'processReturn'])->name('staff.bookings.return');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
