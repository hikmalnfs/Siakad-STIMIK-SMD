<?php
namespace App\Http\Controllers\Dosen\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Akademik\JadwalKuliah;
use App\Models\AbsensiMahasiswa;
use App\Models\AbsensiStatus;
use App\Models\Pengaturan\WebSetting;
use App\Models\Akademik\TahunAkademik;

class JadwalAjarController extends Controller
{
    public function index(Request $request)
    {
        // Ambil pengaturan website
        $web = WebSetting::where('id', 1)->first();

        // Ambil ID dosen yang sedang login
        $dosenId = Auth::guard('dosen')->user()->id;

        // Ambil jadwal kuliah berdasarkan dosen dan filter yang diberikan
        $jadwals = JadwalKuliah::where('dosen_id', $dosenId)
            ->when($request->tahun_akademik_id, function ($query) use ($request) {
                return $query->where('tahun_akademik_id', $request->tahun_akademik_id);
            })
            ->when($request->semester, function ($query) use ($request) {
                return $query->whereHas('mataKuliah', function ($mq) use ($request) {
                    return $mq->where('semester', $request->semester);
                });
            })
            ->latest()
            ->get();

        // Ambil daftar tahun akademik untuk filter
        $tahunAkademikList = TahunAkademik::orderBy('start_date', 'desc')->get();

        // Mengarahkan ke view dengan data yang diperlukan
        return view('dosen.akademik.kelas-index', compact('jadwals', 'web', 'tahunAkademikList'));
    }

public function viewAbsen($code)
{
    // Pastikan pengaturan website dimuat
    $web = WebSetting::where('id', 1)->first();

    // Ambil ID dosen yang sedang login
    $dosenId = Auth::guard('dosen')->user()->id;

    // Ambil data Jadwal berdasarkan kode dan dosen
    $jadwal = JadwalKuliah::where('code', $code)
        ->where('dosen_id', $dosenId)
        ->with(['kelas.mahasiswas', 'mataKuliah'])
        ->firstOrFail();

    // Ambil data absensi mahasiswa
    $absens = AbsensiMahasiswa::where('jadkul_code', $jadwal->code)->get();

    // Gabungkan mahasiswa dari setiap kelas dalam jadwal
    $mahasiswas = collect();
    foreach ($jadwal->kelas as $kelas) {
        $mahasiswas = $mahasiswas->merge($kelas->mahasiswas);
    }
    $mahasiswas = $mahasiswas->unique('id')->values();

    // Ambil status absensi
    $absensiStatus = AbsensiStatus::where('jadkul_code', $jadwal->code)->get();

    // Data absensi per mahasiswa per pertemuan
    $absensiData = [];
    foreach ($absens as $absen) {
        $absensiData[$absen->mahasiswa_id][$absen->pertemuan] = $absen->absen_type;
    }

    // Pass data ke view
    return view('dosen.akademik.kelas-absensi', compact(
        'jadwal',
        'web', // Pastikan $web ada di sini
        'mahasiswas',
        'absensiData',
        'absensiStatus'
    ));
}

    // Fungsi untuk membuka/tutup absensi per pertemuan
    public function toggleAbsensi($jadkul_code, $pertemuan)
    {
        $status = AbsensiStatus::firstOrNew([
            'jadkul_code' => $jadkul_code,
            'pertemuan' => $pertemuan,
        ]);

        // Toggle status absensi
        $status->is_active = !$status->is_active;
        $status->status = $status->is_active ? 'buka' : 'tutup';
        $status->save();

        // Menampilkan pesan sesuai dengan status absensi yang dibuka/tutup
        Alert::success('Berhasil', 'Status absensi pertemuan ke-' . $pertemuan . ' diperbarui.');
        return back();
    }

    // Fungsi untuk mengupdate deskripsi absensi
    public function updateAbsen(Request $request, $code)
    {
        $absen = AbsensiMahasiswa::where('code', $code)->first();

        // Update deskripsi absensi
        $absen->absen_desc = $request->absen_desc;
        $absen->save();

        Alert::success('Success', 'Data telah berhasil diupdate');
        return back();
    }
}
