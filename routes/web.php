<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login'); // Redirects to the login page
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/residents', [ResidentController::class, 'index'])->name('residents.index');

    //GIAN ADDED THIS
    Route::post('/residents', [ResidentController::class, 'store'])->name('residents.store');
    Route::put('/households/{household}', [ResidentController::class, 'updateHousehold'])->name('households.update'); // added by gian
    Route::put('/residents/{resident}', [ResidentController::class, 'update'])->name('residents.update'); // added by gian

    Route::get('/residents/search', [ResidentController::class, 'search'])->name('residents.search');

    Route::get('/residents/{resident}', [ResidentController::class, 'show'])->name('residents.show');  // added by gian, fixed route conflict by cath

    Route::get('/transaction', [DocumentController::class, 'document'])->name('transaction.document');
    Route::post('/document-request/store', [DocumentController::class, 'store'])->name('document-request.store');
    Route::post('/document/sign', [DocumentController::class, 'sign'])->name('document.sign');
    Route::post('/document/release', [DocumentController::class, 'release'])->name('document.release');
    Route::post('/document/cancel', [DocumentController::class, 'cancel'])->name('document.cancel');
    Route::get('/transaction/facility', [FacilityController::class, 'index'])->name('transaction.facility');
    Route::post('/transaction/facility/reservation', [FacilityController::class, 'storeReservation'])->name('facility.storeReservation');
    Route::post('/facility/reservation/store', [FacilityController::class, 'storeReservation'])->name('facility.reservation.store');

    // --- ADDED: Profile Settings Routes ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // --------------------------------------

    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Creates index, create, store, edit, update, destroy routes automatically.
        Route::resource('users', UserController::class);

    // Other Admin routes (Logs, Events, etc.) would go here later
    });


});

require __DIR__.'/auth.php';
