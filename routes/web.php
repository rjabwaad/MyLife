<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\EmotionController;
use App\Http\Controllers\FinanceController;
// صفحة تسجيل الدخول
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Google Login
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])
    ->name('google.login');

// Google Callback
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
    ->name('google.callback');

// كل الروتس المحمية بعد تسجيل الدخول
Route::middleware(['auth'])->group(function () {

    // الصفحة الرئيسية + الكالندري
    Route::get('/home', [CalendarEventController::class, 'index'])->name('home');

    // الصفحة الرئيسية مع الطقس
    Route::get('/weather-view', [WeatherController::class, 'showWithCalendar'])->name('weather.view');

    // عمليات CRUD الخاصة بالـ Calendar Events
    Route::prefix('calendar')->group(function () {
        Route::post('/events', [CalendarEventController::class, 'store']);
        Route::put('/events/{id}', [CalendarEventController::class, 'update']);
        Route::delete('/events/{id}', [CalendarEventController::class, 'destroy']);
    });

    // API الطقس (JSON)
    Route::get('/weather', [WeatherController::class, 'index']);

    // API أوقات الصلاة
    Route::get('/prayer-times', [\App\Http\Controllers\prayerController::class, 'getPrayerTimes']);

    // API تحليل المشاعر
    Route::post('/emotion/analyze', [EmotionController::class, 'analyze']);

    // Finance Tracker Routes
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');

    // Income routes
    Route::post('/finance/income', [FinanceController::class, 'storeIncome'])->name('finance.income.store');
    Route::delete('/finance/income/{id}', [FinanceController::class, 'deleteIncome'])->name('finance.income.delete');

    // Expense routes
    Route::post('/finance/expense', [FinanceController::class, 'storeExpense'])->name('finance.expense.store');
    Route::delete('/finance/expense/{id}', [FinanceController::class, 'deleteExpense'])->name('finance.expense.delete');

    // Debt routes
    Route::post('/finance/debt', [FinanceController::class, 'storeDebt'])->name('finance.debt.store');
    Route::put('/finance/debt/{id}', [FinanceController::class, 'updateDebt'])->name('finance.debt.update');
    Route::delete('/finance/debt/{id}', [FinanceController::class, 'deleteDebt'])->name('finance.debt.delete');

    // Wishlist routes
    Route::post('/finance/wishlist', [FinanceController::class, 'storeWishlist'])->name('finance.wishlist.store');
    Route::put('/finance/wishlist/{id}/purchased', [FinanceController::class, 'markPurchased'])->name('finance.wishlist.purchased');
    Route::delete('/finance/wishlist/{id}', [FinanceController::class, 'deleteWishlist'])->name('finance.wishlist.delete');

});