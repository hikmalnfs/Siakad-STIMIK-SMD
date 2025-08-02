<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Pengaturan\WebSetting;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{
    public function renderSignin()
    {
        $user = $this->getCurrentUser();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user?->prefix ?? '';
        $data['menus'] = "Login";
        $data['pages'] = "Authentication";
        $data['academy'] = $data['webs']->school_apps . ' by ' . $data['webs']->school_name;
        $data['user'] = $user;

        return view('central.auth.signin-content', $data);
    }

    public function handleSignin(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Coba login sebagai User
        if ($this->attemptLogin('web', User::class, $fieldType, $login, $password)) {
            return $this->redirectAfterLogin(Auth::guard('web')->user());
        }

        // Coba login sebagai Dosen
        if ($this->attemptLogin('dosen', Dosen::class, $fieldType, $login, $password)) {
            return $this->redirectAfterLogin(Auth::guard('dosen')->user());
        }

        // Coba login sebagai Mahasiswa
        if ($this->attemptLogin('mahasiswa', Mahasiswa::class, $fieldType, $login, $password)) {
            return $this->redirectAfterLogin(Auth::guard('mahasiswa')->user());
        }

        // Jika gagal semua
        Alert::error('Login Gagal', 'Email/NIM/Username atau password salah.');
        return back();
    }

    public function handleLogout(Request $request)
    {
        foreach (['web', 'dosen', 'mahasiswa'] as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
                Alert::success('Berhasil!', 'Logout berhasil.');
                return redirect()->route('auth.render-signin');
            }
        }

        Alert::error('Gagal!', 'Tidak ada sesi login yang ditemukan.');
        return back();
    }

    // ================================
    // 🔧 HELPER METHODS
    // ================================

    private function attemptLogin($guard, $model, $field, $loginValue, $password)
    {
        $user = $model::where($field, $loginValue)->first();
        if (!$user) return false;

        $success = Auth::guard($guard)->attempt([
            $field => $loginValue,
            'password' => $password,
        ]);

        // Pastikan benar-benar login di guard tersebut
        return $success && Auth::guard($guard)->check();
    }

    private function redirectAfterLogin($user)
{
    if (!$user || !isset($user->prefix)) {
        Alert::error('Login gagal', 'Autentikasi gagal. Coba lagi.');
        return redirect()->route('auth.render-signin');
    }

    Alert::toast('Selamat datang, ' . $user->name, 'success');

    // Khusus dosen redirect ke home-index dosen
    if (Auth::guard('dosen')->check()) {
        return redirect()->route('dosen.home-index');
    }

    // Khusus mahasiswa redirect ke home-index mahasiswa
    if (Auth::guard('mahasiswa')->check()) {
        return redirect()->route('mahasiswa.home-index');
    }

    // Default untuk role lainnya (admin/web)
    return redirect()->route($user->prefix . 'profile-render');
}

    private function getCurrentUser()
    {
        return Auth::user()
            ?: Auth::guard('dosen')->user()
            ?: Auth::guard('mahasiswa')->user();
    }
}