<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ChristmasBoxController; // added by GIAN
use App\Http\Controllers\Admin\TupadController; // added by GIAN
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

    Route::get('/residents/{residentId}/demographics', [ResidentController::class, 'getDemographics']); // added by cath
    Route::get('/residents/search', [ResidentController::class, 'search'])->name('residents.search');

    Route::get('/residents/{resident}', [ResidentController::class, 'show'])->name('residents.show');  // added by gian, fixed route conflict by cath

    Route::get('/transaction', [DocumentController::class, 'document'])->name('transaction.document');
    Route::get('/document-requests/{requestId}', [DocumentController::class, 'show']); // added by cath
    Route::post('/document-request/store', [DocumentController::class, 'store'])->name('document-request.store');
    Route::put('/document-requests/{requestId}/update', [DocumentController::class, 'update'])->name('document.update'); // added by cath
    Route::post('/document/sign', [DocumentController::class, 'transferToSignature'])->name('document.sign'); // added and changed by cath
    Route::post('/document/transfer-for-release', [DocumentController::class, 'transferToRelease'])->name('document.transfer-for-release'); // added and changed by cath
    Route::post('/document/transfer-for-signature', [DocumentController::class, 'transferToSignature'])->name('document.transfer-for-signature'); // added and changed by cath
    Route::post('/document/release', [DocumentController::class, 'release'])->name('document.release'); // added and changed by cath
    Route::post('/document/cancel', [DocumentController::class, 'cancel'])->name('document.cancel'); // added and changed by cath
    Route::get('/transaction/facility', [FacilityController::class, 'index'])->name('transaction.facility');
    Route::post('/transaction/facility/reservation', [FacilityController::class, 'storeReservation'])->name('facility.storeReservation');
    Route::post('/facility/reservation/store', [FacilityController::class, 'storeReservation'])->name('facility.reservation.store');

    // --- ADDED: Profile Settings Routes ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // --------------------------------------

    



    Route::middleware(['auth', 'role:admin|super admin'])->prefix('admin')->name('admin.')->group(function () {

    
        Route::resource('users', UserController::class);
        Route::get('/logs', [LogController::class, 'index'])->name('users.logs');

        Route::get('/christmas', [ChristmasBoxController::class, 'index'])->name('christmas.index'); // added by GIAN
        Route::post('/christmas/{household}/release', [ChristmasBoxController::class, 'release'])->name('christmas.release');
        Route::post('/christmas/{household}/revert', [ChristmasBoxController::class, 'revert'])->name('christmas.revert');

        Route::get('/tupad', [TupadController::class, 'index'])->name('tupad.index'); // added by GIAN
        Route::post('/admin/tupad/employ', [TupadController::class, 'employ'])->name('tupad.employ'); // added by GIAN
        Route::post('/admin/tupad/drop', [TupadController::class, 'drop'])->name('tupad.drop'); // added by GIAN
        Route::put('/admin/tupad/update', [TupadController::class, 'update'])->name('tupad.update'); // added by GIAN
    });

    Route::middleware('auth')->group(function () { // Added by gian, ensures only authenticated users can access ticket routes

        Route::resource('tickets', TicketController::class);

        Route::post('/tickets/{ticket}/start', [TicketController::class, 'start'])->name('tickets.start');
        Route::post('/tickets/{ticket}/resolve', [TicketController::class, 'resolve'])->name('tickets.resolve');
        Route::post('/tickets/{ticket}/cancel', [TicketController::class, 'cancel'])->name('tickets.cancel');
        Route::post('/tickets/{ticket}/mark-seen', [TicketController::class, 'markAsSeen'])->name('tickets.mark-seen');
    });

});

require __DIR__.'/auth.php';
