<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AbsensiMahasiswa;
use App\Models\AbsensiStatus;
use App\Models\Akademik\JadwalKuliah;
use App\Models\Mahasiswa;
use App\Exports\AbsensiExport;
use App\Imports\AbsensiImport;
use Maatwebsite\Excel\Facades\Excel;



class AbsensikuliahController extends Controller
{
    public function index($id)
    {
        $user = Auth::user();
        $spref = $user ? $user->prefix : '';

        $jadwal = JadwalKuliah::with(['mataKuliah', 'kelas', 'dosen'])->findOrFail($id);
        $absensi_status = AbsensiStatus::where('jadkul_code', $jadwal->code)
            ->orderBy('pertemuan')
            ->get();

        $pages = 'Absensi';
        $menus = 'Akademik';
        $academy = 'SIAKAD';

        return view('master.akademik.absensi.index', compact(
            'jadwal', 'absensi_status', 'pages', 'menus', 'academy', 'user', 'spref'
        ));
    }

    public function showPertemuan($jadwal_kuliah_id, $pertemuan)
    {
        $user = Auth::user();
        $spref = $user ? $user->prefix : '';

        $jadwal = JadwalKuliah::with(['mataKuliah', 'kelas'])->findOrFail($jadwal_kuliah_id);

        $status = AbsensiStatus::where('jadkul_code', $jadwal->code)
            ->where('pertemuan', $pertemuan)
            ->first();

        $mahasiswas = Mahasiswa::whereHas('krs', function ($q) use ($jadwal) {
            $q->where('jadwal_id', $jadwal->id);
        })->get();

        $absensi_mahasiswa = AbsensiMahasiswa::where('jadkul_code', $jadwal->code)
            ->where('pertemuan', $pertemuan)
            ->get()
            ->keyBy('author_id'); // ✅ Harus sesuai kolom DB

        $pages = 'Absensi';
        $menus = 'Akademik';
        $academy = 'SIAKAD';

        return view('master.akademik.absensi.show', compact(
            'jadwal', 'pertemuan', 'mahasiswas', 'absensi_mahasiswa', 'status', 'pages', 'menus', 'academy', 'user', 'spref'
        ));
    }

    public function store(Request $request, $jadwal_kuliah_id, $pertemuan)
    {
        $jadwal = JadwalKuliah::findOrFail($jadwal_kuliah_id);
        $data = $request->input('absensi', []);
        // \Log::info('Data Absensi Diterima:', $request->all());

        $user = Auth::user();
        $isSuperAdmin = $user && $user->role === 'superadmin';

        $status = AbsensiStatus::where('jadkul_code', $jadwal->code)
            ->where('pertemuan', $pertemuan)
            ->first();

        if (!$isSuperAdmin && (!$status || !$status->is_active)) {
            // \Log::warning("Absensi dikunci, user tidak dapat menyimpan data absensi pertemuan $pertemuan jadwal {$jadwal->code}");
            return redirect()->back()->with('error', 'Absensi untuk pertemuan ini sudah dikunci dan tidak dapat diubah.');
        }

        $mapAbsenType = [
            'Hadir' => 'H',
            'Sakit' => 'S',
            'Izin' => 'I',
            'Alpha' => 'A',
        ];

        foreach ($data as $mahasiswa_id => $absen_type) {
            // \Log::info("Proses absensi mahasiswa_id: $mahasiswa_id, absen_type: $absen_type");

            if (!in_array($absen_type, array_keys($mapAbsenType)) && $absen_type !== 'Belum Absen') {
                // \Log::warning("Status absen '$absen_type' tidak valid untuk mahasiswa_id $mahasiswa_id, dilewati.");
                continue;
            }

            if ($absen_type == 'Belum Absen') {
                $deleted = AbsensiMahasiswa::where([
                    'jadkul_code' => $jadwal->code,
                    'pertemuan' => $pertemuan,
                    'author_id' => $mahasiswa_id,
                ])->delete();

                // \Log::info("Hapus absensi untuk mahasiswa_id $mahasiswa_id, baris dihapus: $deleted");
                continue;
            }

            $absen_type_code = $mapAbsenType[$absen_type];

            $record = AbsensiMahasiswa::where('jadkul_code', $jadwal->code)
                ->where('pertemuan', $pertemuan)
                ->where('author_id', $mahasiswa_id)
                ->first();

            if ($record) {
                $updated = $record->update([
                    'absen_type' => $absen_type_code,
                    'absen_date' => now()->toDateString(),
                    'absen_time' => now()->toTimeString(),
                    'absen_desc' => $isSuperAdmin ? 'Diubah oleh superadmin' : 'Diinput oleh dosen',
                    'absen_proof' => '-',
                ]);
                // \Log::info("Update record absensi mahasiswa_id $mahasiswa_id: " . ($updated ? 'Berhasil' : 'Gagal'));
            } else {
                $created = AbsensiMahasiswa::create([
                    'jadkul_code' => $jadwal->code,
                    'pertemuan' => $pertemuan,
                    'author_id' => $mahasiswa_id,
                    'absen_type' => $absen_type_code,
                    'absen_date' => now()->toDateString(),
                    'absen_time' => now()->toTimeString(),
                    'absen_desc' => $isSuperAdmin ? 'Diubah oleh superadmin' : 'Diinput oleh dosen',
                    'code' => 'ABS-' . strtoupper(Str::random(8)),
                    'absen_proof' => '-',
                ]);
                // \Log::info("Create record absensi mahasiswa_id $mahasiswa_id: ID " . ($created->id ?? 'NULL'));
            }
        }

        // Update status absensi
        if ($status) {
            $updatedStatus = $status->update([
                'status' => 'Sudah Diisi',
            ]);
            // \Log::info("Update status absensi pertemuan $pertemuan jadwal {$jadwal->code}: " . ($updatedStatus ? 'Berhasil' : 'Gagal'));
        } else {
            $newStatus = AbsensiStatus::create([
                'jadkul_code' => $jadwal->code,
                'pertemuan' => $pertemuan,
                'status' => 'Sudah Diisi',
                'is_active' => true,
            ]);
            // \Log::info("Create status absensi baru pertemuan $pertemuan jadwal {$jadwal->code}: ID " . ($newStatus->id ?? 'NULL'));
        }

        return redirect()->back()->with('success', 'Absensi berhasil disimpan.');
    }

    /** ============================ */
    /** FUNGSI KHUSUS UNTUK SUPERADMIN */
    /** ============================ */

    // Buka kunci absensi (is_active = true)
    public function unlockAbsensi($jadwal_kuliah_id, $pertemuan)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat membuka absensi.');
        }

        $jadwal = JadwalKuliah::findOrFail($jadwal_kuliah_id);
        
        AbsensiStatus::updateOrCreate(
            [
                'jadkul_code' => $jadwal->code,
                'pertemuan' => $pertemuan,
            ],
            [
                'is_active' => true,
                'status' => 'Dibuka kembali oleh superadmin',
            ]
        );

        return redirect()->back()->with('success', 'Absensi berhasil dibuka kembali.');
    }

    // Kunci absensi (is_active = false)
    public function lockAbsensi($jadwal_kuliah_id, $pertemuan)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengunci absensi.');
        }

        $jadwal = JadwalKuliah::findOrFail($jadwal_kuliah_id);

        AbsensiStatus::updateOrCreate(
            [
                'jadkul_code' => $jadwal->code,
                'pertemuan' => $pertemuan,
            ],
            [
                'is_active' => false,
                'status' => 'Dikunci oleh superadmin',
            ]
        );

        return redirect()->back()->with('success', 'Absensi berhasil dikunci.');
    }

    /** ============================ */
    /** FUNGSI BIASA LAINNYA */
    /** ============================ */

    public function openPertemuan($jadwal_id, $pertemuan)
    {
        $user = Auth::user();
        if ($user && $user->role === 'superadmin') {
            return redirect()->back()->with('error', 'Superadmin harus menggunakan fitur unlockAbsensi untuk membuka absensi.');
        }

        $jadwal = JadwalKuliah::findOrFail($jadwal_id);

        AbsensiStatus::updateOrCreate(
            [
                'jadkul_code' => $jadwal->code,
                'pertemuan' => $pertemuan,
            ],
            [
                'is_active' => true,
                'status' => 'Dibuka oleh dosen',
            ]
        );

        return redirect()->back()->with('success', 'Pertemuan ' . $pertemuan . ' telah dibuka untuk absensi.');
    }

    public function lockPertemuan($jadwal_id, $pertemuan)
    {
        $user = Auth::user();
        if ($user && $user->role === 'superadmin') {
            return redirect()->back()->with('error', 'Superadmin harus menggunakan fitur lockAbsensi untuk mengunci absensi.');
        }

        $jadwal = JadwalKuliah::findOrFail($jadwal_id);

        AbsensiStatus::updateOrCreate(
            [
                'jadkul_code' => $jadwal->code,
                'pertemuan' => $pertemuan,
            ],
            [
                'is_active' => false,
                'status' => 'Dikunci oleh dosen',
            ]
        );

        return redirect()->back()->with('success', 'Pertemuan ' . $pertemuan . ' telah ditutup untuk absensi.');
    }

    public function export(Request $request, $jadwal_id)
    {
        $pertemuan = $request->query('pertemuan'); // opsional, bisa export semua atau per pertemuan

        $jadwal = JadwalKuliah::findOrFail($jadwal_id);

        $fileName = 'absensi_' . $jadwal->code;
        if ($pertemuan) {
            $fileName .= '_pertemuan_' . $pertemuan;
        }
        $fileName .= '.xlsx';

        return Excel::download(new AbsensiExport($jadwal->code, $pertemuan), $fileName);
    }

    public function import(Request $request, $jadwal_id)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'pertemuan' => 'required|integer|min:1',
        ]);

        $jadwal = JadwalKuliah::findOrFail($jadwal_id);
        $pertemuan = $request->input('pertemuan');

        try {
            Excel::import(new AbsensiImport($jadwal->code, $pertemuan), $request->file('file'));

            // Update status absensi setelah import
            \App\Models\AbsensiStatus::updateOrCreate(
                [
                    'jadkul_code' => $jadwal->code,
                    'pertemuan' => $pertemuan,
                ],
                [
                    'status' => 'Sudah Diisi',
                    'is_active' => true,
                ]
            );

            return redirect()->back()->with('success', 'Data absensi berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    // Fungsi ini redundant, sudah digantikan oleh store()
    public function storePertemuan(Request $request, $jadwal_kuliah_id, $pertemuan)
    {
        return $this->store($request, $jadwal_kuliah_id, $pertemuan);
    }
    public function cetakKosongPdf($jadwal_id)
    {
        $jadwal = JadwalKuliah::with(['mataKuliah', 'kelas', 'dosen', 'waktuKuliah'])->findOrFail($jadwal_id);

        $mahasiswas = Mahasiswa::whereHas('krs', function ($query) use ($jadwal) {
            $query->where('jadwal_id', $jadwal->id);
        })->orderBy('name')->get();

        $pdf = PDF::loadView('master.akademik.absensi.cetak-kosong', compact('jadwal', 'mahasiswas'));

        return $pdf->stream('dosen-absensi-kosong-'.$jadwal->id.'.pdf');
    }

}
