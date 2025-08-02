<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Pengaturan\WebSetting;

class ErrorController extends Controller
{
    private function setPrefix()
    {
        // Hindari akses Auth jika tidak login sama sekali
        if (!Auth::guard('dosen')->check() && !Auth::guard('mahasiswa')->check() && !Auth::guard('web')->check()) {
            return 'web-admin.';
        }

        $user = Auth::guard('dosen')->user()
            ?? Auth::guard('mahasiswa')->user()
            ?? Auth::guard('web')->user();

        $rawType = $user->raw_type ?? null;

        return match ($rawType) {
            1 => 'finance.',
            2 => 'officer.',
            3 => 'academic.',
            4 => 'admin.',
            5 => 'support.',
            default => 'web-admin.',
        };
    }

    public function ErrorVerify()
    {
        $data['web'] = WebSetting::where('id', 1)->first();
        $data['prefix'] = $this->setPrefix();
        $data['title'] = "ESEC - ESchool Ecosystem";
        $data['menu'] = "Error Verify";
        $data['submenu'] = "Please verify your account";
        $data['subdesc'] = "Errors, Please check inbox mail to verify your account";

        return view('base.base-errors-index', $data);
    }

    public function ErrorAccess()
    {
        $data['prefix'] = $this->setPrefix();
        $data['web'] = WebSetting::where('id', 1)->first();
        $data['title'] = "ESEC - ESchool Ecosystem";
        $data['menu'] = "Error Authorization";
        $data['submenu'] = "You are not authorized to access this page";
        $data['subdesc'] = "Error, you are not allowed to enter this page";

        return view('base.base-errors-index', $data);
    }

    public function ErrorNotFound()
    {
        Alert::error('Error 404', 'Halaman tidak ditemukan');
        return back();
    }
}
