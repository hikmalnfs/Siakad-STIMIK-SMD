<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// Model
use App\Models\Dosen;
use App\Models\Akademik\JadwalKuliah;
use App\Models\Pengaturan\WebSetting;

class AuthController extends Controller
{
    public function AuthSignInPage()
    {
        if (Auth::guard('dosen')->check()) {
            Alert::info('Informasi', 'Saat ini kamu telah login sebagai ' . Auth::guard('dosen')->user()->dsn_name);
            return redirect()->route('dosen.home-index');
        }

        if (Auth::guard('mahasiswa')->check()) {
            Alert::info('Informasi', 'Saat ini kamu telah login sebagai mahasiswa.');
            return redirect()->route('mahasiswa.home-index');
        }

        $data['web'] = WebSetting::find(1);
        $data['title'] = "Login Dosen - " . $data['web']->school_name;
        $data['menu'] = "Halaman Login Dosen";
        $data['submenu'] = "SignIn to continue";
        $data['subdesc'] = "Gunakan ID unik anda untuk login...";

        return view('base.auth.auth-dsn-signin', $data);
    }

    public function AuthSignInPost(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $login = $request->input('login');

        // Tentukan field login berdasarkan input
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'dsn_mail' : 'dsn_nidn';

        if (preg_match('/^[0-9]{10,13}$/', $login)) {
            $fieldType = 'dsn_phone';
        }

        $user = Dosen::where($fieldType, $login)->first();

        if (!$user) {
            Alert::error('Error', 'Mohon maaf akun anda belum terdaftar');
            return back()->withInput();
        }

        $remember = $request->has('remember_me');

        if (Auth::guard('dosen')->attempt([$fieldType => $login, 'password' => $request->password], $remember)) {
            if ($user->raw_dsn_stat == 0) {
                Alert::error('Error', $user->dsn_name . ' saat ini belum aktif, silakan hubungi Administrator');
                Auth::guard('dosen')->logout();
                return back()->withInput();
            }

            // Cek apakah dosen adalah dosen wali (wali == 1 atau 'yes')
            $isWali = ($user->wali == 1 || strtolower(trim($user->wali)) === 'yes');
            $isPengampu = JadwalKuliah::where('dosen_id', $user->id)->exists();

            // Simpan role dosen di session
            session([
                'is_dosen_wali' => $isWali,
                'is_dosen_pengampu' => $isPengampu,
            ]);

            // Logging untuk debugging
            Log::info('Login Dosen:', [
                'dsn_name' => $user->dsn_name,
                'login_field' => $fieldType,
                'login_value' => $login,
                'is_wali' => $isWali,
                'is_pengampu' => $isPengampu,
            ]);

            // Tentukan peran dosen
            $roles = [];
            if ($isWali) $roles[] = 'Dosen Wali';
            if ($isPengampu) $roles[] = 'Dosen Pengampu';
            $peran = count($roles) > 0 ? implode(' & ', $roles) : 'Dosen Biasa';

            Alert::success('Berhasil Login', 'Anda login sebagai ' . $peran);
            return redirect()->route('dosen.home-index');
        } else {
            Alert::error('Error', 'Username / Email atau Password salah');
            return back()->withInput();
        }
    }

    public function AuthForgotPage()
    {
        $data['web'] = WebSetting::find(1);
        $data['title'] = "Reset Password Dosen - " . $data['web']->school_name;
        $data['menu'] = "Halaman Reset Password Dosen";
        $data['submenu'] = "Lupa Password";
        $data['subdesc'] = "Gunakan email aktif anda untuk reset password.";

        return view('base.auth.auth-dsn-forgot', $data);
    }

    public function AuthForgotVerify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:dosens,dsn_mail',
        ]);

        $user = Dosen::where('dsn_mail', $request->email)->first();

        $user->verify_token = Str::random(40);
        $user->token_created_at = now();
        $user->save();

        Mail::send('base.resource.mail-dsn-forgot-temp', ['user' => $user], function ($message) use ($user) {
            $message->to($user->dsn_mail);
            $message->subject('Reset Password for ' . $user->dsn_name);
            $message->from('admin@internal-dev.id', 'SIAKAD PT by Internal-Dev');
        });

        Alert::success('Success', 'Email reset berhasil dikirim.');
        return back();
    }

    public function AuthResetPage($token)
    {
        $data['web'] = WebSetting::find(1);
        $data['title'] = "Reset Password Dosen - " . $data['web']->school_name;
        $data['menu'] = 'Reset Password';
        $data['submenu'] = 'Form Reset';
        $data['user'] = Dosen::where('verify_token', $token)->firstOrFail();
        $data['token'] = $token;

        return view('base.auth.auth-dsn-reset', $data);
    }

    public function AuthResetPassword(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|same:password_confirm|min:6',
        ]);

        $user = Dosen::where('verify_token', $token)->first();

        if (!$user || Carbon::parse($user->token_created_at)->diffInHours(now()) > 1) {
            Alert::error('Error', 'Token tidak valid atau sudah kedaluwarsa');
            return back();
        }

        $user->password = Hash::make($request->password);
        $user->verify_token = null;
        $user->token_created_at = null;
        $user->save();

        Alert::success('Success', 'Password berhasil direset');
        return redirect()->route('dosen.auth-signin-page');
    }

    public function AuthSignOutPost(Request $request)
    {
        Auth::guard('dosen')->logout();
        session()->forget(['is_dosen_wali', 'is_dosen_pengampu']);

        Alert::success('Success', 'Anda berhasil logout');
        return redirect()->route('auth.render-signin');
    }
}
