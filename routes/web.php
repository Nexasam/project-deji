<?php

use App\Http\Controllers\Owner\BusinessOnboardingController;
use App\Http\Controllers\Owner\OwnerBookingsController;
use App\Http\Controllers\Owner\OwnerCalendarController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\OwnerEntryController;
use App\Http\Controllers\Owner\OwnerFinanceController;
use App\Http\Controllers\Owner\OwnerOperationsController;
use App\Http\Controllers\Owner\OwnerPropertyController;
use App\Http\Controllers\Owner\PropertySetupController;
use App\Http\Controllers\Owner\PropertyWizardController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MarketplacePropertyController;
use App\Http\Controllers\Guest\GuestBookingController;
use App\Http\Controllers\Guest\MarketplaceCheckoutController;
use App\Http\Controllers\Guest\GuestBookingCancellationController;
use Illuminate\Support\Facades\Route;

Route::get('/', MarketplaceController::class)->name('home');
Route::get('/stays/{slug}', MarketplacePropertyController::class)->name('marketplace.show');
Route::post('/stays/{slug}/checkout', [MarketplaceCheckoutController::class, 'store'])->middleware('auth')->name('marketplace.checkout.store');
Route::middleware('auth')->prefix('guest')->name('guest.')->group(function () {
    Route::get('/bookings', [GuestBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [GuestBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', GuestBookingCancellationController::class)->name('bookings.cancel');
});

Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('/', OwnerEntryController::class)->name('entry');
    Route::get('/onboarding/business', [BusinessOnboardingController::class, 'create'])->name('onboarding.business.create');
    Route::post('/onboarding/business', [BusinessOnboardingController::class, 'store'])->name('onboarding.business.store');

    Route::middleware(['auth', 'business.context', 'business.owner'])->group(function () {
        Route::get('/dashboard', OwnerDashboardController::class)->name('dashboard');
        Route::get('/properties', [OwnerPropertyController::class, 'index'])->name('properties.index');
        Route::get('/properties/create', fn () => redirect('/owner/properties/create/step1'))->name('properties.create');
        Route::get('/properties/create/step1', [PropertyWizardController::class, 'entry'])->name('properties.create.step1');
        Route::post('/properties/create/step1', [PropertyWizardController::class, 'start'])->name('properties.wizard.start');
        Route::get('/properties/create/wizard', fn () => redirect()->route('owner.properties.create.step1'))->name('properties.create.wizard');
        Route::get('/properties/{property}/setup', [PropertyWizardController::class, 'resume'])->name('properties.resume');
        Route::get('/properties/{property}/create/step{step}', [PropertyWizardController::class, 'show'])->whereNumber('step')->name('properties.wizard.step');
        Route::post('/properties/{property}/create/step{step}', [PropertyWizardController::class, 'store'])->whereNumber('step')->name('properties.wizard.store');
        Route::post('/properties/{property}/create/step{step}/skip', [PropertyWizardController::class, 'skip'])->whereNumber('step')->name('properties.wizard.skip');
        Route::delete('/properties/{property}/create/media/{media}', [PropertyWizardController::class, 'destroyMedia'])->name('properties.wizard.media.destroy');
        Route::delete('/properties/{property}/create/documents/{document}', [PropertyWizardController::class, 'destroyDocument'])->name('properties.wizard.documents.destroy');
        Route::post('/properties/{property}/create/submit', [PropertyWizardController::class, 'submit'])->name('properties.wizard.submit');
        Route::get('/properties/{property}/create/success', [PropertyWizardController::class, 'success'])->name('properties.wizard.success');
        Route::post('/properties/{property}/marketplace-verification', [PropertySetupController::class, 'submitMarketplace'])->name('properties.marketplace-verification.submit');
        Route::patch('/properties/{property}', [OwnerPropertyController::class, 'update'])->name('properties.update');
        Route::get('/bookings', OwnerBookingsController::class)->name('bookings');
        Route::get('/calendar', OwnerCalendarController::class)->name('calendar');
        Route::get('/finance', OwnerFinanceController::class)->name('finance');
        Route::get('/operations', OwnerOperationsController::class)->name('operations');

        Route::view('/property/detail', 'property-detail')->name('property.detail');
        Route::view('/property/finance', 'property-finance')->name('property.finance');
    });
});

Route::redirect('/dashboard', '/owner/dashboard');
Route::redirect('/properties', '/owner/properties');
Route::redirect('/calendar', '/owner/calendar');
Route::redirect('/property/add/step1', '/owner/properties/create/step1');
Route::redirect('/property2', '/owner/properties/create');

// Retained URLs now converge on the active full-page wizard.
Route::prefix('owner/preview')->name('owner.preview.')->group(function () {
    Route::redirect('/property-wizard', '/owner/properties/create/step1')->name('property-wizard');
    Route::redirect('/property-amenities', '/owner/properties/create/step1')->name('property-amenities');
    Route::redirect('/property-documents', '/owner/properties/create/step1')->name('property-documents');
    Route::redirect('/property-marketplace', '/owner/properties/create/step1')->name('property-marketplace');
});

require __DIR__.'/auth.php';
