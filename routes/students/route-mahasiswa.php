<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mahasiswa\HomeController;

// HAK AKSES MAHASISWA
Route::group(['prefix' => 'mahasiswa', 'middleware' => ['mhs-access:Mahasiswa Aktif'], 'as' => 'mahasiswa.'],function(){
    // GLOBAL MENU AUTHENTIKASI
    Route::get('/signout',[App\Http\Controllers\Mahasiswa\AuthController::class, 'AuthSignOutPost'])->name('auth-signout-post');

    Route::get('/profile',[App\Http\Controllers\Private\Mahasiswa\RootController::class, 'renderProfile'])->name('profile-render');
    Route::patch('/profile',[App\Http\Controllers\Private\Mahasiswa\RootController::class, 'handleProfile'])->name('profile-handle');

    // GLOBAL MENU
    Route::get('/home',[App\Http\Controllers\Mahasiswa\HomeController::class, 'index'])->name('home-index');
    Route::get('/profile/old',[App\Http\Controllers\Mahasiswa\HomeController::class, 'profile'])->name('home-profile');
    Route::get('/tagihan',[App\Http\Controllers\Mahasiswa\HomeController::class, 'tagihanIndex'])->name('home-tagihan-index');
    Route::get('/tagihan/{code}/invoice',[App\Http\Controllers\Mahasiswa\HomeController::class, 'tagihanInvoice'])->name('home-tagihan-invoice');
    Route::get('/jadwal-kuliah',[App\Http\Controllers\Mahasiswa\HomeController::class, 'jadkulIndex'])->name('home-jadkul-index');
    Route::get('/tagihan/view/{code}',[App\Http\Controllers\Mahasiswa\HomeController::class, 'tagihanView'])->name('home-tagihan-view');
    Route::post('/tagihan/view/{code}/payment',[App\Http\Controllers\Mahasiswa\HomeController::class, 'tagihanPayment'])->name('home-tagihan-payment');
    Route::get('/tagihan/view/{code}/payment/success',[App\Http\Controllers\Mahasiswa\HomeController::class, 'tagihanSuccess'])->name('home-tagihan-payment-success');

    // Menampilkan form absensi berdasarkan kode jadwal
    Route::get('/jadwal-kuliah/{code}/absen', [App\Http\Controllers\Mahasiswa\HomeController::class, 'jadkulAbsen'])->name('home-jadkul-absen');
    // Menyimpan absensi mahasiswa
    Route::post('/jadwal-kuliah/store/absen', [App\Http\Controllers\Mahasiswa\HomeController::class, 'jadkulAbsenStore'])->name('home-jadkul-absen-store');
    Route::get('/jadwal-kuliah/rekap', [App\Http\Controllers\Mahasiswa\RekapAbsensiController::class, 'index'])->name('home-jadkul-rekap');


    Route::post('/jadwal-kuliah/store/{code}/feedback',[App\Http\Controllers\Mahasiswa\HomeController::class, 'storeFBPerkuliahan'])->name('jadkul.feedback-store');

    // PRIVATE FUNCTION => PROFILE
    Route::patch('/profile/update-image',[App\Http\Controllers\Mahasiswa\HomeController::class, 'saveImageProfile'])->name('home-profile-save-image');
    Route::patch('/profile/update-data',[App\Http\Controllers\Mahasiswa\HomeController::class, 'saveDataProfile'])->name('home-profile-save-data');
    Route::patch('/profile/update-kontak',[App\Http\Controllers\Mahasiswa\HomeController::class, 'saveDataKontak'])->name('home-profile-save-kontak');
    Route::patch('/profile/update-password',[App\Http\Controllers\Mahasiswa\HomeController::class, 'saveDataPassword'])->name('home-profile-save-password');

    // PRIVATE FUNCTION => SUPPORT TICKET
    Route::get('/support',[App\Http\Controllers\Mahasiswa\Pages\SupportController::class, 'index'])->name('support.ticket-index');
    Route::get('/support/open',[App\Http\Controllers\Mahasiswa\Pages\SupportController::class, 'open'])->name('support.ticket-open');
    Route::get('/support/view/{code}',[App\Http\Controllers\Mahasiswa\Pages\SupportController::class, 'view'])->name('support.ticket-view');
    Route::get('/support/create/{dept}',[App\Http\Controllers\Mahasiswa\Pages\SupportController::class, 'create'])->name('support.ticket-create');
    Route::post('/support/create/store',[App\Http\Controllers\Mahasiswa\Pages\SupportController::class, 'store'])->name('support.ticket-store');
    Route::post('/support/create/store-reply/{code}',[App\Http\Controllers\Mahasiswa\Pages\SupportController::class, 'storeReply'])->name('support.ticket-store-reply');

    // PRIVATE FUNCTION => TUGAS KULIAH
    Route::get('/tugas-kuliah',[App\Http\Controllers\Mahasiswa\Pages\StudentTaskController::class, 'index'])->name('akademik.tugas-index');
    Route::get('/tugas-kuliah/{code}/view',[App\Http\Controllers\Mahasiswa\Pages\StudentTaskController::class, 'view'])->name('akademik.tugas-view');
    Route::post('/tugas-kuliah/{code}/store',[App\Http\Controllers\Mahasiswa\Pages\StudentTaskController::class, 'store'])->name('akademik.tugas-store');
    
    Route::prefix('krs')->name('krs.')->group(function () {
        Route::get('/', [App\Http\Controllers\Mahasiswa\KrsController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Mahasiswa\KrsController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Mahasiswa\KrsController::class, 'store'])->name('store');
        Route::get('/cetak/{id}', [App\Http\Controllers\Mahasiswa\KrsController::class, 'show'])->name('show');
        Route::delete('/{id}', [App\Http\Controllers\Mahasiswa\KrsController::class, 'destroy'])->name('destroy');
    });

    Route::get('/khs', [App\Http\Controllers\Mahasiswa\KhsController::class, 'index'])->name('khs.index');
    Route::get('/khs/pdf', [App\Http\Controllers\Mahasiswa\KhsController::class, 'cetakPdf'])->name('khs.pdf');

    // AJAX ASYNC
    Route::get('/ajax/getTicketLastReply/{code}',[App\Http\Controllers\Mahasiswa\Pages\SupportController::class, 'AjaxLastReply'])->name('ajax.support.ticket-last-reply');
    Route::post('/krs/calc-sks', [App\Http\Controllers\Mahasiswa\KrsController::class, 'calcSks'])->name('krs.calcSks');
    Route::get('/ajax/getTagihan',[App\Http\Controllers\Mahasiswa\HomeController::class, 'tagihanIndexAjax'])->name('ajax-tagihan-index');

    // Menampilkan halaman absensi berdasarkan kode jadwal
    Route::get('/jadwal/absen/{code}', [HomeController::class, 'jadkulAbsen'])
        ->name('mahasiswa.home-jadkul-absen');

    // Menyimpan hasil absensi mahasiswa
    Route::post('/jadwal/absen/store', [HomeController::class, 'jadkulAbsenStore'])
        ->name('mahasiswa.home-jadkul-absen-store');
});
