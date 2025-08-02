<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use RealRashid\SweetAlert\Facades\Alert;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Str;

// Models
use App\Models\Akademik\{ProgramStudi, MataKuliah, Kurikulum, Dosen, TahunAkademik, JadwalKuliah, Kelas};
use App\Models\Keuangan\{TagihanKuliah, HistoryTagihan};
use App\Models\Infrastruktur\Ruang;
use App\Models\AbsensiStatus;
use App\Models\AbsensiMahasiswa;
use App\Models\Notification;
use App\Models\Pengaturan\WebSetting;
use App\Models\FeedBack\FBPerkuliahan;
use App\Models\Publikasi\Pengumuman;
use App\Models\Nilai;

class HomeController extends Controller
{
    protected array $response = ['snap_token' => null, 'code_uniq' => null];

    public function index(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();
        $kelas = $user->kelas;
        $prokuId = optional($kelas?->proku)->id;
        $pstudiId = optional($kelas?->pstudi)->id;

        $tagihan = TagihanKuliah::where(function ($q) use ($user, $prokuId, $pstudiId) {
            $q->where('users_id', $user->id);
            if ($prokuId && Schema::hasColumn('tagihan_kuliahs', 'proku_id')) {
                $q->orWhere('proku_id', $prokuId);
            }
            if ($pstudiId && Schema::hasColumn('tagihan_kuliahs', 'prodi_id')) {
                $q->orWhere('prodi_id', $pstudiId);
            }
        })->sum('price');

        $history = HistoryTagihan::where('users_id', $user->id)->where('stat', 1)->with('tagihan')->get();
        $historyTotal = $history->sum(fn($h) => $h->tagihan->price ?? 0);

        return view('mahasiswa.home-index', [
            'web' => WebSetting::find(1),
            'tagihan' => $tagihan,
            'history' => $historyTotal,
            'jadkul' => JadwalKuliah::where('kelas_id', $kelas->id ?? null)->count(),
            'habsen' => AbsensiMahasiswa::where('author_id', $user->id)->where('absen_type', 'H')->count(),
            'notify' => Notification::whereIn('send_to', [0, 3])->latest()->paginate(5),
            'sisatagihan' => $tagihan - $historyTotal,
                        'pengumuman' => Pengumuman::where('status', 'Publish')
                        ->orderByDesc('created_at')
                        ->paginate(5),
        ]);
    }

        public function profile()
    {
        return view('mahasiswa.home-profile', ['web' => WebSetting::find(1)]);
    }

    public function jadkulIndex()
    {
        $user = Auth::guard('mahasiswa')->user();

        // Ambil nama kelas dari mahasiswa login (relasi kelas harus sudah didefinisikan di model Mahasiswa)
        $kelasName = optional($user->kelas)->name;

        // Filter jadwal berdasarkan nama kelas (melalui relasi)
        $jadwal = JadwalKuliah::with(['waktuKuliah', 'mataKuliah', 'kelas', 'ruang', 'dosen'])
                    ->whereHas('kelas', function ($query) use ($kelasName) {
                        $query->where('name', $kelasName);
                    })
                    ->get();

        return view('mahasiswa.pages.mhs-jadkul-index', [
            'kuri'   => Kurikulum::all(),
            'taka'   => TahunAkademik::all(),
            'pstudi' => ProgramStudi::all(),
            'matkul' => MataKuliah::all(),
            'jadkul' => $jadwal,
            'ruang'  => Ruang::all(),
            'kelas'  => Kelas::where('name', $kelasName)->get(),
            'web'    => WebSetting::find(1),
        ]);
    }

    public function jadkulAbsen($code)
    {
        $user = Auth::guard('mahasiswa')->user();
        

        // Ambil jadwal kuliah beserta relasi kelas dan mahasiswanya
        
        $jadwal = JadwalKuliah::with('kelas.mahasiswas')->where('code', $code)->first();
        if (!$jadwal) {
            Alert::error('Error', 'Jadwal tidak ditemukan.');
            return redirect()->back();
        }

        // Cek apakah mahasiswa termasuk dalam salah satu kelas
        $mahasiswaTerdaftar = $jadwal->kelas->contains(function ($kelas) use ($user) {
            return $kelas->mahasiswas->contains('id', $user->id);
        });

        if (!$mahasiswaTerdaftar) {
            Alert::error('Tidak Diizinkan', 'Anda tidak terdaftar di kelas ini.');
            return redirect()->route('mahasiswa.home-jadkul-index');
        }

        // Ambil semua absensi status yang aktif dan terbuka
        $statusAbsensi = AbsensiStatus::where('jadkul_code', $code)
            ->where('is_active', 1)
            ->where('status', 'buka')
            ->latest()
            ->get();

        if ($statusAbsensi->isEmpty()) {
            Alert::warning('Absen Belum Dibuka', 'Silakan tunggu dosen membuka absensi.');
            return redirect()->route('mahasiswa.home-jadkul-index');
        }

        // Cek apakah ada pertemuan terbuka yang belum diabsen
        $pertemuanBelumAbsen = $statusAbsensi->first(function ($status) use ($code, $user) {
            return !AbsensiMahasiswa::where('jadkul_code', $code)
                ->where('author_id', $user->id)
                ->where('pertemuan', $status->pertemuan)
                ->exists();
        });

        if (!$pertemuanBelumAbsen) {
            Alert::info('Info', 'Kamu sudah melakukan absensi pada semua pertemuan yang dibuka.');
            return redirect()->route('mahasiswa.home-jadkul-index');
        }

        // Tampilkan form absensi
        return view('mahasiswa.pages.mhs-jadkul-absen', [
            'web' => WebSetting::find(1),
            'kuri' => Kurikulum::all(),
            'taka' => TahunAkademik::all(),
            'pstudi' => ProgramStudi::all(),
            'matkul' => MataKuliah::all(),
            'jadkul' => $jadwal,
            'ruang' => Ruang::all(),
            'kelas' => Kelas::all(),
            'absensiStatus' => $statusAbsensi,
        ]);
    }

    public function jadkulAbsenStore(Request $request)
{
    $request->validate([
        'jadkul_code' => 'required|string|exists:jadwal_kuliahs,code',
        'pertemuan'   => 'required|integer|min:1|max:16',
        'absen_type'  => 'required|string|in:H,I,S,A',
        'absen_proof' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:8192',
        'absen_desc'  => 'nullable|string|max:1000',
    ]);

    $user = Auth::guard('mahasiswa')->user();
    $jadwal = JadwalKuliah::where('code', $request->jadkul_code)->firstOrFail();

    $statusAbsensi = AbsensiStatus::where('jadkul_code', $jadwal->code)
        ->where('pertemuan', $request->pertemuan)
        ->where('is_active', 1)
        ->where('status', 'buka')
        ->latest()
        ->first();

    if (!$statusAbsensi) {
        Alert::error('Error', 'Absensi belum dibuka untuk pertemuan ini.');
        return redirect()->back();
    }

    $sudahAbsen = AbsensiMahasiswa::where('jadkul_code', $jadwal->code)
        ->where('author_id', $user->id)
        ->where('pertemuan', $request->pertemuan)
        ->exists();

    if ($sudahAbsen) {
        Alert::info('Info', 'Kamu sudah absen untuk pertemuan ini.');
        return redirect()->route('mahasiswa.home-jadkul-index');
    }

    $absen = new AbsensiMahasiswa();
    $absen->code = uniqid();
    $absen->author_id = $user->id;
    $absen->jadkul_code = $jadwal->code;
    $absen->pertemuan = $request->pertemuan;
    $absen->absen_type = $request->absen_type;
    $absen->absen_date = now()->toDateString();
    $absen->absen_time = now()->format('H:i:s');
    $absen->absen_desc = $request->absen_desc;

    if ($request->hasFile('absen_proof')) {
        $file = $request->file('absen_proof');
        $filename = 'absen-' . $jadwal->code . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/images/profile/absen', $filename);
        $absen->absen_proof = str_replace('public/', '', $path);
    }

    $absen->save();

    Alert::success('Success', 'Absensi berhasil disimpan.');
    return redirect()->route('mahasiswa.home-jadkul-index');
}

    public function saveImageProfile(Request $request)
    {
        $request->validate(['mhs_image' => 'image|mimes:jpeg,png,jpg|max:8192']);
        $user = Auth::guard('mahasiswa')->user();

        if ($request->hasFile('mhs_image')) {
            $img = $request->file('mhs_image');
            $name = 'profile-' . $user->mhs_code . '-' . uniqid() . '.' . $img->getClientOriginalExtension();
            $manager = new ImageManager(new Driver());
            $manager->read($img->getRealPath())->scaleDown(height: 300)->toPng()->save(storage_path("app/public/images/profile/$name"));

            if ($user->mhs_image && $user->mhs_image != 'default/default-profile.jpg') {
                File::delete(storage_path('app/public/images/' . $user->mhs_image));
            }

            $user->mhs_image = "profile/$name";
            $user->save();
        }

        Alert::success('Success', 'Foto berhasil diupdate');
        return redirect()->route('mahasiswa.home-profile');
    }

    public function saveDataProfile(Request $request)
    {
        $request->validate([
            'mhs_name' => 'required|string|max:255',
            'mhs_nim' => 'required|string|max:255|unique:users,user,' . Auth::id(),
            'mhs_birthplace' => 'required|string|max:255',
            'mhs_birthdate' => 'required|date',
        ]);

        $user = Auth::guard('mahasiswa')->user();
        $user->fill($request->only([
            'mhs_name', 'mhs_nim', 'mhs_reli', 'mhs_gend', 'mhs_birthplace', 'mhs_birthdate'
        ]))->save();

        Alert::success('Success', 'Profil berhasil diupdate');
        return back();
    }

    public function saveDataKontak(Request $request)
    {
        $request->validate([
            'mhs_phone' => 'required|numeric|unique:users,phone,' . Auth::id(),
            'mhs_mail' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'mhs_parent_father' => 'nullable|string|max:255',
            'mhs_parent_mother' => 'nullable|string|max:255',
            'mhs_parent_father_phone' => 'nullable|string|max:14',
            'mhs_parent_mother_phone' => 'nullable|string|max:14',
            'mhs_parent_wali_name' => 'nullable|string|max:14',
            'mhs_parent_wali_phone' => 'nullable|string|max:14',
            'mhs_addr_domisili' => 'nullable|string|max:4192',
            'mhs_addr_kelurahan' => 'nullable|string|max:255',
            'mhs_addr_kecamatan' => 'nullable|string|max:255',
            'mhs_addr_kota' => 'nullable|string|max:255',
            'mhs_addr_provinsi' => 'nullable|string|max:255',
        ]);

        $user = Auth::guard('mahasiswa')->user();
        $user->fill($request->all())->save();

        Alert::success('Success', 'Kontak berhasil diupdate');
        return back();
    }

    public function saveDataPassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|confirmed',
        ]);

        $user = Auth::guard('mahasiswa')->user();

        if (!Hash::check($request->old_password, $user->password)) {
            Alert::error('Error', 'Password lama salah');
            return back();
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        Alert::success('Success', 'Password berhasil diubah');
        return back();
    }

    public function tagihanIndex()
    {
        $user = Auth::guard('mahasiswa')->user();
        $kelas = $user->kelas;

        $query = TagihanKuliah::where('users_id', $user->id);

        if ($kelas) {
            if (Schema::hasColumn('tagihan_kuliahs', 'proku_id') && $kelas->proku) {
                $query->orWhere('proku_id', $kelas->proku->id);
            }
            if (Schema::hasColumn('tagihan_kuliahs', 'prodi_id') && $kelas->pstudi) {
                $query->orWhere('prodi_id', $kelas->pstudi->id);
            }
        }

        $tagihan = $query->latest()->get();

        $history = HistoryTagihan::where('users_id', $user->id)
            ->where('stat', 1)
            ->with('tagihan')
            ->latest()
            ->get();

        return view('mahasiswa.pages.mhs-tagihan-index', [
            'web' => WebSetting::find(1),
            'tagihan' => $tagihan,
            'history' => $history,
            'sisatagihan' => $tagihan->sum('price') - $history->sum(fn($h) => $h->tagihan->price ?? 0),
        ]);
    }

    public function tagihanIndexAjax()
    {
        $user = Auth::guard('mahasiswa')->user();
        $kelas = $user->kelas;

        $query = TagihanKuliah::where('users_id', $user->id);

        if ($kelas) {
            if ($kelas->proku) {
                $query->orWhere('proku_id', $kelas->proku->id);
            }
            if ($kelas->pstudi) {
                $query->orWhere('prodi_id', $kelas->pstudi->id);
            }
        }

        $tagihan = $query->latest()->get();

        $history = HistoryTagihan::where('users_id', $user->id)
            ->where('stat', 1)
            ->with('tagihan')
            ->latest()
            ->get();

        return response()->json([
            'tagihan' => $tagihan,
            'history' => $history,
            'sisatagihan' => $tagihan->sum('price') - $history->sum(fn($h) => $h->tagihan->price ?? 0),
        ]);
    }

    public function tagihanView($code)
    {
        $user = Auth::guard('mahasiswa')->user();
        $data['web'] = WebSetting::find(1);

        if (HistoryTagihan::where('users_id', $user->id)->where('tagihan_code', $code)->where('stat', 1)->exists()) {
            Alert::error('Error', 'Kamu sudah membayar tagihan ini');
            return back();
        }

        $data['tagihan'] = TagihanKuliah::where('code', $code)->firstOrFail();
        return view('mahasiswa.pages.mhs-tagihan-view', $data);
    }

    public function storeFBPerkuliahan(Request $request, $code)
    {
        $user = Auth::guard('mahasiswa')->user();

        $request->validate([
            'fb_score' => 'required|in:Tidak Puas,Cukup Puas,Sangat Puas',
            'fb_reason' => 'required'
        ]);

        if (FBPerkuliahan::where('fb_jakul_code', $code)->where('fb_users_code', $user->mhs_code)->exists()) {
            Alert::error('Error', 'Kamu sudah memberikan FeedBack pada perkuliahan ini.');
            return back();
        }

        try {
            FBPerkuliahan::create([
                'fb_users_code' => $user->mhs_code,
                'fb_jakul_code' => $code,
                'fb_code' => uniqid('fb-'),
                'fb_score' => $request->fb_score,
                'fb_reason' => $request->fb_reason,
            ]);

            Alert::success('Sukses', 'Terima kasih telah memberi FeedBack ^_^');
        } catch (\Exception $e) {
            logger()->error('Gagal simpan feedback: ' . $e->getMessage());
            Alert::error('Error', 'Gagal menyimpan feedback.');
        }

        return back();
    }
}
