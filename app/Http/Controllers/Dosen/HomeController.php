<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

// Models
use App\Models\Dosen;
use App\Models\FeedBack\FBPerkuliahan;
use App\Models\Notification;
use App\Models\Publikasi\Pengumuman;
use App\Models\Pengaturan\WebSetting;
use App\Models\Akademik\JadwalKuliah;

class HomeController extends Controller
{
    public function index()
    {
        $dosenId = Auth::guard('dosen')->user()->id;

        $data = [
            'web' => WebSetting::find(1),
            'feedback' => FBPerkuliahan::whereHas('jadwal', function ($query) use ($dosenId) {
                $query->where('dosen_id', $dosenId);
            })->latest()->get(),
            'jadwal_count' => JadwalKuliah::where('dosen_id', $dosenId)->count(),
            'pengumuman' => Pengumuman::where('status', 'Publish')
                        ->orderByDesc('created_at')
                        ->paginate(5),
        ];
        // dd($data);

        return view('dosen.home-index', $data);
    }

    public function profile()
    {
        return view('dosen.home-profile', ['web' => WebSetting::find(1)]);
    }

    public function saveImageProfile(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:8192',
        ]);

        $user = Auth::guard('dosen')->user();

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $name = 'profile-' . $user->code . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $destinationPath = storage_path('app/public/images/profile/dosen');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $manager = new ImageManager(new Driver());
            $manager->make($image->getRealPath())
                ->resize(null, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save($destinationPath . '/' . $name);

            if ($user->photo && $user->photo !== 'default.jpg') {
                File::delete($destinationPath . '/' . basename($user->photo));
            }

            $user->photo = "profile/dosen/" . $name;
            $user->save();

            Alert::success('Success', 'Foto profil berhasil diupdate');
        }

        return redirect()->route('dosen.home-profile');
    }

    public function saveDataProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'numb_nidn' => 'required|string|max:255|unique:users,numb_nidn,' . Auth::guard('dosen')->id(),
            'bio_placebirth' => 'required|string|max:255',
            'bio_datebirth' => 'required|date',
            'bio_gender' => 'required|string|max:20',
        ]);

        $user = Auth::guard('dosen')->user();
        $user->fill($request->only([
            'name', 'numb_nidn', 'bio_placebirth', 'bio_datebirth', 'bio_gender'
        ]))->save();

        Alert::success('Success', 'Data profil berhasil diupdate');
        return back();
    }

    public function saveDataKontak(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|unique:users,phone,' . Auth::guard('dosen')->id(),
            'email' => 'required|email|max:255|unique:users,email,' . Auth::guard('dosen')->id(),
        ]);

        $user = Auth::guard('dosen')->user();
        $user->fill($request->only(['phone', 'email']))->save();

        Alert::success('Success', 'Kontak berhasil diperbarui');
        return back();
    }

    public function saveDataPassword(Request $request)
    {
        $request->validate([
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'confirmed'],
        ]);

        $user = Auth::guard('dosen')->user();

        if (!Hash::check($request->old_password, $user->password)) {
            Alert::error('Error', 'Password lama tidak cocok.');
            return back();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        Alert::success('Success', 'Password berhasil diubah!');
        return back();
    }
}
