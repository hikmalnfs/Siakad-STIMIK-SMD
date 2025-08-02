<?php

namespace App\Http\Controllers\Dosen\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Nilai;
use App\Models\Akademik\JadwalKuliah;
use App\Models\Pengaturan\WebSetting;
use App\Models\Mahasiswa;

class NilaiController extends Controller
{
    private function konversiNilaiHuruf($nilai)
    {
        if ($nilai >= 93) return 'A+';
        if ($nilai >= 90) return 'A';
        if ($nilai >= 87) return 'A-';
        if ($nilai >= 83) return 'B+';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 77) return 'B-';
        if ($nilai >= 73) return 'C+';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 67) return 'C-';
        if ($nilai >= 63) return 'D+';
        if ($nilai >= 60) return 'D';
        return 'E';
    }

    public function list(Request $request)
    {
        $dosenId = Auth::guard('dosen')->user()->id;
        $semester = $request->get('semester');
        
        $jadwals = JadwalKuliah::with(['mataKuliah', 'kelas', 'tahunAkademik', 'dosen'])
            ->where('dosen_id', $dosenId)
            ->when($semester, function ($query, $type) {
                $query->whereHas('tahunAkademik', function ($q) use ($type) {
                    $q->where('type', strtolower($type));
                });
            })
            ->get();

        $listSemester = ['ganjil' => 'Ganjil', 'genap' => 'Genap'];
        $web = WebSetting::find(1);

        return view('dosen.nilai.list', compact('jadwals', 'listSemester', 'web'));
    }

    public function show($id)
    {
        $dosenId = Auth::guard('dosen')->user()->id;

        $jadwal = JadwalKuliah::with(['mataKuliah', 'kelas', 'dosen', 'tahunAkademik'])
            ->where('id', $id)
            ->where('dosen_id', $dosenId)
            ->firstOrFail();

        $kelasIds = $jadwal->kelas->pluck('id')->toArray();

        $mahasiswas = Mahasiswa::with(['kelas', 'taka'])
            ->whereHas('kelas', function ($q) use ($kelasIds) {
                $q->whereIn('kelas_id', $kelasIds);
            })
            ->get();

        // Map nilai ke mahasiswa
        $items = $mahasiswas->map(function ($mhs) use ($jadwal) {
            $nilai = Nilai::where('jadwal_kuliah_id', $jadwal->id)
                          ->where('mahasiswa_id', $mhs->id)
                          ->first();
            $mhs->nilai_absen = $nilai->nilai_absen ?? null;
            $mhs->nilai_tugas = $nilai->nilai_tugas ?? null;
            $mhs->nilai_uts = $nilai->nilai_uts ?? null;
            $mhs->nilai_uas = $nilai->nilai_uas ?? null;
            $mhs->nilai_akhir = $nilai->nilai_akhir ?? null;
            $mhs->nilai_huruf = $nilai->nilai_huruf ?? null;
            return $mhs;
        });

        $ta = $jadwal->tahunAkademik ?? null;
        $web = WebSetting::find(1);
        $tgl = date('d-m-Y');

        return view('dosen.nilai.show', compact('jadwal', 'items', 'ta', 'web', 'tgl'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalKuliah::findOrFail($id);

        if ($jadwal->nilai_locked) {
            return redirect()->route('dosen.nilai.show', $id)->with('error', 'Nilai sudah dikunci dan tidak dapat diubah.');
        }

        $request->validate([
            'idMhs' => 'required|array',
            'nilai_absen' => 'array',
            'nilai_tugas' => 'array',
            'nilai_uts'   => 'array',
            'nilai_uas'   => 'array',
        ]);

        $bobot_absen = $jadwal->bobot_absen ?? 10;
        $bobot_tugas = $jadwal->bobot_tugas ?? 30;
        $bobot_uts   = $jadwal->bobot_uts ?? 30;
        $bobot_uas   = $jadwal->bobot_uas ?? 30;

        foreach ($request->input('idMhs') as $index => $mahasiswaId) {
            $absen = is_numeric($request->input('nilai_absen')[$index] ?? null) ? floatval($request->input('nilai_absen')[$index]) : 0;
            $tugas = is_numeric($request->input('nilai_tugas')[$index] ?? null) ? floatval($request->input('nilai_tugas')[$index]) : 0;
            $uts   = is_numeric($request->input('nilai_uts')[$index] ?? null) ? floatval($request->input('nilai_uts')[$index]) : 0;
            $uas   = is_numeric($request->input('nilai_uas')[$index] ?? null) ? floatval($request->input('nilai_uas')[$index]) : 0;

            $nilaiAkhir = ($absen * $bobot_absen + $tugas * $bobot_tugas + $uts * $bobot_uts + $uas * $bobot_uas) / 100;
            $nilaiHuruf = $this->konversiNilaiHuruf($nilaiAkhir);

            $nilai = Nilai::firstOrNew([
                'jadwal_kuliah_id' => $id,
                'mahasiswa_id' => $mahasiswaId,
            ]);

            $nilai->nilai_absen = $absen;
            $nilai->nilai_tugas = $tugas;
            $nilai->nilai_uts   = $uts;
            $nilai->nilai_uas   = $uas;
            $nilai->nilai_akhir = round($nilaiAkhir, 2);
            $nilai->nilai_huruf = $nilaiHuruf;
            $nilai->save();
        }

        return redirect()->route('dosen.nilai.show', $id)->with('status', 'Nilai draft berhasil disimpan.');
    }

    public function updateKomposisi(Request $request, $id)
    {
        $request->validate([
            'bobot_absen' => 'required|numeric|min:0|max:100',
            'bobot_tugas' => 'required|numeric|min:0|max:100',
            'bobot_uts'   => 'required|numeric|min:0|max:100',
            'bobot_uas'   => 'required|numeric|min:0|max:100',
        ]);

        $jadwal = JadwalKuliah::findOrFail($id);

        $jadwal->bobot_absen = $request->bobot_absen;
        $jadwal->bobot_tugas = $request->bobot_tugas;
        $jadwal->bobot_uts   = $request->bobot_uts;
        $jadwal->bobot_uas   = $request->bobot_uas;
        $jadwal->save();

        return redirect()->route('dosen.nilai.show', $id)->with('status', 'Komposisi bobot berhasil diperbarui.');
    }

    public function ajukanKeBaak($id)
    {
        $jadwal = JadwalKuliah::findOrFail($id);

        if ($jadwal->dosen_id !== Auth::guard('dosen')->id()) {
            abort(403);
        }

        if ($jadwal->nilai_locked) {
            return back()->with('error', 'Nilai sudah dikunci dan tidak bisa diajukan.');
        }

        $jadwal->nilai_submitted = true;
        $jadwal->nilai_locked = true;
        $jadwal->save();

        return back()->with('status', 'Nilai berhasil diajukan dan dikunci.');
    }

    public function bukaKunciNilai($id)
    {
        if (!Gate::allows('superadmin')) {
            abort(403, "Hanya superadmin yang boleh membuka kunci nilai.");
        }

        $jadwal = JadwalKuliah::findOrFail($id);
        $jadwal->nilai_locked = false;
        $jadwal->nilai_submitted = false;
        $jadwal->save();

        return redirect()->route('dosen.nilai.show', $id)->with('status', 'Kunci nilai berhasil dibuka oleh superadmin.');
    }

    public function cetakDosen($id)
    {
        $dosenId = Auth::guard('dosen')->user()->id;

        $jadwal = JadwalKuliah::with(['mataKuliah', 'kelas', 'dosen', 'tahunAkademik'])
            ->where('id', $id)->where('dosen_id', $dosenId)->firstOrFail();

        $kelasIds = $jadwal->kelas->pluck('id')->toArray();

        $mahasiswas = Mahasiswa::with(['kelas', 'taka'])
            ->whereHas('kelas', function ($q) use ($kelasIds) {
                $q->whereIn('kelas_id', $kelasIds);
            })
            ->get();

        $items = $mahasiswas->map(function ($mhs) use ($jadwal) {
            $nilai = Nilai::where('jadwal_kuliah_id', $jadwal->id)->where('mahasiswa_id', $mhs->id)->first();
            $mhs->nilai_absen = $nilai->nilai_absen ?? null;
            $mhs->nilai_tugas = $nilai->nilai_tugas ?? null;
            $mhs->nilai_uts = $nilai->nilai_uts ?? null;
            $mhs->nilai_uas = $nilai->nilai_uas ?? null;
            $mhs->nilai_akhir = $nilai->nilai_akhir ?? null;
            $mhs->nilai_huruf = $nilai->nilai_huruf ?? null;
            return $mhs;
        });

        $ta = $jadwal->tahunAkademik ?? null;
        $tgl = date('d-m-Y');

        return view('dosen.nilai.cetak', compact('jadwal', 'items', 'ta', 'tgl'));
    }
}
