<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Filament handles the root route (/) and Tenant CRUD (/tenants); this custom
// MVC dashboard is a separate page that admins land on after login.

// Define a named login route so the auth middleware knows where to redirect
Route::get('/system/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::get('/debug-users', function() {
    return \App\Models\User::all();
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Placeholder pages for sidebar sections that don't have a dedicated
    // feature built yet.
    Route::get('/content-management', fn () => view('pages.placeholder', ['title' => 'Content Management']))->name('content-management');
    Route::get('/reports', fn () => view('pages.placeholder', ['title' => 'Reports']))->name('reports');
    Route::get('/email-campaigns', fn () => view('pages.placeholder', ['title' => 'Email Campaigns']))->name('email-campaigns');
    Route::get('/admin-directory', [\App\Http\Controllers\AdminDirectoryController::class, 'index'])->name('admin-directory.index');
    Route::get('/admin-directory/create', [\App\Http\Controllers\AdminDirectoryController::class, 'create'])->name('admin-directory.create');
    Route::post('/admin-directory', [\App\Http\Controllers\AdminDirectoryController::class, 'store'])->name('admin-directory.store');
    // Software routes (placeholder until feature is built)
    Route::get('/software', fn () => view('pages.placeholder', ['title' => 'Software']))->name('software.index');
    Route::get('/software/create', fn () => view('pages.placeholder', ['title' => 'Add Software']))->name('software.create');

    // System Roles API for popup
    Route::post('/roles', [\App\Http\Controllers\RolePermissionController::class, 'store'])->name('roles.store');
    Route::delete('/roles/{id}', [\App\Http\Controllers\RolePermissionController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/permissions', [\App\Http\Controllers\RolePermissionController::class, 'updatePermissions'])->name('roles.permissions.update');
    Route::get('/security', fn () => view('pages.placeholder', ['title' => 'Security']))->name('security');
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'save'])->name('settings.save');
    Route::get('/account', [\App\Http\Controllers\AccountSettingsController::class, 'index'])->name('account.settings');
    Route::post('/account/profile', [\App\Http\Controllers\AccountSettingsController::class, 'updateProfile'])->name('account.profile');
    Route::post('/account/password', [\App\Http\Controllers\AccountSettingsController::class, 'updatePassword'])->name('account.password');
    Route::post('/account/locale', [\App\Http\Controllers\AccountSettingsController::class, 'updateLocale'])->name('account.locale');
    
    // DB Tracker Routes
    Route::get('/db-tracker', [\App\Http\Controllers\DbTrackerController::class, 'index'])->name('db-tracker');
    Route::get('/db-tracker/schema', [\App\Http\Controllers\DbTrackerController::class, 'schema'])->name('db-tracker.schema');
    Route::get('/db-tracker/schema/{table}/details', [\App\Http\Controllers\DbTrackerController::class, 'schemaTableDetails'])->name('db-tracker.schema.details');
    Route::get('/db-tracker/schema-erd', [\App\Http\Controllers\DbTrackerController::class, 'schemaErd'])->name('db-tracker.schema.erd');
    Route::get('/db-tracker/data', [\App\Http\Controllers\DbTrackerController::class, 'data'])->name('db-tracker.data');
    Route::get('/db-tracker/performance', [\App\Http\Controllers\DbTrackerController::class, 'performance'])->name('db-tracker.performance');
    Route::get('/db-tracker/performance/stats', [\App\Http\Controllers\DbTrackerController::class, 'performanceStats'])->name('db-tracker.performance.stats');
    Route::get('/db-tracker/security', [\App\Http\Controllers\DbTrackerController::class, 'security'])->name('db-tracker.security');
    Route::get('/db-tracker/backups', [\App\Http\Controllers\DbTrackerController::class, 'backups'])->name('db-tracker.backups');
    Route::post('/db-tracker/action', [\App\Http\Controllers\DbTrackerController::class, 'action'])->name('db-tracker.action');
    Route::get('/db-tracker/logs', [\App\Http\Controllers\DbTrackerController::class, 'logs'])->name('db-tracker.logs');

    // Named/pathed distinctly from Filament's own POST /logout route — reusing
    // that exact method+URI pair overwrites Filament's route in the router's
    // dispatch table and breaks route('filament.admin.auth.logout') everywhere
    // else in the panel (e.g. the account menu on every Filament page).
    Route::post('/dashboard/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    })->name('dashboard.logout');
});
