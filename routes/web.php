<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RootController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/welcome', [RootController::class, 'renderWelcome'])->name('root.welcome');
Route::post('/api/setup', [SetupController::class, 'processSetup'])->name('setup.process');

// 🧩 Semua route frontend + auth dimasukkan ke group guest + first.setup
Route::middleware(['guest', 'first.setup'])->group(function () {
    // // HOMEPAGE
    Route::get('/landing', [RootController::class, 'renderHomePage'])->name('root.home-index');

    // AUTH UMUM
    Route::get('/', [App\Http\Controllers\AuthController::class, 'renderSignin'])->name('auth.render-signin');
    Route::post('/signin', [App\Http\Controllers\AuthController::class, 'handleSignin'])->name('auth.handle-signin');
    Route::get('/forgot', [App\Http\Controllers\AuthController::class, 'renderForgot'])->name('auth.render-forgot');
    Route::post('/forgot', [App\Http\Controllers\AuthController::class, 'handleForgot'])->name('auth.handle-forgot');
    Route::get('/logout', [App\Http\Controllers\AuthController::class, 'handleLogout'])->name('auth.handle-logout');

    // AUTENTIKASI MAHASISWA
    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/auth-signin', [App\Http\Controllers\Mahasiswa\AuthController::class, 'AuthSignInPage'])->name('auth-signin-page');
        Route::post('/auth-signin/post', [App\Http\Controllers\Mahasiswa\AuthController::class, 'AuthSignInPost'])->name('auth-signin-post');
        Route::get('/auth-forgot', [App\Http\Controllers\Mahasiswa\AuthController::class, 'AuthForgotPage'])->name('auth-forgot-page');
        Route::post('/auth-forgot/verify', [App\Http\Controllers\Mahasiswa\AuthController::class, 'AuthForgotVerify'])->name('auth-forgot-verify');
        Route::get('/auth-reset/{token}', [App\Http\Controllers\Mahasiswa\AuthController::class, 'AuthResetPage'])->name('auth-reset-page');
        Route::post('/auth-reset/{token}/save', [App\Http\Controllers\Mahasiswa\AuthController::class, 'AuthResetPassword'])->name('auth-reset-post');
    });

    // AUTENTIKASI ADMIN
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/auth-signin', [App\Http\Controllers\Admin\AuthController::class, 'AuthSignInPage'])->name('auth-signin-page');
        Route::post('/auth-signin/post', [App\Http\Controllers\Admin\AuthController::class, 'AuthSignInPost'])->name('auth-signin-post');
        Route::get('/auth-forgot', [App\Http\Controllers\Admin\AuthController::class, 'AuthForgotPage'])->name('auth-forgot-page');
        Route::post('/auth-forgot/verify', [App\Http\Controllers\Admin\AuthController::class, 'AuthForgotVerify'])->name('auth-forgot-verify');
        Route::get('/auth-reset/{token}', [App\Http\Controllers\Admin\AuthController::class, 'AuthResetPage'])->name('auth-reset-page');
        Route::post('/auth-reset/{token}/save', [App\Http\Controllers\Admin\AuthController::class, 'AuthResetPassword'])->name('auth-reset-post');
    });

    // AUTENTIKASI DOSEN
    Route::middleware(['auth:dosen', 'dsnaccess:aktif'])->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Dosen\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/auth-signin', [App\Http\Controllers\Dosen\AuthController::class, 'AuthSignInPage'])->name('auth-signin-page');
        Route::post('/auth-signin/post', [App\Http\Controllers\Dosen\AuthController::class, 'AuthSignInPost'])->name('auth-signin-post');
        Route::get('/auth-forgot', [App\Http\Controllers\Dosen\AuthController::class, 'AuthForgotPage'])->name('auth-forgot-page');
        Route::post('/auth-forgot/verify', [App\Http\Controllers\Dosen\AuthController::class, 'AuthForgotVerify'])->name('auth-forgot-verify');
        Route::get('/auth-reset/{token}', [App\Http\Controllers\Dosen\AuthController::class, 'AuthResetPage'])->name('auth-reset-page');
        Route::post('/auth-reset/{token}/save', [App\Http\Controllers\Dosen\AuthController::class, 'AuthResetPassword'])->name('auth-reset-post');
    });

    // FRONTEND (BERITA, GALERI, DOWNLOAD, DLL)
    Route::get('/post/view/{slug}', [App\Http\Controllers\Root\HomeController::class, 'postView'])->name('root.post-view');
    Route::get('/advice', [App\Http\Controllers\Root\HomeController::class, 'adviceIndex'])->name('root.home-advice');
    Route::get('/download', [App\Http\Controllers\Root\HomeController::class, 'downloadIndex'])->name('root.home-download');
    Route::get('/album-foto', [App\Http\Controllers\Root\HomeController::class, 'galleryIndex'])->name('root.gallery-index');
    Route::get('/album-foto/search', [App\Http\Controllers\Root\HomeController::class, 'gallerySearch'])->name('root.gallery-search');
    Route::get('/album-foto/show/{slug}', [App\Http\Controllers\Root\HomeController::class, 'galleryShow'])->name('root.gallery-show');
    Route::get('/admission/{slug}', [App\Http\Controllers\Root\HomeController::class, 'prodiIndex'])->name('root.home-prodi');
    Route::get('/program-kuliah/{code}', [App\Http\Controllers\Root\HomeController::class, 'prokuIndex'])->name('root.home-proku');
    Route::post('/advice/store', [App\Http\Controllers\Root\HomeController::class, 'adviceStore'])->name('root.home-advice-store');
    
});

// ===========================
// === MAHASISWA TERAUTENTIKASI ===
// ===========================
Route::middleware(['auth:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Mahasiswa\DashboardController::class, 'index'])->name('dashboard');
});

// ===========================
// === DOSEN TERAUTENTIKASI ===
// ===========================
Route::middleware(['auth:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Dosen\DashboardController::class, 'index'])->name('dashboard');
});

// ===========================
// === ADMIN TERAUTENTIKASI ===
// ===========================
Route::middleware(['auth:web'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
});

// ERROR PAGE
Route::prefix('error')->name('error.')->group(function () {
    Route::get('/verify', [App\Http\Controllers\Root\ErrorController::class, 'ErrorVerify'])->name('verify');
    Route::get('/access', [App\Http\Controllers\Root\ErrorController::class, 'ErrorAccess'])->name('access');
    Route::get('/notfound', [App\Http\Controllers\Root\ErrorController::class, 'ErrorNotFound'])->name('notfound');
});

// Include Modular Routes by Role/Department
require __DIR__.'/users/route-web-admin.php';
require __DIR__.'/users/route-admin.php';
require __DIR__.'/users/route-akademik.php';
require __DIR__.'/users/route-finance.php';
require __DIR__.'/users/route-officer.php';
require __DIR__.'/users/route-support.php';
require __DIR__.'/lectures/route-dosen.php';
require __DIR__.'/students/route-mahasiswa.php';
require __DIR__.'/master-core.php';
