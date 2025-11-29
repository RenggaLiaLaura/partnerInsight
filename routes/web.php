<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\SatisfactionController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ClusteringController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/global-search', [App\Http\Controllers\GlobalSearchController::class, 'search'])->name('global.search');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Notification Routes
    Route::get('/notifications/unread', [App\Http\Controllers\NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');

    // Settings Routes
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password');



    // Manager & Admin Routes - Read-only access to distributors
    Route::middleware(['role:manager,admin'])->group(function () {
        Route::get('/distributors', [DistributorController::class, 'index'])->name('distributors.index');
        Route::get('/distributors/{distributor}', [DistributorController::class, 'show'])->name('distributors.show');
        
        // Import/Export routes (Manager & Admin)
        Route::get('/distributors/import', [DistributorController::class, 'showImportForm'])->name('distributors.import');
        Route::post('/distributors/import', [DistributorController::class, 'import'])->name('distributors.import.process');
        Route::get('/distributors/template', [DistributorController::class, 'downloadTemplate'])->name('distributors.template');
        Route::get('/export-all-data', [DistributorController::class, 'exportAll'])->name('export.all');
        
        // Clustering routes
        Route::get('/clustering', [ClusteringController::class, 'index'])->name('clustering.index');
        Route::post('/clustering/run', [ClusteringController::class, 'run'])->name('clustering.run');
        Route::get('/clustering/export', [ClusteringController::class, 'export'])->name('clustering.export');
    });

    // Admin Routes - Full CRUD access
    Route::middleware(['role:admin'])->group(function () {
        // Distributor management (create, edit, delete)
        Route::get('/distributors/create', [DistributorController::class, 'create'])->name('distributors.create');
        Route::post('/distributors', [DistributorController::class, 'store'])->name('distributors.store');
        Route::get('/distributors/{distributor}/edit', [DistributorController::class, 'edit'])->name('distributors.edit');
        Route::put('/distributors/{distributor}', [DistributorController::class, 'update'])->name('distributors.update');
        Route::delete('/distributors/{distributor}', [DistributorController::class, 'destroy'])->name('distributors.destroy');
        
        // Satisfaction and Sales management
        Route::resource('satisfaction', SatisfactionController::class);
        Route::resource('sales', SalesController::class);
        
        // Audit Logs
        Route::get('/audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{id}', [App\Http\Controllers\AuditLogController::class, 'show'])->name('audit-logs.show');
    });
});
