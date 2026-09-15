<?php

use App\Http\Controllers\Admin\AdminAuditController;
use App\Http\Controllers\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminBusinessController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDisputeController;
use App\Http\Controllers\Admin\AdminPlatformSettingController;
use App\Http\Controllers\Admin\AdminPropertyController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Guest\GuestBookingCancellationController;
use App\Http\Controllers\Guest\GuestBookingController;
use App\Http\Controllers\Guest\GuestReviewController;
use App\Http\Controllers\Guest\GuestServiceRequestController;
use App\Http\Controllers\Guest\MarketplaceCheckoutController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\MarketplacePropertyController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Owner\BusinessContextController;
use App\Http\Controllers\Owner\BusinessOnboardingController;
use App\Http\Controllers\Owner\OwnerAvailabilityBlockController;
use App\Http\Controllers\Owner\OwnerBookingCancellationController;
use App\Http\Controllers\Owner\OwnerBookingDateController;
use App\Http\Controllers\Owner\OwnerBookingLifecycleController;
use App\Http\Controllers\Owner\OwnerBookingsController;
use App\Http\Controllers\Owner\OwnerCalendarController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\OwnerEntryController;
use App\Http\Controllers\Owner\OwnerExternalCalendarController;
use App\Http\Controllers\Owner\OwnerFinanceController;
use App\Http\Controllers\Owner\OwnerFinanceEntryController;
use App\Http\Controllers\Owner\OwnerGuestServiceRequestController;
use App\Http\Controllers\Owner\OwnerInspectionController;
use App\Http\Controllers\Owner\OwnerManualBookingController;
use App\Http\Controllers\Owner\OwnerOperationalTaskController;
use App\Http\Controllers\Owner\OwnerOperationsController;
use App\Http\Controllers\Owner\OwnerPropertyController;
use App\Http\Controllers\Owner\OwnerReviewResponseController;
use App\Http\Controllers\Owner\OwnerTeamController;
use App\Http\Controllers\Owner\PropertySetupController;
use App\Http\Controllers\Owner\PropertyWizardController;
use App\Http\Controllers\PropertyCalendarFeedController;
use App\Http\Controllers\Staff\StaffTaskController;
use App\Http\Controllers\TaskAttachmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', MarketplaceController::class)->name('home');
Route::get('/stays/{slug}', MarketplacePropertyController::class)->name('marketplace.show');
Route::get('/ical/{export}/{token}.ics', PropertyCalendarFeedController::class)->middleware('throttle:60,1')->name('property-calendar-feed');
Route::post('/stays/{slug}/checkout', [MarketplaceCheckoutController::class, 'store'])->middleware('auth')->name('marketplace.checkout.store');
Route::post('/stays/{slug}/quote', [MarketplaceCheckoutController::class, 'quote'])->middleware('auth')->name('marketplace.checkout.quote');
Route::middleware('auth')->prefix('guest')->name('guest.')->group(function () {
    Route::get('/bookings', [GuestBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [GuestBookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/receipt', [GuestBookingController::class, 'receipt'])->name('bookings.receipt');
    Route::post('/bookings/{booking}/cancel', GuestBookingCancellationController::class)->name('bookings.cancel');
    Route::post('/bookings/{booking}/service-requests', [GuestServiceRequestController::class, 'store'])->name('bookings.service-requests.store');
    Route::post('/bookings/{booking}/review', [GuestReviewController::class, 'store'])->name('bookings.reviews.store');
});
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'platform.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->middleware('platform.permission:platform.dashboard.view')->name('dashboard');
    Route::get('/businesses', [AdminBusinessController::class, 'index'])->middleware('platform.permission:platform.business.view')->name('businesses.index');
    Route::get('/businesses/{business}', [AdminBusinessController::class, 'show'])->middleware('platform.permission:platform.business.view')->name('businesses.show');
    Route::post('/businesses/{business}/verify', [AdminBusinessController::class, 'verify'])->middleware('platform.permission:platform.business.verify')->name('businesses.verify');
    Route::post('/businesses/{business}/reject', [AdminBusinessController::class, 'reject'])->middleware('platform.permission:platform.business.verify')->name('businesses.reject');
    Route::post('/businesses/{business}/suspend', [AdminBusinessController::class, 'suspend'])->middleware('platform.permission:platform.business.manage')->name('businesses.suspend');
    Route::post('/businesses/{business}/reactivate', [AdminBusinessController::class, 'reactivate'])->middleware('platform.permission:platform.business.manage')->name('businesses.reactivate');
    Route::get('/users', [AdminUserController::class, 'index'])->middleware('platform.permission:platform.user.view')->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->middleware('platform.permission:platform.user.view')->name('users.show');
    Route::post('/users/{user}/lock', [AdminUserController::class, 'lock'])->middleware('platform.permission:platform.user.lock')->name('users.lock');
    Route::post('/users/{user}/unlock', [AdminUserController::class, 'unlock'])->middleware('platform.permission:platform.user.lock')->name('users.unlock');
    Route::get('/reviews', [AdminReviewController::class, 'index'])->middleware('platform.permission:platform.review.view')->name('reviews.index');
    Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->middleware('platform.permission:platform.review.view')->name('reviews.show');
    Route::post('/reviews/{review}/hide', [AdminReviewController::class, 'hide'])->middleware('platform.permission:platform.review.moderate')->name('reviews.hide');
    Route::post('/reviews/{review}/restore', [AdminReviewController::class, 'restore'])->middleware('platform.permission:platform.review.moderate')->name('reviews.restore');
    Route::post('/review-responses/{response}/hide', [AdminReviewController::class, 'hideResponse'])->middleware('platform.permission:platform.review.moderate')->name('review-responses.hide');
    Route::post('/review-responses/{response}/restore', [AdminReviewController::class, 'restoreResponse'])->middleware('platform.permission:platform.review.moderate')->name('review-responses.restore');
    Route::get('/disputes', [AdminDisputeController::class, 'index'])->middleware('platform.permission:platform.dispute.view')->name('disputes.index');
    Route::get('/disputes/create', [AdminDisputeController::class, 'create'])->middleware('platform.permission:platform.dispute.manage')->name('disputes.create');
    Route::post('/disputes', [AdminDisputeController::class, 'store'])->middleware('platform.permission:platform.dispute.manage')->name('disputes.store');
    Route::get('/disputes/{dispute}', [AdminDisputeController::class, 'show'])->middleware('platform.permission:platform.dispute.view')->name('disputes.show');
    Route::patch('/disputes/{dispute}/assignment', [AdminDisputeController::class, 'assign'])->middleware('platform.permission:platform.dispute.manage')->name('disputes.assign');
    Route::patch('/disputes/{dispute}/transition', [AdminDisputeController::class, 'transition'])->middleware('platform.permission:platform.dispute.manage')->name('disputes.transition');
    Route::get('/settings', [AdminPlatformSettingController::class, 'index'])->middleware('platform.permission:platform.configure')->name('settings.index');
    Route::patch('/settings', [AdminPlatformSettingController::class, 'update'])->middleware('platform.permission:platform.configure')->name('settings.update');
    Route::get('/audit', AdminAuditController::class)->middleware('platform.permission:platform.audit.view')->name('audit.index');
    Route::get('/properties', [AdminPropertyController::class, 'index'])->middleware('platform.permission:platform.property.view')->name('properties.index');
    Route::get('/properties/{property}', [AdminPropertyController::class, 'show'])->middleware('platform.permission:platform.property.view')->name('properties.show');
    Route::post('/properties/{property}/publish', [AdminPropertyController::class, 'publish'])->middleware('platform.permission:platform.property.verify')->name('properties.publish');
    Route::post('/properties/{property}/unpublish', [AdminPropertyController::class, 'unpublish'])->middleware('platform.permission:platform.property.verify')->name('properties.unpublish');
    Route::post('/properties/{property}/reject', [AdminPropertyController::class, 'reject'])->middleware('platform.permission:platform.property.verify')->name('properties.reject');
});

Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('/', OwnerEntryController::class)->name('entry');
    Route::get('/onboarding/business', [BusinessOnboardingController::class, 'create'])->name('onboarding.business.create');
    Route::post('/onboarding/business', [BusinessOnboardingController::class, 'store'])->name('onboarding.business.store');
    Route::patch('/business-context', [BusinessContextController::class, 'update'])->middleware('auth')->name('business-context.update');

    Route::middleware(['auth', 'business.context'])->group(function () {
        Route::get('/dashboard', OwnerDashboardController::class)->middleware('permission:business.view')->name('dashboard');
        Route::get('/properties', [OwnerPropertyController::class, 'index'])->middleware('permission:property.view')->name('properties.index');
        Route::get('/properties/create', fn () => redirect('/owner/properties/create/step1'))->middleware('permission:property.create')->name('properties.create');
        Route::get('/properties/create/step1', [PropertyWizardController::class, 'entry'])->middleware('permission:property.create')->name('properties.create.step1');
        Route::post('/properties/create/step1', [PropertyWizardController::class, 'start'])->middleware(['permission:property.create', 'business.mutation'])->name('properties.wizard.start');
        Route::get('/properties/create/wizard', fn () => redirect()->route('owner.properties.create.step1'))->middleware('permission:property.create')->name('properties.create.wizard');
        Route::get('/properties/{property}/setup', [PropertyWizardController::class, 'resume'])->middleware('permission:property.edit')->name('properties.resume');
        Route::get('/properties/{property}/create/step{step}', [PropertyWizardController::class, 'show'])->whereNumber('step')->middleware('permission:property.edit')->name('properties.wizard.step');
        Route::post('/properties/{property}/create/step{step}', [PropertyWizardController::class, 'store'])->whereNumber('step')->middleware(['permission:property.edit', 'business.mutation'])->name('properties.wizard.store');
        Route::post('/properties/{property}/create/step{step}/skip', [PropertyWizardController::class, 'skip'])->whereNumber('step')->middleware(['permission:property.edit', 'business.mutation'])->name('properties.wizard.skip');
        Route::delete('/properties/{property}/create/media/{media}', [PropertyWizardController::class, 'destroyMedia'])->middleware(['permission:property.edit', 'business.mutation'])->name('properties.wizard.media.destroy');
        Route::delete('/properties/{property}/create/documents/{document}', [PropertyWizardController::class, 'destroyDocument'])->middleware(['permission:property.manage_documents', 'business.mutation'])->name('properties.wizard.documents.destroy');
        Route::get('/properties/{property}/create/documents/{document}/preview', [PropertyWizardController::class, 'previewDocument'])->middleware('permission:document.download')->name('properties.wizard.documents.preview');
        Route::post('/properties/{property}/create/submit', [PropertyWizardController::class, 'submit'])->middleware(['permission:property.manage_listing', 'business.mutation'])->name('properties.wizard.submit');
        Route::get('/properties/{property}/create/success', [PropertyWizardController::class, 'success'])->middleware('permission:property.view')->name('properties.wizard.success');
        Route::post('/properties/{property}/marketplace-verification', [PropertySetupController::class, 'submitMarketplace'])->middleware(['permission:property.manage_listing', 'business.mutation'])->name('properties.marketplace-verification.submit');
        Route::get('/properties/{property}', [OwnerPropertyController::class, 'show'])->middleware('permission:property.view')->name('properties.show');
        Route::post('/properties/{property}/calendars', [OwnerExternalCalendarController::class, 'store'])->middleware(['permission:calendar.manage_connections', 'business.mutation'])->name('properties.calendars.store');
        Route::post('/properties/{property}/calendars/{connection}/sync', [OwnerExternalCalendarController::class, 'sync'])->middleware(['permission:calendar.sync', 'business.mutation'])->name('properties.calendars.sync');
        Route::delete('/properties/{property}/calendars/{connection}', [OwnerExternalCalendarController::class, 'destroy'])->middleware(['permission:calendar.manage_connections', 'business.mutation'])->name('properties.calendars.destroy');
        Route::post('/properties/{property}/calendar-export/regenerate', [OwnerExternalCalendarController::class, 'regenerate'])->middleware(['permission:calendar.manage_connections', 'business.mutation'])->name('properties.calendars.export.regenerate');
        Route::patch('/properties/{property}', [OwnerPropertyController::class, 'update'])->middleware(['permission:property.edit', 'business.mutation'])->name('properties.update');
        Route::get('/bookings', OwnerBookingsController::class)->middleware('permission:booking.view_history')->name('bookings');
        Route::get('/bookings/create/manual', [OwnerManualBookingController::class, 'create'])->middleware('permission:booking.create')->name('bookings.manual.create');
        Route::post('/bookings/create/manual', [OwnerManualBookingController::class, 'store'])->middleware(['permission:booking.create', 'business.mutation'])->name('bookings.manual.store');
        Route::get('/bookings/{booking}', [OwnerBookingsController::class, 'show'])->middleware('permission:booking.view_history')->name('bookings.show');
        Route::patch('/bookings/{booking}/dates', [OwnerBookingDateController::class, 'update'])->middleware('permission:booking.modify')->name('bookings.dates.update');
        Route::post('/bookings/{booking}/check-in', [OwnerBookingLifecycleController::class, 'checkIn'])->middleware('permission:booking.check_in')->name('bookings.check-in');
        Route::post('/bookings/{booking}/check-out', [OwnerBookingLifecycleController::class, 'checkOut'])->middleware('permission:booking.check_out')->name('bookings.check-out');
        Route::post('/bookings/{booking}/no-show', [OwnerBookingLifecycleController::class, 'noShow'])->middleware('permission:booking.modify')->name('bookings.no-show');
        Route::post('/bookings/{booking}/cancel', OwnerBookingCancellationController::class)->middleware('permission:booking.cancel')->name('bookings.cancel');
        Route::get('/calendar', OwnerCalendarController::class)->middleware('permission:calendar.view')->name('calendar');
        Route::post('/calendar/blocks', [OwnerAvailabilityBlockController::class, 'store'])->middleware(['permission:calendar.manage_blocks', 'business.mutation'])->name('calendar.blocks.store');
        Route::delete('/calendar/blocks/{block}', [OwnerAvailabilityBlockController::class, 'destroy'])->middleware(['permission:calendar.manage_blocks', 'business.mutation'])->name('calendar.blocks.destroy');
        Route::get('/finance', OwnerFinanceController::class)->middleware('permission:finance.view_transactions')->name('finance');
        Route::post('/finance/payments', [OwnerFinanceEntryController::class, 'payment'])->middleware(['permission:payment.record', 'business.mutation'])->name('finance.payments.store');
        Route::post('/finance/expenses', [OwnerFinanceEntryController::class, 'expense'])->middleware(['permission:expense.create', 'business.mutation'])->name('finance.expenses.store');
        Route::get('/operations', OwnerOperationsController::class)->middleware('permission:task.view_assigned')->name('operations');
        Route::post('/operations/tasks', [OwnerOperationalTaskController::class, 'store'])->middleware(['permission:task.assign', 'business.mutation'])->name('operations.tasks.store');
        Route::patch('/operations/tasks/{task}/transition', [OwnerOperationalTaskController::class, 'transition'])->middleware('permission:task.complete')->name('operations.tasks.transition');
        Route::patch('/operations/tasks/{task}/assign', [OwnerOperationalTaskController::class, 'assign'])->middleware(['permission:task.reassign', 'business.mutation'])->name('operations.tasks.assign');
        Route::patch('/operations/tasks/{task}/schedule', [OwnerOperationalTaskController::class, 'schedule'])->middleware(['permission:task.assign', 'business.mutation'])->name('operations.tasks.schedule');
        Route::post('/operations/tasks/{task}/inspection', OwnerInspectionController::class)->middleware('permission:inspection.perform')->name('operations.tasks.inspection');
        Route::patch('/service-requests/{serviceRequest}', [OwnerGuestServiceRequestController::class, 'update'])->middleware('permission:booking.communicate')->name('service-requests.update');
        Route::post('/reviews/{review}/response', [OwnerReviewResponseController::class, 'store'])->middleware('permission:property.respond_reviews')->name('reviews.responses.store');
        Route::get('/team', [OwnerTeamController::class, 'index'])->middleware('permission:employee.invite')->name('team.index');
        Route::post('/team', [OwnerTeamController::class, 'store'])->middleware(['permission:employee.invite', 'business.mutation'])->name('team.store');
        Route::patch('/team/{membership}', [OwnerTeamController::class, 'update'])->middleware(['permission:employee.change_role', 'business.mutation'])->name('team.update');
        Route::delete('/team/{membership}', [OwnerTeamController::class, 'deactivate'])->middleware(['permission:employee.remove', 'business.mutation'])->name('team.deactivate');

        Route::view('/property/detail', 'property-detail')->middleware('permission:property.view')->name('property.detail');
        Route::view('/property/finance', 'property-finance')->middleware('permission:property.view_finance')->name('property.finance');
    });
});

Route::middleware(['auth', 'business.context'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/tasks', [StaffTaskController::class, 'index'])->middleware('permission:task.view_assigned')->name('tasks.index');
    Route::patch('/tasks/{task}', [StaffTaskController::class, 'transition'])->middleware('permission:task.complete')->name('tasks.transition');
    Route::post('/tasks/{task}/inspection', [StaffTaskController::class, 'inspection'])->middleware('permission:inspection.perform')->name('tasks.inspection');
});
Route::get('/task-attachments/{task}/{attachment}', TaskAttachmentController::class)
    ->middleware(['auth', 'business.context', 'permission:document.download'])->name('task-attachments.show');

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
