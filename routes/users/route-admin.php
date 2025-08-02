<?php

use Illuminate\Support\Facades\Route;

// HAK AKSES DEPARTEMENT ACADEMIC
Route::group(['prefix' => 'admin', 'middleware' => ['checkUser:Departement Admin'], 'as' => 'admin.'],function(){

    // GLOBAL ROUTE
    require __DIR__.'/../private-core.php';


    // STATUS ACTIVE BOLEH AKSES INI
    Route::middleware(['is-active:1'])->group(function () {
    Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard/chart-data', [App\Http\Controllers\Admin\DashboardController::class, 'getChartData'])->name('chart.student.data');


    });

});
