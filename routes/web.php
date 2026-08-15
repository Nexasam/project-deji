<?php

use App\Http\Controllers\Owner\BusinessOnboardingController;
use App\Http\Controllers\Owner\OwnerCalendarController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\OwnerEntryController;
use App\Http\Controllers\Owner\OwnerFinanceController;
use App\Http\Controllers\Owner\OwnerOperationsController;
use App\Http\Controllers\Owner\OwnerPropertyController;
use App\Http\Controllers\Owner\PropertySetupController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/', OwnerEntryController::class)->name('entry');
    Route::get('/onboarding/business', [BusinessOnboardingController::class, 'create'])->name('onboarding.business.create');
    Route::post('/onboarding/business', [BusinessOnboardingController::class, 'store'])->name('onboarding.business.store');

    Route::middleware(['business.context', 'business.owner'])->group(function () {
        Route::get('/dashboard', OwnerDashboardController::class)->name('dashboard');
        Route::get('/properties', [OwnerPropertyController::class, 'index'])->name('properties.index');
        Route::get('/properties/create', [OwnerPropertyController::class, 'create'])->name('properties.create');
        Route::post('/properties', [OwnerPropertyController::class, 'store'])->name('properties.store');
        Route::get('/properties/{property}/setup', [OwnerPropertyController::class, 'resume'])->name('properties.resume');
        Route::get('/properties/{property}/setup/basics', [OwnerPropertyController::class, 'edit'])->name('properties.edit');
        Route::get('/properties/{property}/setup/amenities', [OwnerPropertyController::class, 'amenities'])->name('properties.setup.amenities');
        Route::put('/properties/{property}/setup/amenities', [OwnerPropertyController::class, 'updateAmenities'])->name('properties.setup.amenities.update');
        Route::get('/properties/{property}/setup/media', [OwnerPropertyController::class, 'media'])->name('properties.setup.media');
        Route::post('/properties/{property}/setup/media', [OwnerPropertyController::class, 'storeMedia'])->name('properties.setup.media.store');
        Route::delete('/properties/{property}/setup/media/{media}', [OwnerPropertyController::class, 'destroyMedia'])->name('properties.setup.media.destroy');
        Route::get('/properties/{property}/setup/house-rules', [PropertySetupController::class, 'houseRules'])->name('properties.setup.house-rules');
        Route::post('/properties/{property}/setup/house-rules', [PropertySetupController::class, 'storeHouseRules'])->name('properties.setup.house-rules.store');
        Route::get('/properties/{property}/setup/operations', [PropertySetupController::class, 'operations'])->name('properties.setup.operations');
        Route::post('/properties/{property}/setup/operations', [PropertySetupController::class, 'storeOperations'])->name('properties.setup.operations.store');
        Route::get('/properties/{property}/setup/assets', [PropertySetupController::class, 'assets'])->name('properties.setup.assets');
        Route::post('/properties/{property}/setup/assets', [PropertySetupController::class, 'storeAssets'])->name('properties.setup.assets.store');
        Route::get('/properties/{property}/setup/documents', [PropertySetupController::class, 'documents'])->name('properties.setup.documents');
        Route::post('/properties/{property}/setup/documents', [PropertySetupController::class, 'storeDocuments'])->name('properties.setup.documents.store');
        Route::get('/properties/{property}/setup/marketplace', [PropertySetupController::class, 'marketplace'])->name('properties.setup.marketplace');
        Route::post('/properties/{property}/setup/marketplace', [PropertySetupController::class, 'storeMarketplace'])->name('properties.setup.marketplace.store');
        Route::get('/properties/{property}/setup/review', [PropertySetupController::class, 'review'])->name('properties.setup.review');
        Route::post('/properties/{property}/setup/review/complete', [PropertySetupController::class, 'complete'])->name('properties.setup.review.complete');
        Route::post('/properties/{property}/marketplace-verification', [PropertySetupController::class, 'submitMarketplace'])->name('properties.marketplace-verification.submit');
        Route::post('/properties/{property}/setup/{step}/skip', [PropertySetupController::class, 'skip'])->name('properties.setup.skip');
        Route::patch('/properties/{property}', [OwnerPropertyController::class, 'update'])->name('properties.update');
        Route::get('/calendar', OwnerCalendarController::class)->name('calendar');
        Route::get('/finance', OwnerFinanceController::class)->name('finance');
        Route::get('/operations', OwnerOperationsController::class)->name('operations');

        Route::view('/property/detail', 'property-detail')->name('property.detail');
        Route::view('/property/finance', 'property-finance')->name('property.finance');
        Route::view('/properties/create/amenities', 'property-add-amenities')->name('properties.create.amenities');
        Route::view('/properties/create/documents', 'property-add-documents')->name('properties.create.documents');
        Route::view('/properties/create/marketplace', 'property-add-marketplace')->name('properties.create.marketplace');
        Route::view('/properties/create/new', 'property-add-new')->name('properties.create.new');
        Route::view('/properties/create/step1-flat', 'property-add-step1-flat')->name('properties.create.step1-flat');
        Route::view('/properties/create/step2', 'property-add-step2')->name('properties.create.step2');
        Route::view('/properties/create/step3', 'property-add-step3')->name('properties.create.step3');
        Route::view('/properties/create/step4', 'property-add-step4')->name('properties.create.step4');
        Route::view('/properties/create/step5', 'property-add-step5')->name('properties.create.step5');
        Route::view('/properties/create/step6', 'property-add-step6')->name('properties.create.step6');
        Route::view('/properties/create/step7', 'property-add-step7')->name('properties.create.step7');
        Route::view('/properties/create/step8', 'property-add-step8')->name('properties.create.step8');
        Route::view('/properties/create/step9', 'property-add-step9')->name('properties.create.step9');
        Route::view('/properties/create/success', 'property-add-success')->name('properties.create.success');
    });
});

Route::redirect('/dashboard', '/owner/dashboard');
Route::redirect('/properties', '/owner/properties');
Route::redirect('/calendar', '/owner/calendar');
Route::redirect('/property/add/step1', '/owner/properties/create');

require __DIR__.'/auth.php';
