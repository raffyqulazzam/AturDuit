<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\SavingsGoalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test', function () {
    return 'App is working!';
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Logout route
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Demo login route (for testing purposes)
Route::get('/demo-login', function () {
    $user = User::where('email', 'admin@gmail.com')->first();
    if ($user) {
        Auth::login($user);
        return redirect('/dashboard');
    }
    return 'User not found. Please run php artisan db:setup first.';
})->name('demo-login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/quick-stats', [DashboardController::class, 'getQuickStats'])->name('api.quick-stats');
    
    // Resource routes
    Route::resource('transactions', TransactionController::class);
    Route::resource('accounts', AccountController::class);
    // Account Types
    Route::resource('account-types', AccountTypeController::class)
        ->scoped(['account-types' => 'user_id']);
    Route::resource('categories', CategoryController::class);
    Route::resource('budgets', BudgetController::class);
    Route::resource('savings-goals', SavingsGoalController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('settings', SettingController::class);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // Additional routes for savings goals
    Route::post('/savings-goals/{savingsGoal}/add-saving', [SavingsGoalController::class, 'addSaving'])->name('savings-goals.add-saving');
    
    // Settings specific routes
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.password');
    Route::put('/settings/preferences', [SettingController::class, 'updatePreferences'])->name('settings.preferences');
    Route::put('/settings/notifications', [SettingController::class, 'updateNotifications'])->name('settings.notifications');
    Route::get('/settings/export', [SettingController::class, 'exportData'])->name('settings.export');
    Route::delete('/settings/reset', [SettingController::class, 'resetData'])->name('settings.reset');
});

// API Routes for Charts
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('/reports/monthly', [App\Http\Controllers\Api\ReportController::class, 'monthlyChart']);
    Route::get('/reports/category', [App\Http\Controllers\Api\ReportController::class, 'categoryChart']);
    Route::get('/reports/weekly', [App\Http\Controllers\Api\ReportController::class, 'weeklyChart']);
    Route::get('/reports/daily', [App\Http\Controllers\Api\ReportController::class, 'dailyChart']);
    Route::get('/reports/summary', [App\Http\Controllers\Api\ReportController::class, 'summary']);
    Route::get('/ai/insights', [App\Http\Controllers\Api\AIInsightsController::class, 'getSpendingInsights']);
});
