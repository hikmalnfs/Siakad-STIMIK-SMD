<?php

namespace App\Http\Controllers\Dosen\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Akademik\Krs;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Akademik\Kelas;

class PengajuanKrsController extends Controller
{
    public function index()
    {
        $dosen = Auth::guard('dosen')->user();

        // Ambil semua mahasiswa yang berada di kelas yang diampu dosen sebagai wali
        $mahasiswaIds = Mahasiswa::whereHas('kelas', function ($q) use ($dosen) {
            $q->where('wali_id', $dosen->id);
        })->pluck('id');

        // Ambil pengajuan KRS terakhir per mahasiswa
        $latestKrsIds = Krs::select(DB::raw('MAX(id) as id'))
            ->whereIn('users_id', $mahasiswaIds)
            ->groupBy('users_id')
            ->pluck('id');

        $pengajuans = Krs::with([
                'mahasiswa.kelas.wali',
                'mahasiswa.kelas.pstudi',
                'jadwal.mataKuliah',
                'jadwal.jenisKelas',
                'jadwal.tahunAkademik'
            ])
            ->whereIn('id', $latestKrsIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dosen.pengajuan-krs.index', compact('pengajuans'));
    }

    public function show(int $id)
    {
        $dosen = Auth::guard('dosen')->user();

        $pengajuan = Krs::with(['mahasiswa.kelas.wali', 'mahasiswa.kelas.pstudi'])
            ->findOrFail($id);

        // Validasi dosen adalah wali kelas dari mahasiswa terkait
        if ($pengajuan->mahasiswa->kelas->wali_id !== $dosen->id) {
            abort(403, 'Anda bukan wali mahasiswa ini.');
        }

        $semuaPengajuan = Krs::with([
                'jadwal.mataKuliah',
                'jadwal.jenisKelas',
                'jadwal.tahunAkademik',
            ])
            ->where('users_id', $pengajuan->users_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dosen.pengajuan-krs.show', compact('pengajuan', 'semuaPengajuan'));
    }

    public function approve(int $id)
    {
        $dosen = Auth::guard('dosen')->user();
        $pengajuan = Krs::with('mahasiswa.kelas')->findOrFail($id);

        if ($pengajuan->mahasiswa->kelas->wali_id !== $dosen->id) {
            abort(403, 'Anda bukan wali mahasiswa ini.');
        }

        if ($pengajuan->status !== 'Menunggu') {
            Alert::warning('Sudah Diproses', 'Pengajuan sudah diproses sebelumnya.');
            return redirect()->route('dosen.pengajuan-krs.show', $pengajuan->id);
        }

        $pengajuan->update([
            'status' => 'Disetujui',
            'keterangan' => ''
        ]);

        Alert::success('Disetujui', 'Pengajuan KRS berhasil disetujui.');
        return redirect()->route('dosen.pengajuan-krs.show', $pengajuan->id);
    }

    public function reject(Request $request, int $id)
    {
        $dosen = Auth::guard('dosen')->user();
        $request->validate(['keterangan' => 'nullable|string|max:255']);

        $pengajuan = Krs::with('mahasiswa.kelas')->findOrFail($id);

        if ($pengajuan->mahasiswa->kelas->wali_id !== $dosen->id) {
            abort(403, 'Anda bukan wali mahasiswa ini.');
        }

        if ($pengajuan->status !== 'Menunggu') {
            Alert::warning('Sudah Diproses', 'Pengajuan sudah diproses sebelumnya.');
            return redirect()->route('dosen.pengajuan-krs.show', $pengajuan->id);
        }

        $pengajuan->update([
            'status' => 'Ditolak',
            'keterangan' => $request->input('keterangan', '')
        ]);

        Alert::error('Ditolak', 'Pengajuan KRS berhasil ditolak.');
        return redirect()->route('dosen.pengajuan-krs.show', $pengajuan->id);
    }

    public function edit(int $id)
    {
        $dosen = Auth::guard('dosen')->user();
        $pengajuan = Krs::with([
            'mahasiswa.kelas.pstudi',
            'jadwal.mataKuliah',
            'jadwal.jenisKelas',
            'jadwal.tahunAkademik'
        ])->findOrFail($id);

        if ($pengajuan->mahasiswa->kelas->wali_id !== $dosen->id) {
            abort(403, 'Anda bukan wali mahasiswa ini.');
        }

        return view('dosen.pengajuan-krs.edit', compact('pengajuan'));
    }

    public function update(Request $request, int $id)
    {
        $dosen = Auth::guard('dosen')->user();

        $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,Ditolak',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $pengajuan = Krs::with('mahasiswa.kelas')->findOrFail($id);

        if ($pengajuan->mahasiswa->kelas->wali_id !== $dosen->id) {
            abort(403, 'Anda bukan wali mahasiswa ini.');
        }

        $pengajuan->update([
            'status' => $request->input('status'),
            'keterangan' => $request->input('keterangan', '')
        ]);

        Alert::success('Berhasil', 'Pengajuan KRS berhasil diperbarui.');
        return redirect()->route('dosen.pengajuan-krs.show', $pengajuan->id);
    }
}
