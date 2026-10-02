<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Masyarakat\ReportController as MasyarakatReportController;
use App\Http\Controllers\Opd\OpdController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - JALAN KU Infrastructure Platform
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/peta', [PublicController::class, 'peta'])->name('public.peta');
Route::get('/laporan-publik', [PublicController::class, 'laporanPublik'])->name('public.reports.index');
Route::get('/laporan-publik/{id}', [PublicController::class, 'detailLaporanPublik'])->name('public.reports.show');
Route::get('/statistik', [PublicController::class, 'statistik'])->name('public.statistik');
Route::get('/cara-kerja', [PublicController::class, 'caraKerja'])->name('public.cara-kerja');
Route::get('/tentang', [PublicController::class, 'tentang'])->name('public.tentang');
Route::get('/api/geo-reports', [PublicController::class, 'apiGeoReports'])->name('api.geo-reports');
Route::get('/docs', function () {
    return view('docs.swagger');
})->name('api.docs');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/lupa-sandi', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/lupa-sandi', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');

    // MASYARAKAT PORTAL (Accessible by Masyarakat, Admin, OPD)
    Route::prefix('masyarakat')->name('masyarakat.')->middleware('role:masyarakat,admin,opd')->group(function () {
        Route::get('/dashboard', [MasyarakatReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/laporan/buat', [MasyarakatReportController::class, 'create'])->name('reports.create');
        Route::post('/laporan', [MasyarakatReportController::class, 'store'])->name('reports.store');
        Route::get('/laporan', [MasyarakatReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/{id}', [MasyarakatReportController::class, 'show'])->name('reports.show');
        Route::post('/laporan/{id}/feedback', [MasyarakatReportController::class, 'submitFeedback'])->name('reports.feedback');
    });

    // ADMIN PORTAL (Accessible by Admin - Semua Fitur Super Admin Dipindahkan ke Sini)
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/laporan', [AdminController::class, 'reports'])->name('reports.index');
        Route::get('/laporan/{id}', [AdminController::class, 'show'])->name('reports.show');
        Route::post('/laporan/{id}/verifikasi', [AdminController::class, 'verify'])->name('reports.verify');
        Route::post('/laporan/{id}/tolak', [AdminController::class, 'reject'])->name('reports.reject');
        Route::post('/laporan/{id}/duplikat', [AdminController::class, 'markDuplicate'])->name('reports.duplicate');
        Route::post('/laporan/{id}/tugaskan', [AdminController::class, 'assignOpd'])->name('reports.assign');
        Route::post('/laporan/{id}/yolo', [AdminController::class, 'runYoloAnalysis'])->name('reports.yolo');
        Route::post('/recalculate-topsis', [AdminController::class, 'recalculateTopsis'])->name('recalculate-topsis');
        Route::delete('/laporan/{id}', [AdminController::class, 'deleteReport'])->name('reports.delete');
        Route::delete('/foto/{id}', [AdminController::class, 'deleteReportPhoto'])->name('photos.delete');
        Route::delete('/foto-progres/{id}', [AdminController::class, 'deleteProgressPhoto'])->name('progress-photos.delete');
        Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit-logs');

        // Master & Konfigurasi (Pindahan dari Super Admin):
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users.index');
        Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{id}', [SuperAdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [SuperAdminController::class, 'deleteUser'])->name('users.delete');

        Route::get('/roles', [SuperAdminController::class, 'roles'])->name('roles.index');

        Route::get('/opd', [SuperAdminController::class, 'opds'])->name('opds.index');
        Route::post('/opd', [SuperAdminController::class, 'storeOpd'])->name('opds.store');
        Route::put('/opd/{id}', [SuperAdminController::class, 'updateOpd'])->name('opds.update');
        Route::delete('/opd/{id}', [SuperAdminController::class, 'deleteOpd'])->name('opds.delete');

        Route::get('/kriteria', [SuperAdminController::class, 'criteria'])->name('criteria.index');
        Route::post('/kriteria/bobot', [SuperAdminController::class, 'updateWeights'])->name('criteria.update-weights');

        Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings.index');
        Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
    });

    // OPD / PETUGAS PORTAL (Accessible by OPD, Admin)
    Route::prefix('opd')->name('opd.')->middleware('role:opd,admin')->group(function () {
        Route::get('/dashboard', [OpdController::class, 'dashboard'])->name('dashboard');
        Route::get('/tugas', [OpdController::class, 'tasks'])->name('tasks.index');
        Route::get('/tugas/{id}', [OpdController::class, 'show'])->name('tasks.show');
        Route::post('/tugas/{id}/survei', [OpdController::class, 'startSurvey'])->name('tasks.survey');
        Route::post('/tugas/{id}/progres', [OpdController::class, 'storeProgress'])->name('tasks.progress');
        Route::put('/tugas/{id}/progres/{progressId}', [OpdController::class, 'updateProgress'])->name('tasks.progress.update');
        Route::delete('/tugas/{id}/progres/{progressId}', [OpdController::class, 'deleteProgress'])->name('tasks.progress.delete');
        Route::delete('/foto-progres/{id}', [OpdController::class, 'deleteProgressPhoto'])->name('tasks.delete-photo');
    });

    // Alias kompatibilitas rute superadmin ke admin
    Route::middleware('role:admin')->name('superadmin.')->group(function () {
        Route::get('/super-admin/dashboard', fn() => redirect()->route('admin.dashboard'))->name('dashboard');
        Route::get('/admin/users-alias', [SuperAdminController::class, 'users'])->name('users.index');
        Route::post('/admin/users-alias', [SuperAdminController::class, 'storeUser'])->name('users.store');
        Route::put('/admin/users-alias/{id}', [SuperAdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/admin/users-alias/{id}', [SuperAdminController::class, 'deleteUser'])->name('users.delete');
        Route::get('/admin/opd-alias', [SuperAdminController::class, 'opds'])->name('opds.index');
        Route::post('/admin/opd-alias', [SuperAdminController::class, 'storeOpd'])->name('opds.store');
        Route::put('/admin/opd-alias/{id}', [SuperAdminController::class, 'updateOpd'])->name('opds.update');
        Route::delete('/admin/opd-alias/{id}', [SuperAdminController::class, 'deleteOpd'])->name('opds.delete');
        Route::get('/admin/criteria-alias', [SuperAdminController::class, 'criteria'])->name('criteria.index');
        Route::post('/admin/criteria-alias/bobot', [SuperAdminController::class, 'updateWeights'])->name('criteria.update-weights');
        Route::get('/admin/audit-logs-alias', [AdminController::class, 'auditLogs'])->name('audit-logs.index');
        Route::get('/admin/settings-alias', [SuperAdminController::class, 'settings'])->name('settings.index');
        Route::post('/admin/settings-alias', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
        Route::put('/super-admin/users/{id}', [SuperAdminController::class, 'updateUser']);
        Route::put('/super-admin/opd/{id}', [SuperAdminController::class, 'updateOpd']);
    });
});
