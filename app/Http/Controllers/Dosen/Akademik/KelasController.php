<?php

namespace App\Http\Controllers\Dosen\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

use App\Models\Akademik\JadwalKuliah;
use App\Models\AbsensiMahasiswa;
use App\Models\AbsensiStatus;
use App\Models\Nilai;
use App\Models\Pengaturan\WebSetting;
use App\Models\Akademik\TahunAkademik;
use App\Exports\KelasExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $dosenId = Auth::guard('dosen')->user()->id;
        $tahunAkademikList = TahunAkademik::orderBy('start_date', 'desc')->get();

        $jadwals = JadwalKuliah::where('dosen_id', $dosenId)
            ->with(['mataKuliah', 'kelas.mahasiswas'])
            ->when($request->tahun_akademik_id, fn($q) => $q->where('tahun_akademik_id', $request->tahun_akademik_id))
            ->when($request->semester, fn($q) => $q->whereHas('mataKuliah', fn($mq) => $mq->where('semester', $request->semester)))
            ->get();

        $web = WebSetting::find(1);

        return view('dosen.akademik.kelas-index', compact('jadwals', 'web', 'tahunAkademikList'));
    }

    public function viewAbsensi($id)
    {
        $dosenId = Auth::guard('dosen')->user()->id;

        $jadwal = JadwalKuliah::where('id', $id)
            ->where('dosen_id', $dosenId)
            ->with(['kelas.mahasiswas', 'nilai'])
            ->firstOrFail();

        $absens = AbsensiMahasiswa::where('jadkul_code', $jadwal->code)->get();

        $mahasiswas = collect();
        foreach ($jadwal->kelas as $kelas) {
            $mahasiswas = $mahasiswas->merge($kelas->mahasiswas);
        }
        $mahasiswas = $mahasiswas->unique('id')->values();

        $totalPertemuan = 16;
        $absensiStatus = AbsensiStatus::where('jadkul_code', $jadwal->code)->get();

        $absensiData = [];
        foreach ($absens as $absen) {
            $absensiData[$absen->mahasiswa_id][$absen->pertemuan] = $absen->absen_type;
        }

        $web = WebSetting::find(1);

        return view('dosen.akademik.kelas-absensi', compact(
            'jadwal',
            'web',
            'mahasiswas',
            'absensiData',
            'totalPertemuan',
            'absensiStatus'
        ));
    }

    public function saveAbsensi(Request $request, $jadwalId)
    {
        $request->validate([
            'absensi' => 'required|array',
        ]);

        $jadwal = JadwalKuliah::findOrFail($jadwalId);
        $jadwalCode = $jadwal->code;

        foreach ($request->absensi as $mahasiswaId => $pertemuanData) {
            foreach ($pertemuanData as $pertemuanKe => $status) {
                AbsensiMahasiswa::updateOrCreate(
                    [
                        'jadkul_code'   => $jadwalCode,
                        'author_id'     => $mahasiswaId,
                        'pertemuan'     => $pertemuanKe,
                    ],
                    [
                        'code'          => 'ABS-' . strtoupper(Str::random(8)),
                        'absen_type'    => $status ?? 'A',
                        'absen_proof'   => '-', // default: tidak upload bukti
                        'absen_date'    => now()->toDateString(),
                        'absen_time'    => now()->toTimeString(),
                        'absen_desc'    => 'Diinput oleh dosen',
                        'updated_at'    => now(),
                    ]
                );

            }
        }

    Alert::success('Sukses', 'Absensi mahasiswa berhasil disimpan');
    return back();
}

    public function updateAbsensi(Request $request, $code)
    {
        $request->validate([
            'absen_type' => 'required|in:H,S,I,A',
            'absen_desc' => 'nullable|string',
            'absen_proof' => 'nullable|image|max:2048',
        ]);

        $absen = AbsensiMahasiswa::where('code', $code)->firstOrFail();
        $absen->absen_type = $request->absen_type;
        $absen->absen_desc = $request->absen_desc;

        if ($request->hasFile('absen_proof')) {
            $file = $request->file('absen_proof');
            $filename = uniqid('absen_') . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/images', $filename);
            $absen->absen_proof = $filename;
        }

        $absen->save();

        Alert::success('Sukses', 'Data absensi diperbarui');
        return back();
    }

    public function bukaAbsensi($jadkul_code, $pertemuan)
    {
        AbsensiStatus::updateOrCreate(
            ['jadkul_code' => $jadkul_code, 'pertemuan' => $pertemuan],
            ['is_active' => true, 'status' => 'buka']
        );

        Alert::success('Sukses', 'Pertemuan ke-' . $pertemuan . ' dibuka untuk absensi.');
        return back();
    }

    public function tutupAbsensi($jadkul_code, $pertemuan)
    {
        AbsensiStatus::updateOrCreate(
            ['jadkul_code' => $jadkul_code, 'pertemuan' => $pertemuan],
            ['is_active' => false, 'status' => 'tutup']
        );

        Alert::info('Info', 'Pertemuan ke-' . $pertemuan . ' telah ditutup.');
        return back();
    }

    public function toggleAbsensi(Request $request, $jadwalId, $pertemuan)
    {
        $jadwal = JadwalKuliah::findOrFail($jadwalId);

        $status = AbsensiStatus::firstOrNew([
            'jadkul_code' => $jadwal->code,
            'pertemuan' => $pertemuan,
        ]);

        $status->is_active = !$status->is_active;
        $status->status = $status->is_active ? 'buka' : 'tutup';

        $status->save();

        Alert::success('Berhasil', 'Status absensi pertemuan diperbarui');
        return back();
    }

    public function viewNilai($id)
    {
        $dosenId = Auth::guard('dosen')->user()->id;

        $jadwal = JadwalKuliah::where('id', $id)
            ->where('dosen_id', $dosenId)
            ->with(['kelas.mahasiswas', 'nilai'])
            ->firstOrFail();

        $web = WebSetting::find(1);

        return view('dosen.akademik.kelas-nilai', compact('jadwal', 'web'));
    }

    public function updateNilai(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|array',
            'nilai.*.mahasiswa_id' => 'required|exists:mahasiswas,id',
            'nilai.*.nilai_angka' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($request->nilai as $item) {
            Nilai::updateOrCreate(
                [
                    'jadwal_kuliah_id' => $id,
                    'mahasiswa_id' => $item['mahasiswa_id'],
                ],
                [
                    'nilai_angka' => $item['nilai_angka'],
                    'updated_at' => now(),
                ]
            );
        }

        Alert::success('Sukses', 'Nilai mahasiswa berhasil diperbarui');
        return back();
    }

    public function viewMahasiswa($id)
    {
        $dosenId = Auth::guard('dosen')->user()->id;

        $jadwal = JadwalKuliah::where('id', $id)
            ->where('dosen_id', $dosenId)
            ->with(['kelas.mahasiswas.programStudi'])
            ->firstOrFail();

        $mahasiswas = collect();
        foreach ($jadwal->kelas as $kelas) {
            $mahasiswas = $mahasiswas->merge($kelas->mahasiswas);
        }

        $mahasiswas = $mahasiswas->unique('id')->values();
        $web = WebSetting::find(1);

        return view('dosen.akademik.kelas-mahasiswa', compact('jadwal', 'mahasiswas', 'web'));
    }

    public function exportPdf(Request $request)
    {
        $dosenId = Auth::guard('dosen')->user()->id;

        $jadwals = JadwalKuliah::where('dosen_id', $dosenId)
            ->with(['mataKuliah', 'kelas'])
            ->when($request->tahun_akademik_id, fn($q) => $q->where('tahun_akademik_id', $request->tahun_akademik_id))
            ->when($request->semester, fn($q) => $q->whereHas('mataKuliah', fn($mq) => $mq->where('semester', $request->semester)))
            ->get();

        $pdf = Pdf::loadView('dosen.akademik.kelas-pdf', [
            'jadwals' => $jadwals
        ])->setPaper('a4', 'landscape');

        return $pdf->download('kelas-dosen.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new KelasExport($request), 'kelas-dosen.xlsx');
    }
}
