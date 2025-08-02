<?php
namespace App\Http\Controllers\Dosen\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Mahasiswa;
use App\Models\Akademik\Khs;
use App\Models\Akademik\Krs;
use App\Models\Akademik\JadwalKuliah;
use App\Models\Akademik\TahunAkademik;
use RealRashid\SweetAlert\Facades\Alert;

class ManageMahasiswaController extends Controller
{
    // List mahasiswa bimbingan dengan statistik
    public function index(Request $request)
    {
        $waliId = Auth::guard('dosen')->user()->id;

        $query = Mahasiswa::with(['kelas'])
            ->whereHas('kelas', fn($q) => $q->where('wali_id', $waliId));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('numb_nim', 'like', "%$search%");
            });
        }

        $mahasiswas = $query->get()->map(function ($mhs) {
            $khs = Khs::where('mahasiswa_id', $mhs->id)->get();
            $sks = $khs->sum('jumlah_sks');
            $ipk = round($khs->avg('ipk') ?? 0, 2);
            $semesterTerakhir = $khs->max('semester') ?? 0;

            $mhs->ipk = $ipk;
            $mhs->sks = $sks;
            $mhs->semester_terakhir = $semesterTerakhir;
            $mhs->status_studi = $this->statusAkademik($semesterTerakhir);
            $mhs->rawan_do = ($ipk < 2.00 || $sks < $semesterTerakhir * 10);
            return $mhs;
        });

        $jumlahMahasiswa = $mahasiswas->count();
        $mahasiswaRawanDO = $mahasiswas->where('rawan_do', true)->count();
        $rataIpk = round($mahasiswas->avg('ipk') ?? 0, 2);

        return view('dosen.mahasiswa-bimbingan.index', compact(
            'mahasiswas', 'jumlahMahasiswa', 'rataIpk', 'mahasiswaRawanDO'
        ));
    }

    // Detail mahasiswa + daftar KRS dan KHS
    public function show($id)
    {
        $mahasiswa = Mahasiswa::with([
            'kelas',
            'krs.jadwal.mataKuliah',
            'khs',
        ])->findOrFail($id);

        $tahunAktif = TahunAkademik::where('status', 'Aktif')->first();
        $krsAktif = Krs::where('users_id', $mahasiswa->id)
            ->where('tahun_akademik_id', optional($tahunAktif)->id)
            ->get();

        $khs = $mahasiswa->khs->sortBy('semester');
        $ipk = round($khs->avg('ipk') ?? 0, 2);

        $ipList = $khs->map(fn ($k) => [
            'semester' => $k->semester,
            'ip' => $k->ip_semester ?? 0,
        ]);

        $sksLulus = $khs->sum('jumlah_sks') ?? 0;
        $sksTotal = $khs->sum('jumlah_sks') ?? 0;
        $semesterCount = $khs->count();
        $avgSks = $semesterCount > 0 ? round($sksLulus / $semesterCount, 1) : 0;

        return view('dosen.mahasiswa-bimbingan.show', compact(
            'mahasiswa', 'krsAktif', 'khs', 'ipList', 'ipk',
            'sksLulus', 'sksTotal', 'semesterCount', 'avgSks'
        ));
    }
     // Form tambah KRS baru (pilih mata kuliah jadwal)
    public function createKrs($mahasiswaId)
    {
        $waliId = Auth::guard('dosen')->user()->id;

        $mahasiswa = Mahasiswa::whereHas('kelas', fn($q) => $q->where('wali_id', $waliId))
            ->with('prodi', 'kelas')
            ->findOrFail($mahasiswaId);

        $tahunAktif = TahunAkademik::where('status', 'Aktif')->first();

        $jadwalKuliah = JadwalKuliah::with(['mataKuliah', 'kelas', 'dosen'])
            ->where('tahun_akademik_id', optional($tahunAktif)->id)
            ->whereHas('kelas', function ($q) use ($waliId) {
                $q->where('wali_id', $waliId);
            })
            ->get();

        // Group berdasarkan mata_kuliah_id
        $grouped = $jadwalKuliah->groupBy('mata_kuliah_id');

        // Inisialisasi array untuk simpan jadwal valid
        $validJadwal = collect();

        // Fungsi bantu cek bentrok jadwal
        $isBentrok = function ($jadwal1, $jadwal2) {
            if ($jadwal1->hari != $jadwal2->hari) return false;
            return
                ($jadwal1->waktu_mulai < $jadwal2->waktu_selesai) &&
                ($jadwal2->waktu_mulai < $jadwal1->waktu_selesai);
        };

        foreach ($grouped as $mataKuliahId => $jadwals) {
            // Pilih jadwal pertama sebagai default
            $jadwalTerpilih = null;

            foreach ($jadwals as $jadwal) {
                $bentrok = false;

                // Cek jadwal ini bentrok dengan jadwal yg sudah dipilih sebelumnya
                foreach ($validJadwal as $vjadwal) {
                    if ($isBentrok($jadwal, $vjadwal)) {
                        $bentrok = true;
                        break;
                    }
                }

                if (!$bentrok) {
                    $jadwalTerpilih = $jadwal;
                    break;
                }
            }

            // Jika tidak ada jadwal bebas bentrok, pakai jadwal pertama (pilihan fallback)
            if (!$jadwalTerpilih) {
                $jadwalTerpilih = $jadwals->first();
            }

            $validJadwal->push($jadwalTerpilih);
        }

        // Urutkan berdasarkan semester
        $validJadwal = $validJadwal->sortBy(fn($item) => $item->mataKuliah->semester ?? 0);

        $daftarSemester = $validJadwal->pluck('mataKuliah.semester')->unique()->sort()->values();

        return view('dosen.mahasiswa-bimbingan.create-krs', compact(
            'mahasiswa',
            'validJadwal',
            'tahunAktif',
            'daftarSemester'
        ));
    }


    public function storeKrs(Request $request, $mahasiswaId)
    {
        $request->validate([
            'jadwal_ids' => 'required|array',
            'jadwal_ids.*' => 'integer|exists:jadwal_kuliahs,id',
        ]);

        $waliId = Auth::guard('dosen')->user()->id;
        $mahasiswa = Mahasiswa::whereHas('kelas', fn($q) => $q->where('wali_id', $waliId))
            ->findOrFail($mahasiswaId);

        $tahunAktif = TahunAkademik::where('status', 'Aktif')->first();
        if (!$tahunAktif) {
            Alert::error('Gagal', 'Tahun akademik aktif tidak ditemukan.');
            return back();
        }

        DB::beginTransaction();
        try {
            // Cek jadwal yang dipilih
            $jadwalDipilih = JadwalKuliah::whereIn('id', $request->jadwal_ids)->get();

            // Ambil jadwal yang sudah diambil sebelumnya
            $jadwalExisting = Krs::where('users_id', $mahasiswaId)
                ->where('tahun_akademik_id', $tahunAktif->id)
                ->pluck('jadwal_id')
                ->toArray();

            // Validasi bentrok jadwal
            foreach ($jadwalDipilih as $jadwal) {
                foreach ($jadwalExisting as $jadwalIdExist) {
                    $jadwalExist = JadwalKuliah::find($jadwalIdExist);
                    if ($jadwalExist && $jadwalExist->hari === $jadwal->hari) {
                        // Cek apakah waktu bertabrakan
                        if (
                            ($jadwal->waktu_mulai >= $jadwalExist->waktu_mulai && $jadwal->waktu_mulai < $jadwalExist->waktu_selesai) ||
                            ($jadwal->waktu_selesai > $jadwalExist->waktu_mulai && $jadwal->waktu_selesai <= $jadwalExist->waktu_selesai)
                        ) {
                            Alert::error('Gagal', "Jadwal bentrok pada mata kuliah {$jadwal->mataKuliah->name}.");
                            return back()->withInput();
                        }
                    }
                }
            }

            // Insert data baru
            foreach ($request->jadwal_ids as $jadwalId) {
                $exists = Krs::where('users_id', $mahasiswaId)
                    ->where('jadwal_id', $jadwalId)
                    ->where('tahun_akademik_id', $tahunAktif->id)
                    ->exists();

                if (!$exists) {
                    Krs::create([
                        'users_id' => $mahasiswaId,
                        'jadwal_id' => $jadwalId,
                        'tahun_akademik_id' => $tahunAktif->id,
                        'status' => 'pending', // default pending
                    ]);
                }
            }

            DB::commit();
            Alert::success('Berhasil', 'KRS berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollback();
            Alert::error('Gagal', 'Terjadi kesalahan saat menyimpan KRS.');
        }

        return redirect()->route('mahasiswa-bimbingan.show', $mahasiswaId);
    }

    public function updateKrsStatus(Request $request, $krsId)
    {
        $request->validate([
            'status' => 'required|in:pending,diterima,ditolak',
        ]);

        $krs = Krs::findOrFail($krsId);
        $waliId = Auth::guard('dosen')->user()->id;

        // Validasi apakah mahasiswa dari wali yang sah
        $mahasiswa = Mahasiswa::whereHas('kelas', fn($q) => $q->where('wali_id', $waliId))
            ->findOrFail($krs->users_id);

        $krs->status = $request->status;
        $krs->save();

        Alert::success('Berhasil', "Status KRS diubah menjadi {$request->status}.");
        return back();
    }

    // Lihat KHS mahasiswa per semester
    public function showKhs($mahasiswaId, Request $request)
    {
        $semester = $request->get('semester');
        $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);

        $khsQuery = Khs::where('mahasiswa_id', $mahasiswaId);
        if ($semester) {
            $khsQuery->where('semester', $semester);
        }
        $khs = $khsQuery->get();

        return view('dosen.mahasiswa-bimbingan.khs', compact('mahasiswa', 'khs', 'semester'));
    }

    // Fitur export KHS PDF (placeholder)
    public function exportKhsPdf($mahasiswaId, $semester)
    {
        // nanti integrasi dengan package dompdf/laravel-dompdf
        Alert::info('Segera Tersedia', 'Fitur export PDF KHS sedang dikembangkan.');
        return back();
    }

    // Update semester mahasiswa
    public function naikSemester(Request $request, $mahasiswaId)
    {
        $request->validate([
            'semester_baru' => 'required|integer|min:1',
        ]);

        $waliId = Auth::guard('dosen')->user()->id;
        $mahasiswa = Mahasiswa::whereHas('kelas', fn($q) => $q->where('wali_id', $waliId))
            ->findOrFail($mahasiswaId);

        $semesterBaru = $request->semester_baru;
        $semesterLama = $mahasiswa->semester_terakhir ?? 0;
        if ($semesterBaru <= $semesterLama) {
            Alert::warning('Tidak Valid', 'Semester baru harus lebih besar dari semester terakhir.');
            return back();
        }

        $mahasiswa->semester_terakhir = $semesterBaru;
        $mahasiswa->save();

        Alert::success('Berhasil', 'Semester mahasiswa berhasil diperbarui.');
        return redirect()->route('dosen.mahasiswa-bimbingan.show', $mahasiswaId);
    }

    // Update status akademik mahasiswa (aktif, cuti, do, lulus)
    public function updateStatusAkademik(Request $request, $mahasiswaId)
    {
        $request->validate([
            'status_akademik' => 'required|string|in:Aktif,Cuti,DO,Lulus,Drop Out',
            'catatan' => 'nullable|string|max:500',
        ]);

        $waliId = Auth::guard('dosen')->user()->id;
        $mahasiswa = Mahasiswa::whereHas('kelas', fn($q) => $q->where('wali_id', $waliId))
            ->findOrFail($mahasiswaId);

        $mahasiswa->status_akademik = $request->status_akademik;
        $mahasiswa->catatan_status = $request->catatan;
        $mahasiswa->save();

        Alert::success('Berhasil', 'Status akademik mahasiswa berhasil diperbarui.');
        return redirect()->route('dosen.mahasiswa-bimbingan.show', $mahasiswaId);
    }

    // Catatan bimbingan akademik (baru)
    public function storeCatatanBimbingan(Request $request, $mahasiswaId)
    {
        $request->validate([
            'catatan' => 'required|string|max:2000',
        ]);

        $waliId = Auth::guard('dosen')->user()->id;
        $mahasiswa = Mahasiswa::whereHas('kelas', fn($q) => $q->where('wali_id', $waliId))
            ->findOrFail($mahasiswaId);

        // Simpan catatan ke tabel bimbingan (buat model dan migration bimbingan_akademik)
        // Contoh:
        // $mahasiswa->bimbingan()->create([
        //     'dosen_id' => $waliId,
        //     'catatan' => $request->catatan,
        // ]);

        Alert::success('Berhasil', 'Catatan bimbingan akademik berhasil disimpan.');
        return redirect()->route('dosen.mahasiswa-bimbingan.show', $mahasiswaId);
    }

    // Export Excel (placeholder)
    public function exportExcel()
    {
        Alert::info('Segera Tersedia', 'Fitur export Excel sedang dikembangkan.');
        return back();
    }

    private function statusAkademik($semester)
    {
        if ($semester === null) return 'Tidak Aktif';
        if ($semester >= 1 && $semester <= 8) return 'Aktif';
        if ($semester > 8) return 'Studi Lama';
        return 'Tidak Diketahui';
    }
}
