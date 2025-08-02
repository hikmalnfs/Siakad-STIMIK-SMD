<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;

// Models
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\JadwalKuliah;
use App\Models\Akademik\Krs;
use App\Models\Pengaturan\WebSetting;

class KrsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();

        // Ambil daftar tahun akademik untuk filter
        $tahunAkademikList = TahunAkademik::orderBy('name', 'desc')->get();

        // Filter tahun akademik
        $filterTaId = $request->input('tahun_akademik');
        $filterSemester = $request->input('semester'); // Ganjil / Genap

        // Jika tidak pilih tahun akademik, default ke yang aktif
        if ($filterTaId) {
            $ta = TahunAkademik::find($filterTaId);
        } else {
            $ta = TahunAkademik::where('status', 'Aktif')->first();
            $filterTaId = $ta?->id;
        }

        if (!$ta) {
            Alert::warning('Tidak ada Tahun Akademik aktif');
            return back();
        }

        $query = Krs::with(['jadwal.mataKuliah', 'jadwal.kelas', 'jadwal.dosen', 'jadwal.ruang'])
            ->where('users_id', $user->id);

        // Filter tahun akademik di relasi jadwal
        if ($filterTaId) {
            $query->whereHas('jadwal', function ($q) use ($filterTaId) {
                $q->where('tahun_akademik_id', $filterTaId);
            });
        }

        // Filter semester (Ganjil/Genap)
        if ($filterSemester) {
            $query->whereHas('jadwal', function ($q) use ($filterSemester) {
                $q->where('semester', $filterSemester);
            });
        }

        $items = $query->get();

        $totalSks = $items->sum(fn($i) => $i->jadwal->mataKuliah->sks ?? 0);
        $maxSks = 24;

        return view('mahasiswa.krs.index', [
            'web'             => WebSetting::find(1),
            'mahasiswa'       => $user,
            'ta'              => $ta,
            'items'           => $items,
            'fakultas'        => $user->kelas?->pstudi,
            'dosen'           => $user->dosenPa,
            'totalSks'        => $totalSks,
            'maxSks'          => $maxSks,
            'prefix'          => 'mahasiswa.',
            'tahunAkademikList' => $tahunAkademikList,
            'filterTaId'      => $filterTaId,
            'filterSemester'  => $filterSemester,
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();

        $allTa = TahunAkademik::orderBy('name', 'desc')->get();

        $selectedTa = $request->has('tahun_akademik_id')
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::where('status', 'Aktif')->first();

        if (!$selectedTa) {
            Alert::warning('Tidak Ada Tahun Akademik Aktif', 'Silakan hubungi admin.');
            return back();
        }

        $jadwalKuliah = JadwalKuliah::with(['mataKuliah', 'kelas', 'dosen', 'ruang', 'waktuKuliah'])
            ->where('tahun_akademik_id', $selectedTa->id)
            ->whereHas('mataKuliah', fn($q) => $q->where('status', 'Aktif'))
            ->get();

        $krsTaken = Krs::where('users_id', $user->id)
            ->where('tahun_akademik_id', $selectedTa->id)
            ->pluck('jadwal_id')
            ->toArray();

        return view('mahasiswa.krs.create', [
            'web'          => WebSetting::find(1),
            'jadwalKuliah' => $jadwalKuliah,
            'ta'           => $selectedTa,
            'allTa'        => $allTa,
            'krsTaken'     => $krsTaken,
            'prefix'       => 'mahasiswa.',
        ]);
    }

public function store(Request $request)
{
    $user = Auth::guard('mahasiswa')->user();

    $taId = $request->input('tahun_akademik_id');
    $ta = TahunAkademik::find($taId);

    if (!$ta) {
        Alert::error('Error', 'Tahun Akademik tidak valid.');
        return back()->withInput();
    }

    $jadwalIds = $request->input('jadwal_kuliah_id', []);

    if (empty($jadwalIds)) {
        Alert::error('Gagal', 'Kamu belum memilih mata kuliah (KRS).');
        return back()->withInput();
    }

    $currentKrs = Krs::where('users_id', $user->id)
        ->where('tahun_akademik_id', $ta->id)
        ->whereIn('status', ['Menunggu', 'Disetujui'])
        ->with('jadwal.mataKuliah')
        ->get();

    $sksNow = $currentKrs->sum(fn($item) => $item->jadwal->mataKuliah->sks ?? 0);

    // Ambil wali_id dari kelas mahasiswa
    $waliId = $user->kelas?->wali_id;

    foreach ($jadwalIds as $jadwalId) {
        $jadwal = JadwalKuliah::with('mataKuliah')->find($jadwalId);

        if (!$jadwal || !$jadwal->mataKuliah || $jadwal->mataKuliah->status !== 'Aktif' || $jadwal->tahun_akademik_id != $ta->id) {
            continue;
        }

        $alreadyExists = Krs::where('users_id', $user->id)
            ->where('jadwal_id', $jadwalId)
            ->where('tahun_akademik_id', $ta->id)
            ->whereIn('status', ['Menunggu', 'Disetujui'])
            ->exists();

        if ($alreadyExists) {
            continue;
        }

        $sksNew = $jadwal->mataKuliah->sks ?? 0;

        if (($sksNow + $sksNew) > 24) {
            Alert::error('SKS Melebihi Batas', 'Total SKS maksimal adalah 24.');
            return back()->withInput();
        }

        Krs::create([
            'users_id'          => $user->id,
            'jadwal_id'         => $jadwalId,
            'tahun_akademik_id' => $ta->id,
            'status'            => 'Menunggu',
            'wali_id'           => $waliId,  // <-- disini diisi otomatis
        ]);

        $sksNow += $sksNew;
    }

    Alert::success('Berhasil', 'Pengajuan KRS berhasil dikirim.');
    return redirect()->route('mahasiswa.krs.index');
}


    public function show($id)
    {
        $user = Auth::guard('mahasiswa')->user();
        $ta = TahunAkademik::where('status', 'Aktif')->first();

        $items = Krs::with(['jadwal.mataKuliah', 'jadwal.kelas', 'jadwal.dosen', 'jadwal.ruang'])
            ->where('users_id', $user->id)
            ->where('tahun_akademik_id', $ta?->id)
            ->get();

        $pdf = Pdf::loadView('mahasiswa.krs.cetak', [
            'user'  => $user,
            'items' => $items,
            'ta'    => $ta,
        ]);

        return $pdf->stream("krs-{$user->nim}.pdf");
    }

    public function destroy($id)
    {
        $userId = Auth::guard('mahasiswa')->id();
        $krs = Krs::findOrFail($id);

        // Hanya boleh hapus jika milik mahasiswa
        if ($krs->users_id != $userId) {
            abort(403, 'Tidak diizinkan.');
        }

        // Status hanya 'Menunggu' atau 'Ditolak' yang boleh dihapus
        if (!in_array($krs->status, ['Menunggu', 'Ditolak'])) {
            Alert::error('Gagal', 'KRS hanya bisa dihapus jika statusnya "Menunggu" atau "Ditolak".');
            return back();
        }

        $krs->delete();
        Alert::success('Berhasil', 'KRS berhasil dihapus.');
        return back();
    }
}
