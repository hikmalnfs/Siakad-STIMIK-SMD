<?php

namespace App\Http\Controllers\Private\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// USE SYSTEM
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// USE MODELS
use App\Models\Pengaturan\WebSetting;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class RootController extends Controller
{
    public function renderProfile()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $mahasiswa ? $mahasiswa->prefix : '';
        $data['menus'] = "Detail";
        $data['pages'] = "Profile Mahasiswa";
        $data['academy'] = $data['webs']->school_apps . ' by ' . $data['webs']->school_name;
        
        return view('central.backpage.profile-mahasiswa', $data, compact('mahasiswa'));
    }

    public function handleProfile(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                // Personal Information
                'name' => 'required|string|max:255',
                'title_front' => 'nullable|string|max:50',
                'title_behind' => 'nullable|string|max:50',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'bio_placebirth' => 'nullable|string|max:100',
                'bio_datebirth' => 'nullable|date',
                'bio_gender' => 'nullable|in:Laki-laki,Perempuan',
                'bio_religion' => 'nullable|string|max:50',
                'bio_blood' => 'nullable|string|max:5',
                'bio_height' => 'nullable|numeric',
                'bio_weight' => 'nullable|numeric',

                // Contact Information
                'email' => 'required|email|unique:mahasiswas,email,' . Auth::guard('mahasiswa')->user()->id,
                'phone' => 'required|string|unique:mahasiswas,phone,' . Auth::guard('mahasiswa')->user()->id,
                'link_ig' => 'nullable|url',
                'link_fb' => 'nullable|url',
                'link_in' => 'nullable|url',

                // Address Information
                'ktp_addres' => 'nullable|string',
                'ktp_rt' => 'nullable|string|max:10',
                'ktp_rw' => 'nullable|string|max:10',
                'ktp_village' => 'nullable|string|max:100',
                'ktp_subdistrict' => 'nullable|string|max:100',
                'ktp_city' => 'nullable|string|max:100',
                'ktp_province' => 'nullable|string|max:100',
                'ktp_poscode' => 'nullable|string|max:10',
                'domicile_same' => 'required|in:Yes,No',
                'domicile_addres' => 'nullable|required_if:domicile_same,No|string',
                'domicile_rt' => 'nullable|required_if:domicile_same,No|string|max:10',
                'domicile_rw' => 'nullable|required_if:domicile_same,No|string|max:10',
                'domicile_village' => 'nullable|required_if:domicile_same,No|string|max:100',
                'domicile_subdistrict' => 'nullable|required_if:domicile_same,No|string|max:100',
                'domicile_city' => 'nullable|required_if:domicile_same,No|string|max:100',
                'domicile_province' => 'nullable|required_if:domicile_same,No|string|max:100',
                'domicile_poscode' => 'nullable|required_if:domicile_same,No|string|max:10',

                // Education Information
                'edu1_type' => 'required|in:SMA/SMK,Diploma,Sarjana,Magister,Doktor',
                'edu1_place' => 'required|string|max:255',
                'edu1_major' => 'required|string|max:255',
                'edu1_average_score' => 'required|string|max:10',
                'edu1_graduate_year' => 'required|string|max:4',
                'edu2_type' => 'nullable|in:SMA/SMK,Diploma,Sarjana,Magister,Doktor',
                'edu2_place' => 'nullable|string|max:255',
                'edu2_major' => 'nullable|string|max:255',
                'edu2_average_score' => 'nullable|string|max:10',
                'edu2_graduate_year' => 'nullable|string|max:4',
                'edu3_type' => 'nullable|in:SMA/SMK,Diploma,Sarjana,Magister,Doktor',
                'edu3_place' => 'nullable|string|max:255',
                'edu3_major' => 'nullable|string|max:255',
                'edu3_average_score' => 'nullable|string|max:10',
                'edu3_graduate_year' => 'nullable|string|max:4',

                // Family Information
                'father_name' => 'nullable|string|max:255',
                'father_datebirth' => 'nullable|date',
                'father_lifestat' => 'nullable|in:Hidup,Meninggal',
                'father_education' => 'nullable|string|max:100',
                'father_occupation' => 'nullable|string|max:100',
                'father_income' => 'nullable|string|max:50',
                'father_phone' => 'nullable|string|max:20',
                'father_address' => 'nullable|string',

                'mother_name' => 'nullable|string|max:255',
                'mother_datebirth' => 'nullable|date',
                'mother_lifestat' => 'nullable|in:Hidup,Meninggal',
                'mother_education' => 'nullable|string|max:100',
                'mother_occupation' => 'nullable|string|max:100',
                'mother_income' => 'nullable|string|max:50',
                'mother_phone' => 'nullable|string|max:20',
                'mother_address' => 'nullable|string',

                'guard_name' => 'nullable|string|max:255',
                'guard_nik' => 'nullable|string|max:20',
                'guard_datebirth' => 'nullable|date',
                'guard_relation' => 'nullable|string|max:50',
                'guard_phone' => 'nullable|string|max:20',
                'guard_address' => 'nullable|string',

                // Identity Information
                'numb_kk' => 'nullable|string|max:20',
                'numb_ktp' => 'nullable|string|max:20',
                'numb_nim' => 'nullable|string|max:20',
                'numb_reg' => 'nullable|string|max:20',
                'numb_nisn' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $mahasiswa = Auth::guard('mahasiswa')->user();
            $data = $validator->validated();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($mahasiswa->photo && $mahasiswa->photo !== 'default.jpg') {
                    Storage::disk('public')->delete('images/profile/' . $mahasiswa->photo);
                }

                // Kompres dan simpan foto profil
                $photoName = time() . '-' . $mahasiswa->code . '-' . uniqid() . '-' . uniqid() . '.jpg';
                
                // Buat instance ImageManager dengan driver GD
                $manager = new ImageManager(new Driver());
                
                // Baca dan kompres gambar
                $image = $manager->read($request->photo->getRealPath());
                
                // Resize dengan ukuran yang lebih besar untuk foto profil
                if ($image->height() > 1200) {
                    $image->scaleDown(height: 1200); 
                }
                
                // Simpan dengan kualitas tinggi (90%)
                Storage::disk('public')->put('images/profile/' . $photoName, $image->toJpeg(90));
                
                $data['photo'] = $photoName;
            }

            // Update mahasiswa information
            $mahasiswa->update($data);

            return redirect()->back()->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update profile: ' . $e->getMessage())
                ->withInput();
        }
    }
}
