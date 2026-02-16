<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Public\VenueSearch;
use App\Livewire\Public\VenueDetail;
use App\Livewire\Courts\CourtScheduleCinema;

Route::get('/', VenueSearch::class)->name('home');
Route::get('/venues/{venue}', VenueDetail::class)->name('public.venues.show');

// Court Schedule (Cinema Style)
Route::get('/venues/{venue:slug}/court/{venueCourt}', CourtScheduleCinema::class)->name('courts.schedule');

// Checkout
Route::get('/checkout/review-order', \App\Livewire\Checkout\ReviewOrder::class)->name('checkout.review');

// Auth (modal popup)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', fn() => redirect('/')->with('openAuth', 'login'))->name('login');
    Route::get('/register', fn() => redirect('/')->with('openAuth', 'register'))->name('register');
});

// Bookings (Auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/booking/{booking}', \App\Livewire\Bookings\BookingShow::class)->name('bookings.show');
    Route::get('/booking/{booking}/checkout', \App\Livewire\Bookings\BookingCheckout::class)->name('bookings.checkout');
    Route::get('/checkout/payment/{booking}', \App\Livewire\Bookings\BookingCheckout::class)->name('checkout.payment');
    
    Route::get('/payments/{payment}', \App\Livewire\Payments\PaymentShow::class)->name('payments.show');
});

// Member Area
Route::middleware(['auth'])->prefix('member')->name('member.')->group(function () {
    Route::get('/', \App\Livewire\Member\Dashboard::class)->name('dashboard');
    Route::get('/bookings', \App\Livewire\Member\BookingHistory::class)->name('bookings');
    Route::get('/wallet', \App\Livewire\Member\WalletIndex::class)->name('wallet');
    Route::get('/wallet/withdraw', \App\Livewire\Member\WalletWithdraw::class)->name('wallet.withdraw');
    Route::get('/notifications', \App\Livewire\Member\NotificationIndex::class)->name('notifications');
    Route::get('/profile', \App\Livewire\Member\Profile::class)->name('profile');
});

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Livewire\Admin\Dashboard\AdminDashboard::class)->name('dashboard');
    
    // Venues
    Route::get('/venues', \App\Livewire\Admin\Venues\VenueIndexAdmin::class)->name('venues.index');
    Route::get('/venues/{venue}/hub', \App\Livewire\Admin\Venues\VenueHubAdmin::class)->name('venues.hub');
    Route::get('/venues/create', \App\Livewire\Admin\Venues\VenueFormAdmin::class)->name('venues.create');
    Route::get('/venues/{venue}/edit', \App\Livewire\Admin\Venues\VenueFormAdmin::class)->name('venues.edit');
    Route::get('/venues/{venue}/media', \App\Livewire\Admin\Venues\VenueMediaManage::class)->name('venues.media');
    Route::get('/venues/{venue}/operating-hours', \App\Livewire\Admin\Venues\VenueOperatingHoursManageAdmin::class)->name('venues.operating-hours');
    Route::get('/venues/{venue}/blackouts', \App\Livewire\Admin\Venues\VenueBlackoutManageAdmin::class)->name('venues.blackouts');
    Route::get('/venues/{venue}/amenities', \App\Livewire\Admin\Venues\VenueAmenitiesManage::class)->name('venues.amenities');
    Route::get('/venues/{venue}/policies', \App\Livewire\Admin\Venues\VenuePolicyManageAdmin::class)->name('venues.policies');
    Route::get('/venues/{venue}/courts', \App\Livewire\Admin\Courts\CourtManageAdmin::class)->name('venues.courts');
    Route::get('/courts/{court}/pricing', \App\Livewire\Admin\Courts\CourtPricingManageAdmin::class)->name('courts.pricing');
    Route::get('/courts/{court}/blackouts', \App\Livewire\Admin\Courts\CourtBlackoutManageAdmin::class)->name('courts.blackouts');

    // Vouchers
    Route::get('/vouchers', \App\Livewire\Admin\Vouchers\VoucherIndexAdmin::class)->name('vouchers.index');

    // Bookings
    Route::get('/bookings', \App\Livewire\Admin\Bookings\BookingIndexAdmin::class)->name('bookings.index');
    Route::get('/bookings/{booking}', \App\Livewire\Admin\Bookings\BookingDetailAdmin::class)->name('bookings.show');

    // Finance
    Route::get('/refunds', \App\Livewire\Admin\Refunds\RefundIndexAdmin::class)->name('refunds.index');
    Route::get('/withdraws', \App\Livewire\Admin\Withdraws\WithdrawIndexAdmin::class)->name('withdraws.index');
    Route::get('/settlements', \App\Livewire\Admin\Settlements\SettlementIndexAdmin::class)->name('settlements.index');

    // Reports
    Route::get('/reports/financial', \App\Livewire\Admin\Reports\FinancialReport::class)->name('reports.financial');

    // Reviews
    Route::get('/reviews', \App\Livewire\Admin\Reviews\ReviewIndexAdmin::class)->name('reviews.index');

    // System
    Route::get('/system/users', \App\Livewire\Admin\System\UserIndexAdmin::class)->name('system.users');
    Route::get('/system/audit-logs', \App\Livewire\Admin\System\AuditLogIndex::class)->name('system.audit-logs');

    // Pages (CMS)
    Route::get('/pages', \App\Livewire\Admin\Pages\PageIndex::class)->name('pages.index');
    Route::get('/pages/create', \App\Livewire\Admin\Pages\PageForm::class)->name('pages.create');
    Route::get('/pages/{page}/edit', \App\Livewire\Admin\Pages\PageForm::class)->name('pages.edit');
});

Route::post('/midtrans/notification', \App\Http\Controllers\Midtrans\MidtransNotificationController::class);

// Static Pages (Must be last)
Route::get('/{page:slug}', \App\Livewire\Public\PageDisplay::class)->name('public.page');
