<?php

namespace App\Http\Controllers\Master\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Models\Dosen;
use App\Models\Akademik\Kelas;
use App\Models\Pengaturan\WebSetting;

class DosenController extends Controller
{
    public function renderDosen()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Dosen";
        $data['academy'] = $data['webs']->school_apps . ' by ' . $data['webs']->school_name;
        $data['dosen'] = Dosen::latest()->get();
        $data['kelas'] = Kelas::all();  // Untuk dropdown pilih wali kelas (nullable)

        return view('master.pengguna.dosen-index', $data, compact('user'));
    }

    public function viewDosen($code)
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Dosen";
        $data['academy'] = $data['webs']->school_apps . ' by ' . $data['webs']->school_name;
        $data['dosen'] = Dosen::where('code', $code)->firstOrFail();
        $data['kelas'] = Kelas::all();

        return view('master.pengguna.dosen-views', $data, compact('user'));
    }

    public function handleDosen(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:dosens,email',
                'phone' => 'required|string|unique:dosens,phone',
                'password' => 'required|string|min:6',
                'dsn_stat' => 'required|in:Aktif,Tidak Aktif',
                'wali' => 'nullable|exists:kelas,id',
            ]);

            $code = 'DSN-' . strtoupper(Str::random(8));

            Dosen::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'code' => $code,
                'dsn_stat' => $request->dsn_stat,
                'wali' => $request->wali, // simpan id kelas ke kolom 'wali'
                'created_by' => Auth::id()
            ]);

            DB::commit();
            $spref = Auth::user() ? Auth::user()->prefix : '';
            return redirect()->route($spref . 'pengguna.dosen-render')->with('success', 'Dosen berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function updateDosen(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $dosen = Dosen::where('code', $code)->firstOrFail();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:dosens,email,' . $dosen->id,
                'phone' => 'required|string|unique:dosens,phone,' . $dosen->id,
                'password' => 'nullable|string|min:6',
                'dsn_stat' => 'required|in:Aktif,Tidak Aktif',
                'wali' => 'nullable|exists:kelas,id',
            ]);

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'dsn_stat' => $request->dsn_stat,
                'wali' => $request->wali, // update kolom wali
                'updated_by' => Auth::id()
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $dosen->update($updateData);

            DB::commit();
            $spref = Auth::user() ? Auth::user()->prefix : '';
            return redirect()->route($spref . 'pengguna.dosen-render')->with('success', 'Data dosen berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function handleProfile(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $dosen = Dosen::where('code', $code)->firstOrFail();

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:dosens,email,' . $dosen->id,
                'phone' => 'required|string|unique:dosens,phone,' . $dosen->id,
                'dsn_stat' => 'required|in:Aktif,Tidak Aktif',
                'wali' => 'nullable|exists:kelas,id',
            ];

            if ($request->domicile_same === 'No') {
                $rules = array_merge($rules, [
                    'domicile_addres' => 'required|string',
                    'domicile_rt' => 'required|string',
                    'domicile_rw' => 'required|string',
                    'domicile_village' => 'required|string',
                    'domicile_subdistrict' => 'required|string',
                    'domicile_city' => 'required|string',
                    'domicile_province' => 'required|string',
                    'domicile_poscode' => 'required|string',
                ]);
            }

            $request->validate($rules);

            $updateData = $request->except(['_token', '_method', 'photo']);

            if ($request->hasFile('photo')) {
                if ($dosen->photo && $dosen->photo !== 'default.jpg') {
                    Storage::disk('public')->delete('images/profile/' . $dosen->photo);
                }
                $photoName = time() . '-' . $dosen->code . '-' . uniqid() . '.' . $request->photo->getClientOriginalExtension();
                $request->photo->storeAs('images/profile', $photoName, 'public');
                $updateData['photo'] = $photoName;
            }

            if ($request->domicile_same === 'Yes') {
                $updateData['domicile_addres'] = $request->ktp_addres;
                $updateData['domicile_rt'] = $request->ktp_rt;
                $updateData['domicile_rw'] = $request->ktp_rw;
                $updateData['domicile_village'] = $request->ktp_village;
                $updateData['domicile_subdistrict'] = $request->ktp_subdistrict;
                $updateData['domicile_city'] = $request->ktp_city;
                $updateData['domicile_province'] = $request->ktp_province;
                $updateData['domicile_poscode'] = $request->ktp_poscode;
            }

            $updateData['updated_by'] = Auth::id();

            $dosen->update($updateData);

            DB::commit();
            $spref = Auth::user() ? Auth::user()->prefix : '';
            return redirect()->route($spref . 'pengguna.dosen-views', $code)->with('success', 'Profile berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function deleteDosen($code)
    {
        try {
            DB::beginTransaction();

            $dosen = Dosen::where('code', $code)->firstOrFail();

            if ($dosen->id === Auth::id()) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri');
            }

            $dosen->update([
                'deleted_by' => Auth::id()
            ]);
            $dosen->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Dosen berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Dosen: ' . $e->getMessage());
        }
    }
}
