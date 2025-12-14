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
    Route::get('/residents/search', [ResidentController::class, 'search'])->name('residents.search');

    Route::get('/transaction', [DocumentController::class, 'document'])->name('transaction.document');
    Route::post('/document-request/store', [DocumentController::class, 'store'])->name('document-request.store');
    Route::post('/document/sign', [DocumentController::class, 'sign'])->name('document.sign');
    Route::post('/document/release', [DocumentController::class, 'release'])->name('document.release');
    Route::post('/document/cancel', [DocumentController::class, 'cancel'])->name('document.cancel');
    Route::get('/transaction/facility', [FacilityController::class, 'index'])->name('transaction.facility');
    Route::post('/transaction/facility/reservation', [FacilityController::class, 'storeReservation'])->name('facility.storeReservation');
    Route::post('/facility/reservation/store', [FacilityController::class, 'storeReservation'])->name('facility.reservation.store');

    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Creates index, create, store, edit, update, destroy routes automatically.
        Route::resource('users', UserController::class); 

    // Other Admin routes (Logs, Events, etc.) would go here later
    });
      

}); 

require __DIR__.'/auth.php';
