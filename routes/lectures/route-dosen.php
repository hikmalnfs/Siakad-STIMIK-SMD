<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dosen\Akademik\PengajuanKrsController;
use App\Http\Controllers\Dosen\Akademik\KelasController;
use App\Http\Controllers\Dosen\Akademik\NilaiController;
use App\Http\Controllers\Dosen\Akademik\ManageMahasiswaController;

// HAK AKSES DOSEN
Route::group(['prefix' => 'dosen', 'middleware' => ['dsn-access:Dosen Aktif'], 'as' => 'dosen.'], function () {

    // GLOBAL MENU AUTHENTIKASI
    Route::get('/signout', [App\Http\Controllers\Dosen\AuthController::class, 'AuthSignOutPost'])->name('auth-signout-post');

        // GLOBAL MENU
    Route::get('/home', [App\Http\Controllers\Dosen\HomeController::class, 'index'])->name('home-index');
    Route::get('/profile/old', [App\Http\Controllers\Dosen\HomeController::class, 'profile'])->name('home-profile');

    Route::get('/profile', [App\Http\Controllers\Private\Dosen\RootController::class, 'renderProfile'])->name('profile-render');
    Route::patch('/profile', [App\Http\Controllers\Private\Dosen\RootController::class, 'handleProfile'])->name('profile-handle');

    // PROFILE UPDATES
    Route::patch('/profile/update-image', [App\Http\Controllers\Dosen\HomeController::class, 'saveImageProfile'])->name('home-profile-save-image');
    Route::patch('/profile/update-data', [App\Http\Controllers\Dosen\HomeController::class, 'saveDataProfile'])->name('home-profile-save-data');
    Route::patch('/profile/update-kontak', [App\Http\Controllers\Dosen\HomeController::class, 'saveDataKontak'])->name('home-profile-save-kontak');
    Route::patch('/profile/update-password', [App\Http\Controllers\Dosen\HomeController::class, 'saveDataPassword'])->name('home-profile-save-password');

    // DATA AKADEMIK - JADWAL MENGAJAR
    Route::get('/data-akademik/jadwal', [App\Http\Controllers\Dosen\Akademik\JadwalAjarController::class, 'index'])->name('akademik.jadwal-index');
    Route::get('/data-akademik/jadwal/{code}/absen', [App\Http\Controllers\Dosen\Akademik\JadwalAjarController::class, 'viewAbsen'])->name('akademik.jadwal-view-absen');
    Route::get('/data-akademik/jadwal/{code}/feedback', [App\Http\Controllers\Dosen\Akademik\JadwalAjarController::class, 'viewFeedBack'])->name('akademik.jadwal-view-feedback');
    Route::patch('/data-akademik/jadwal/absen/{code}/update', [App\Http\Controllers\Dosen\Akademik\JadwalAjarController::class, 'updateAbsen'])->name('akademik.jadwal-absen-update');

    // DATA AKADEMIK - KELOLA TUGAS
    Route::get('/data-akademik/kelola-tugas', [App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'index'])->name('akademik.stask-index');
    Route::get('/data-akademik/kelola-tugas/tambah', [App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'create'])->name('akademik.stask-create');
    Route::get('/data-akademik/kelola-tugas/view/{code}', [App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'view'])->name('akademik.stask-view');
    Route::get('/data-akademik/kelola-tugas/view/detail/{code}', [App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'viewDetail'])->name('akademik.stask-view-detail');
    Route::get('/data-akademik/kelola-tugas/edit/{code}', [App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'edit'])->name('akademik.stask-edit');
    Route::get('/data-akademik/kelola-tugas/edit/{code}/score', [App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'editScore'])->name('akademik.stask-edit-score');
    Route::post('/data-akademik/kelola-tugas/store', [App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'store'])->name('akademik.stask-store');
    Route::patch('/data-akademik/kelola-tugas/update/{code}', [App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'update'])->name('akademik.stask-update');
    Route::patch('/data-akademik/kelola-tugas/update/{code}/score', [App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'updateScore'])->name('akademik.stask-update-score');
    Route::delete('/data-akademik/kelola-tugas/delete/{code}', [App\Http\Controllers\Dosen\Akademik\StudentTaskController::class, 'destroy'])->name('akademik.stask-destroy');

    // DATA AKADEMIK - KELAS (Absensi & Nilai)
    Route::get('/data-akademik/kelas', [KelasController::class, 'index'])->name('akademik.kelas-index');
    Route::get('/data-akademik/kelas/{id}/absen', [KelasController::class, 'viewAbsensi'])->name('akademik.kelas-view-absensi');

      // Buka dan Tutup absensi
    Route::post('/{jadwal}/absensi/{pertemuan}/buka', [KelasController::class, 'bukaAbsensi'])->name('absensi.buka');
    Route::post('/{jadwal}/absensi/{pertemuan}/tutup', [KelasController::class, 'tutupAbsensi'])->name('absensi.tutup');
    
    Route::post('/data-akademik/kelas/{id}/absen/save', [KelasController::class, 'saveAbsensi'])->name('akademik.kelas-save-absensi');
    Route::post('/data-akademik/kelas/{id}/absen/{pertemuan}/toggle', [KelasController::class, 'toggleAbsensi'])->name('akademik.kelas-absensi-toggle');

    Route::post('/data-akademik/kelas/{id}/absen/update', [KelasController::class, 'updateAbsensi'])->name('akademik.kelas-update-absensi');

    Route::get('/data-akademik/kelas/{id}/nilai', [KelasController::class, 'viewNilai'])->name('akademik.kelas-view-nilai');
    Route::post('/data-akademik/kelas/{id}/nilai', [KelasController::class, 'updateNilai'])->name('akademik.kelas-update-nilai');

    Route::get('/data-akademik/kelas/{id}/mahasiswa', [KelasController::class, 'viewMahasiswa'])->name('akademik.kelas-mahasiswa');

    Route::get('/data-akademik/kelas/export/pdf', [KelasController::class, 'exportPdf'])->name('kelas-export-pdf');
    Route::get('/data-akademik/kelas/export/excel', [KelasController::class, 'exportExcel'])->name('kelas-export-excel');

     // Tambahkan ini:
    Route::get('kelas/export/pdf', [KelasController::class, 'exportPdf'])->name('kelas-export-pdf');
    Route::get('kelas/export/excel', [KelasController::class, 'exportExcel'])->name('kelas-export-excel');

    // DATA AKADEMIK - NILAI SPESIFIK
    Route::prefix('nilai')->name('nilai.')->group(function () {
        Route::get('/', [NilaiController::class, 'list'])->name('list');
        Route::get('/{id}', [NilaiController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [NilaiController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [NilaiController::class, 'update'])->name('update');
        Route::get('/{id}/cetak', [NilaiController::class, 'cetakDosen'])->name('cetakDosen');
        Route::post('/{id}/update-komposisi', [NilaiController::class, 'updateKomposisi'])->name('updateKomposisi');

        // 🔒 Kunci nilai (otomatis via ajukan) dan buka kunci (superadmin only)
        Route::post('/{id}/ajukan', [NilaiController::class, 'ajukanKeBaak'])->name('ajukan');
        Route::post('/{id}/buka-kunci', [NilaiController::class, 'bukaKunciNilai'])->name('bukaKunci');
    });

    // GRAPHIC AJAX FUNCTION
    Route::get('/services/ajax/graphic/{code}/kepuasan-mengajar', [App\Http\Controllers\Services\Ajax\GraphicController::class, 'getKepuasanMengajar'])->name('services.ajax.graphic.kepuasan-mengajar');
    Route::get('/services/ajax/graphic/kepuasan-mengajar/dosen', [App\Http\Controllers\Services\Ajax\GraphicController::class, 'getKepuasanMengajarDosen'])->name('services.ajax.graphic.kepuasan-mengajar-dosen');

    // PENGAJUAN KRS - khusus dosen wali
    Route::prefix('pengajuan-krs')
        ->middleware(['dosen.wali'])
        ->name('pengajuan-krs.')
        ->group(function () {
            Route::get('/', [PengajuanKrsController::class, 'index'])->name('index');
            Route::get('/{id}', [PengajuanKrsController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [PengajuanKrsController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [PengajuanKrsController::class, 'reject'])->name('reject');
            Route::put('/{id}', [PengajuanKrsController::class, 'update'])->name('update');
        });

        // Sudah dalam Route::prefix('dosen')->name('dosen.')->group()
Route::get('mahasiswa-bimbingan', [ManageMahasiswaController::class, 'index'])->name('mahasiswa-bimbingan.index');
Route::get('mahasiswa-bimbingan/{id}', [ManageMahasiswaController::class, 'show'])->name('mahasiswa-bimbingan.show');

// KRS
Route::get('mahasiswa-bimbingan/{id}/krs/create', [ManageMahasiswaController::class, 'createKrs'])->name('mahasiswa-bimbingan.krs.create');
Route::post('mahasiswa-bimbingan/{id}/krs/store', [ManageMahasiswaController::class, 'storeKrs'])->name('mahasiswa-bimbingan.krs.store');
Route::patch('mahasiswa-bimbingan/krs/{krsId}/status', [ManageMahasiswaController::class, 'updateKrsStatus'])->name('mahasiswa-bimbingan.krs.update-status');

// KHS
Route::get('mahasiswa-bimbingan/{id}/khs', [ManageMahasiswaController::class, 'showKhs'])->name('mahasiswa-bimbingan.khs.show');
Route::get('mahasiswa-bimbingan/{id}/khs/{semester}/export', [ManageMahasiswaController::class, 'exportKhsPdf'])->name('mahasiswa-bimbingan.khs.export');

// Naik Semester
Route::patch('mahasiswa-bimbingan/{id}/naik-semester', [ManageMahasiswaController::class, 'naikSemester'])->name('mahasiswa-bimbingan.naik-semester');

// Status Akademik
Route::patch('mahasiswa-bimbingan/{id}/status-akademik', [ManageMahasiswaController::class, 'updateStatusAkademik'])->name('mahasiswa-bimbingan.update-status-akademik');

// Catatan Bimbingan
Route::post('mahasiswa-bimbingan/{id}/catatan-bimbingan', [ManageMahasiswaController::class, 'storeCatatanBimbingan'])->name('mahasiswa-bimbingan.catatan-bimbingan.store');

// Export
Route::get('mahasiswa-bimbingan/export/excel', [ManageMahasiswaController::class, 'exportExcel'])->name('mahasiswa-bimbingan.export-excel');

// Fitur Tambah KRS oleh dosen untuk mahasiswa bimbingan
Route::get('mahasiswa-bimbingan/{id}/krs/create', [App\Http\Controllers\Dosen\Akademik\ManageMahasiswaController::class, 'createKrs'])->name('mahasiswa-bimbingan.krs.create');
Route::post('mahasiswa-bimbingan/{id}/krs/store', [App\Http\Controllers\Dosen\Akademik\ManageMahasiswaController::class, 'storeKrs'])->name('mahasiswa-bimbingan.krs.store');


});
