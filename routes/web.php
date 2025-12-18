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



    // ---------------------------------------------------------------------
    // 1. DATA MODIFICATION & IMPORT (Admin & Staff)
    //    - Distributor: CRUD + Import
    //    - Satisfaction: CRUD + Import
    //    - Sales: CRUD + Import
    //    - Manager is completely EXCLUDED from this group.
    // ---------------------------------------------------------------------
    Route::middleware(['role:admin,staff'])->group(function () {
        // Distributor CRUD
        Route::get('/distributors/create', [DistributorController::class, 'create'])->name('distributors.create');
        Route::post('/distributors', [DistributorController::class, 'store'])->name('distributors.store');
        Route::get('/distributors/{distributor}/edit', [DistributorController::class, 'edit'])->name('distributors.edit');
        Route::put('/distributors/{distributor}', [DistributorController::class, 'update'])->name('distributors.update');
        Route::delete('/distributors/{distributor}', [DistributorController::class, 'destroy'])->name('distributors.destroy');
        
        // Distributor Import
        Route::get('/distributors/import', [DistributorController::class, 'showImportForm'])->name('distributors.import');
        Route::post('/distributors/import', [DistributorController::class, 'import'])->name('distributors.import.process');
        Route::get('/distributors/template', [DistributorController::class, 'downloadTemplate'])->name('distributors.template');
        
        // Satisfaction CRUD
        Route::resource('satisfaction', SatisfactionController::class)->except(['index', 'show']);
        
        // Sales CRUD
        Route::resource('sales', SalesController::class)->except(['index', 'show']);
        
        // Sales Import
        Route::get('/sales/import', [SalesController::class, 'showImportForm'])->name('sales.import');
        Route::post('/sales/import', [SalesController::class, 'import'])->name('sales.import.process');
        Route::get('/sales/template', [SalesController::class, 'downloadTemplate'])->name('sales.template');
    });

    // ---------------------------------------------------------------------
    // 2. DISTRIBUTOR VIEW (Admin, Manager, Staff)
    //    Everyone needs to see the distributor list/details.
    // ---------------------------------------------------------------------
    Route::middleware(['role:admin,manager,staff'])->group(function () {
        Route::get('/distributors', [DistributorController::class, 'index'])->name('distributors.index');
        Route::get('/distributors/{distributor}', [DistributorController::class, 'show'])->name('distributors.show');
    });

    // ---------------------------------------------------------------------
    // 3. SATISFACTION & SALES VIEW (Admin & Staff ONLY)
    //    Manager does NOT have access to these modules.
    // ---------------------------------------------------------------------
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::get('/satisfaction', [SatisfactionController::class, 'index'])->name('satisfaction.index');
        Route::get('/satisfaction/{satisfaction}', [SatisfactionController::class, 'show'])->name('satisfaction.show');
        
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('sales.show');
    });

    // ---------------------------------------------------------------------
    // 4. EXPORT & CLUSTERING VIEW (Admin & Manager ONLY)
    //    Staff is EXCLUDED from Export and Clustering.
    // ---------------------------------------------------------------------
    Route::middleware(['role:admin,manager,staff'])->group(function () {
        // Export All Data (Distributors)
        Route::get('/export-all-data', [DistributorController::class, 'exportAll'])->name('export.all');
        
        // Clustering Read Access
        Route::get('/clustering', [ClusteringController::class, 'index'])->name('clustering.index');
        Route::get('/clustering/export', [ClusteringController::class, 'export'])->name('clustering.export');
    });

    // ---------------------------------------------------------------------
    // 5. ADMIN ONLY ROUTES
    // ---------------------------------------------------------------------
    Route::middleware(['role:admin'])->group(function () {
        // Run Analysis
        Route::post('/clustering/run', [ClusteringController::class, 'run'])->name('clustering.run');
    });
});
