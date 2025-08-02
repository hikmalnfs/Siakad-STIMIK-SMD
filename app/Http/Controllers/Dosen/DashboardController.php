<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Menampilkan tampilan utama dosen
        return view('dosen.pages.home-index');
    }

    public function profile()
    {
        

        return view('dosen.pages.home-profile');
    }

    public function sidebar()
    {
        return view('dosen.pages.home-sidebar');
    }

    // Kamu bisa tambah method lainnya sesuai kebutuhan
}
