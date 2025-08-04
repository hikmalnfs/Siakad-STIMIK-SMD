<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\Akademik\JadwalKuliah;
use App\Models\Akademik\MataKuliah;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\JenisKelas;
use App\Models\Akademik\WaktuKuliah;
use App\Models\Akademik\TahunAkademik;
use App\Models\Dosen;
use App\Models\Ruang;
use Illuminate\Support\Facades\Validator;
use App\Models\Pengaturan\WebSetting;
use Illuminate\Support\Facades\Log;


class JadwalKuliahController extends Controller
{
    public function renderJadwalKuliah(Request $request)
    {
        $user = Auth::user();
        $webs = WebSetting::first();

        // Ambil nilai semester dari query parameter
        $semesterFilter = $request->input('semester');

        // Query jadwal dengan relasi
        $jadwalQuery = JadwalKuliah::with(['kelas', 'dosen', 'ruang', 'mataKuliah', 'jenisKelas', 'waktuKuliah']);

        // Jika filter semester diisi, tambahkan where
        if ($semesterFilter) {
            $jadwalQuery->whereHas('mataKuliah', function ($q) use ($semesterFilter) {
                $q->where('semester', $semesterFilter);
            });
        }
            if ($request->filled('ganjil_genap')) {
        $jadwalQuery->whereHas('mataKuliah', function ($query) use ($request) {
            if ($request->ganjil_genap == 'ganjil') {
                $query->whereRaw('semester % 2 = 1');
            } elseif ($request->ganjil_genap == 'genap') {
                $query->whereRaw('semester % 2 = 0');
            }
        });
        }
        
        $kelasId = $request->input('kelas_id');
        if ($kelasId) {
            $jadwalQuery->whereHas('kelas', function ($q) use ($kelasId) {
                $q->where('kelas.id', $kelasId);
            });
        }



        $data = [
            'webs' => $webs,
            'spref' => $user ? $user->prefix : '',
            'menus' => "Master",
            'pages' => "Jadwal Kuliah",
            'academy' => $webs->school_apps . ' by ' . $webs->school_name,
            'jadwal_kuliah' => $jadwalQuery->get(),
            'mata_kuliah' => MataKuliah::orderBy('name', 'asc')->get(),
            'kelas' => Kelas::all(),
            'jenis_kelas' => JenisKelas::with('waktuKuliah')->get(),
            'waktu_kuliah' => WaktuKuliah::all(),
            'dosen' => Dosen::orderBy('name', 'asc')->get(),
            'ruang' => Ruang::all(),
            'tahun_akademik' => TahunAkademik::all(),
            'semester_list' => MataKuliah::select('semester')->distinct()->orderBy('semester')->pluck('semester'),
            'selected_semester' => $semesterFilter,
        ];

        // return view('master.akademik.jadwal-kuliah-index', $data, compact('user'));
                return view('master.akademik.jadwal-kuliah-index', $data, compact('user'));
    }

    public function handleJadwalKuliah(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|integer|exists:dosens,id',
            'ruang_id' => 'required|integer|exists:ruangs,id',
            'matkul_id' => 'required|integer|exists:mata_kuliahs,id',
            'jenis_kelas_id' => 'required|integer|exists:jenis_kelas,id',
            'waktu_kuliah_id' => 'required|array|min:1',
            'waktu_kuliah_id.*' => 'integer|exists:waktu_kuliahs,id',
            'bsks' => 'required|integer|min:1|max:6',
            'pertemuan' => 'required|integer|min:1',
            'hari' => 'required|string|max:20',
            'metode' => 'required|string|in:Tatap Muka,Teleconference',
            'tanggal' => 'required|date',
            'link' => 'nullable|url',
            'kelas_ids' => 'required|array|min:1',
            'kelas_ids.*' => 'integer|exists:kelas,id',
            'tahun_akademik_id' => 'required|integer|exists:tahun_akademiks,id',
        ]);

        try {
            DB::beginTransaction();

            $jenisKelas = JenisKelas::with('waktuKuliah')->findOrFail($request->jenis_kelas_id);
            foreach ($request->waktu_kuliah_id as $waktuId) {
                if (!$jenisKelas->waktuKuliah->contains('id', $waktuId)) {
                    throw new \Exception("Waktu kuliah ID {$waktuId} tidak sesuai dengan jenis kelas yang dipilih.");
                }
            }

            $code = 'JDW-' . Str::upper(Str::random(8));

            $jadwalKuliah = JadwalKuliah::create([
                'code' => $code,
                'dosen_id' => $request->dosen_id,
                'ruang_id' => $request->ruang_id,
                'matkul_id' => $request->matkul_id,
                'jenis_kelas_id' => $request->jenis_kelas_id,
                'bsks' => $request->bsks,
                'pertemuan' => $request->pertemuan,
                'hari' => $request->hari,
                'metode' => $request->metode,
                'tanggal' => $request->tanggal,
                'link' => $request->link,
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'created_by' => Auth::id(),
            ]);

            $jadwalKuliah->kelas()->attach($request->kelas_ids);
            $jadwalKuliah->waktuKuliah()->attach($request->waktu_kuliah_id);

            DB::commit();
            return redirect()->back()->with('success', 'Jadwal Kuliah berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan Jadwal Kuliah: ' . $e->getMessage());
        }
    }

    public function updateJadwalKuliah(Request $request, $code)
    {
        $request->validate([
            'dosen_id' => 'required|integer|exists:dosens,id',
            'ruang_id' => 'required|integer|exists:ruangs,id',
            'matkul_id' => 'required|integer|exists:mata_kuliahs,id',
            'jenis_kelas_id' => 'required|integer|exists:jenis_kelas,id',
            'waktu_kuliah_id' => 'required|array|min:1',
            'waktu_kuliah_id.*' => 'integer|exists:waktu_kuliahs,id',
            'bsks' => 'required|integer|min:1|max:6',
            'pertemuan' => 'required|integer|min:1',
            'hari' => 'required|string|max:20',
            'metode' => 'required|string|in:Tatap Muka,Teleconference',
            'tanggal' => 'required|date',
            'link' => 'nullable|url',
            'kelas_ids' => 'required|array|min:1',
            'kelas_ids.*' => 'integer|exists:kelas,id',
            'tahun_akademik_id' => 'required|integer|exists:tahun_akademiks,id',
        ]);

        try {
            DB::beginTransaction();

            $jenisKelas = JenisKelas::with('waktuKuliah')->findOrFail($request->jenis_kelas_id);
            foreach ($request->waktu_kuliah_id as $waktuId) {
                if (!$jenisKelas->waktuKuliah->contains('id', $waktuId)) {
                    throw new \Exception("Waktu kuliah ID {$waktuId} tidak sesuai dengan jenis kelas yang dipilih.");
                }
            }

            $jadwalKuliah = JadwalKuliah::where('code', $code)->firstOrFail();

            $jadwalKuliah->update([
                'dosen_id' => $request->dosen_id,
                'ruang_id' => $request->ruang_id,
                'matkul_id' => $request->matkul_id,
                'jenis_kelas_id' => $request->jenis_kelas_id,
                'bsks' => $request->bsks,
                'pertemuan' => $request->pertemuan,
                'hari' => $request->hari,
                'metode' => $request->metode,
                'tanggal' => $request->tanggal,
                'link' => $request->link,
                'tahun_akademik_id' => $request->tahun_akademik_id,
                'updated_by' => Auth::id(),
            ]);

            $jadwalKuliah->kelas()->sync($request->kelas_ids);
            $jadwalKuliah->waktuKuliah()->sync($request->waktu_kuliah_id);

            DB::commit();
            return redirect()->back()->with('success', 'Jadwal Kuliah berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui Jadwal Kuliah: ' . $e->getMessage());
        }
    }

    public function deleteJadwalKuliah($code)
    {
        try {
            DB::beginTransaction();

            $jadwalKuliah = JadwalKuliah::where('code', $code)->firstOrFail();

            $jadwalKuliah->kelas()->detach();
            $jadwalKuliah->waktuKuliah()->detach();

            $jadwalKuliah->update(['deleted_by' => Auth::id()]);
            $jadwalKuliah->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Jadwal Kuliah berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Jadwal Kuliah: ' . $e->getMessage());
        }
    }

    public function getMatkulByDosen($dosen_id)
    {
        $matkuls = MataKuliah::where(function ($query) use ($dosen_id) {
            $query->where('dosen1_id', $dosen_id)
                ->orWhere('dosen2_id', $dosen_id)
                ->orWhere('dosen3_id', $dosen_id);
        })
        ->orderBy('name', 'asc')
        ->get();

        if ($matkuls->count()) {
            $result = $matkuls->map(function ($matkul) {
                return [
                    'id' => $matkul->id,
                    'name' => $matkul->name,
                ];
            });

            return response()->json([
                'success' => true,
                'matkul' => $result
            ]);
        }
        return response()->json(['success' => false, 'matkul' => null]);
    }


    public function getDosenByMatkul(Request $request)
    {
        $matkul_id = $request->matkul_id;
        $matkul = MataKuliah::with(['dosen1', 'dosen2', 'dosen3'])->find($matkul_id);

        if ($matkul) {
            $dosenList = collect([]);

            if ($matkul->dosen1) {
                $dosenList->push([
                    'id' => $matkul->dosen1->id,
                    'name' => $matkul->dosen1->name
                ]);
            }
            if ($matkul->dosen2) {
                $dosenList->push([
                    'id' => $matkul->dosen2->id,
                    'name' => $matkul->dosen2->name
                ]);
            }
            if ($matkul->dosen3) {
                $dosenList->push([
                    'id' => $matkul->dosen3->id,
                    'name' => $matkul->dosen3->name
                ]);
            }

            // Urutkan berdasarkan nama dosen (abjad)
            $sorted = $dosenList->sortBy('name')->values()->all();

            return response()->json([
                'success' => true,
                'dosen' => $sorted
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Mata kuliah tidak ditemukan'
        ]);
    }

        public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'matkul_id');
    }
        public function waktuKuliah()
    {
        return $this->hasMany(WaktuKuliah::class, 'jadwal_id');
    }
        public function getMatkulBySemester(Request $request)
    {
        $semester = $request->semester;
        $data = MataKuliah::where('semester', $semester)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);
        return response()->json($data);
    }




public function autoGenerateAll(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
        'tanggal' => 'nullable|date',
        'pertemuan' => 'nullable|integer|min:1|max:20',
        'metode' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $tahunAkademikId = $request->tahun_akademik_id;
    $tanggalMulai = $request->tanggal ?? now()->toDateString();
    $pertemuan = $request->pertemuan ?? 14;
    $metode = $request->metode ?? 'Tatap Muka';

    $mataKuliahs = MataKuliah::with(['dosen1', 'dosen2', 'dosen3'])
        ->orderBy('semester')
        ->get();

    $jenisKelas = JenisKelas::first();
    $waktuKuliahList = WaktuKuliah::all();
    $ruangs = Ruang::pluck('id')->toArray();
    $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    $jadwalPreview = [];
    $jadwalCache = collect(); // Cache jadwal preview untuk cek bentrok cepat

    DB::beginTransaction();

    try {
        foreach ($mataKuliahs as $matkul) {
            $kelasList = Kelas::where('prodi_id', $matkul->prodi_id)->get();
            $dosenPengampu = collect([$matkul->dosen1, $matkul->dosen2, $matkul->dosen3])->filter();

            foreach ($kelasList as $kelas) {
                foreach ($dosenPengampu as $dosen) {
                    $foundSlot = false;
                    foreach ($hariList as $hari) {
                        foreach ($ruangs as $ruang) {
                            foreach ($waktuKuliahList as $waktu) {
                                $jamMulai = $waktu->time_start;
                                $jamSelesai = $waktu->time_ended;

                                // Cek bentrok pada preview jadwal yang sudah ada di cache
                                $bentrokPreview = $this->cekBentrokPreview(
                                    $jadwalCache,
                                    $ruang,
                                    $hari,
                                    $jamMulai,
                                    $jamSelesai,
                                    $dosen->id,
                                    $kelas->id
                                );

                                // Cek bentrok dengan jadwal yang sudah tersimpan di DB
                                $bentrokDb = $this->cekBentrokJadwal(
                                    $ruang,
                                    $hari,
                                    $jamMulai,
                                    $jamSelesai,
                                    $dosen->id,
                                    $kelas->id,
                                    $tahunAkademikId
                                );

                                if (!$bentrokPreview && !$bentrokDb) {
                                    $jadwalItem = [
                                        'code' => 'AUTO-JDW-' . strtoupper(Str::random(6)),
                                        'dosen_id' => $dosen->id,
                                        'ruang_id' => $ruang,
                                        'matkul_id' => $matkul->id,
                                        'jenis_kelas_id' => $jenisKelas->id ?? 1,
                                        'bsks' => $matkul->bsks ?? 2,
                                        'pertemuan' => $pertemuan,
                                        'hari' => $hari,
                                        'metode' => $metode,
                                        'tanggal' => $tanggalMulai,
                                        'tahun_akademik_id' => $tahunAkademikId,
                                        'kelas_id' => $kelas->id,
                                        'waktu_id' => $waktu->id,
                                        'jam_mulai' => $jamMulai,
                                        'jam_selesai' => $jamSelesai,
                                    ];
                                    $jadwalPreview[] = $jadwalItem;
                                    $jadwalCache->push($jadwalItem);
                                    $foundSlot = true;
                                    break 4; // keluar semua loop saat slot ditemukan
                                }
                            }
                        }
                    }
                    if (!$foundSlot) {
                        Log::warning("Auto generate gagal menemukan slot untuk matkul {$matkul->nama} dosen {$dosen->name} kelas {$kelas->nama}");
                    }
                }
            }
        }

        session(['jadwal_preview' => $jadwalPreview]);
        DB::rollBack(); // Rollback karena ini hanya preview (belum disimpan)

        return view('master.akademik.jadwal-preview', ['jadwalPreview' => $jadwalPreview]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Gagal generate jadwal: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal generate jadwal: ' . $e->getMessage());
    }
}
    public function cekBentrokJadwal($ruangId, $hari, $jamMulai, $jamSelesai, $dosenId, $kelasId, $tahunAkademikId)
    {
        return JadwalKuliah::where('tahun_akademik_id', $tahunAkademikId)
            ->where('hari', $hari)
            ->where(function($query) use ($ruangId, $dosenId, $kelasId, $jamMulai, $jamSelesai) {
                $query->where(function($q) use ($ruangId, $jamMulai, $jamSelesai) {
                    $q->where('ruang_id', $ruangId)
                    ->whereHas('waktuKuliah', function ($wq) use ($jamMulai, $jamSelesai) {
                        $wq->where(function ($wt) use ($jamMulai, $jamSelesai) {
                            $wt->where('time_start', '<', $jamSelesai)
                                ->where('time_ended', '>', $jamMulai);
                        });
                    });
                })
                ->orWhere(function ($q) use ($dosenId, $jamMulai, $jamSelesai) {
                    $q->where('dosen_id', $dosenId)
                    ->whereHas('waktuKuliah', function ($wq) use ($jamMulai, $jamSelesai) {
                        $wq->where(function ($wt) use ($jamMulai, $jamSelesai) {
                            $wt->where('time_start', '<', $jamSelesai)
                                ->where('time_ended', '>', $jamMulai);
                        });
                    });
                })
                ->orWhere(function ($q) use ($kelasId) {
                    $q->whereHas('kelas', function ($kq) use ($kelasId) {
                        $kq->where('kelas.id', $kelasId);
                    });
                });
            })
            ->exists();
    }

    public function cekBentrokPreview($jadwalCache, $ruangId, $hari, $jamMulai, $jamSelesai, $dosenId, $kelasId)
    {
        foreach ($jadwalCache as $jadwal) {
            if (
                $jadwal['hari'] == $hari &&
                (
                    $jadwal['ruang_id'] == $ruangId ||
                    $jadwal['dosen_id'] == $dosenId ||
                    $jadwal['kelas_id'] == $kelasId
                )
            ) {
                // Cek overlap waktu
                if (
                    $jadwal['jam_mulai'] < $jamSelesai &&
                    $jadwal['jam_selesai'] > $jamMulai
                ) {
                    return true; // bentrok
                }
            }
        }
        return false; // tidak bentrok
    }

    public function storeGeneratedSchedule(Request $request)
    {
        $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'tanggal' => 'nullable|date',
        ]);

        $jadwalPreview = session('jadwal_preview', []);

        if (empty($jadwalPreview)) {
            return redirect()->back()->with('error', 'Tidak ada jadwal untuk disimpan.');
        }

        DB::beginTransaction();

        try {
            foreach ($jadwalPreview as $jadwal) {
                $jadwalModel = JadwalKuliah::create([
                    'code' => $jadwal['code'],
                    'dosen_id' => $jadwal['dosen_id'],
                    'ruang_id' => $jadwal['ruang_id'],
                    'matkul_id' => $jadwal['matkul_id'],
                    'jenis_kelas_id' => $jadwal['jenis_kelas_id'],
                    'bsks' => $jadwal['bsks'],
                    'pertemuan' => $jadwal['pertemuan'],
                    'hari' => $jadwal['hari'],
                    'metode' => $jadwal['metode'],
                    'tanggal' => $jadwal['tanggal'],
                    'tahun_akademik_id' => $jadwal['tahun_akademik_id'],
                    'created_by' => Auth::id(),
                ]);

                $jadwalModel->kelas()->attach($jadwal['kelas_id']);
                $jadwalModel->waktuKuliah()->attach($jadwal['waktu_id']);
            }

            DB::commit();

            session()->forget('jadwal_preview');

            return redirect()->route('dosen.akademik.kelas-index')->with('success', 'Jadwal berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal simpan jadwal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal simpan jadwal: ' . $e->getMessage());
        }
    }

    public function rescheduleAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'tanggal' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $tahunAkademikId = $request->tahun_akademik_id;
        $tanggalMulai = $request->tanggal ?? now()->toDateString();

        $waktuKuliahList = WaktuKuliah::all();
        $ruangs = Ruang::pluck('id')->toArray();
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        $jadwalExisting = JadwalKuliah::with(['waktuKuliah', 'kelas'])->where('tahun_akademik_id', $tahunAkademikId)->get();

        $jadwalCache = collect();
        $jadwalReschedule = [];

        DB::beginTransaction();

        try {
            foreach ($jadwalExisting as $jadwal) {
                $foundSlot = false;

                foreach ($hariList as $hari) {
                    foreach ($ruangs as $ruang) {
                        foreach ($waktuKuliahList as $waktu) {
                            $jamMulai = $waktu->time_start;
                            $jamSelesai = $waktu->time_ended;

                            $bentrokPreview = $this->cekBentrokPreview(
                                $jadwalCache,
                                $ruang,
                                $hari,
                                $jamMulai,
                                $jamSelesai,
                                $jadwal->dosen_id,
                                $jadwal->kelas->first()->id ?? null
                            );

                            $bentrokDb = JadwalKuliah::where('id', '!=', $jadwal->id)
                                ->where('tahun_akademik_id', $tahunAkademikId)
                                ->where('hari', $hari)
                                ->where(function($query) use ($ruang, $jadwal, $jamMulai, $jamSelesai) {
                                    $query->where(function($q) use ($ruang, $jamMulai, $jamSelesai) {
                                        $q->where('ruang_id', $ruang)
                                            ->whereHas('waktuKuliah', function($wq) use ($jamMulai, $jamSelesai) {
                                                $wq->where(function($wt) use ($jamMulai, $jamSelesai) {
                                                    $wt->where('time_start', '<', $jamSelesai)
                                                        ->where('time_ended', '>', $jamMulai);
                                                });
                                            });
                                    })
                                    ->orWhere(function($q) use ($jadwal, $jamMulai, $jamSelesai) {
                                        $q->where('dosen_id', $jadwal->dosen_id)
                                            ->whereHas('waktuKuliah', function($wq) use ($jamMulai, $jamSelesai) {
                                                $wq->where(function($wt) use ($jamMulai, $jamSelesai) {
                                                    $wt->where('time_start', '<', $jamSelesai)
                                                        ->where('time_ended', '>', $jamMulai);
                                                });
                                            });
                                    })
                                    ->orWhereHas('kelas', function($kq) use ($jadwal) {
                                        $kq->where('kelas.id', $jadwal->kelas->first()->id ?? null);
                                    });
                                })
                                ->exists();

                            if ($bentrokPreview || $bentrokDb) {
                                continue;
                            }

                            // Slot bebas, simpan ke cache dan array reschedule
                            $jadwalReschedule[] = [
                                'id' => $jadwal->id,
                                'ruang_id' => $ruang,
                                'hari' => $hari,
                                'waktu_id' => $waktu->id,
                                'jam_mulai' => $jamMulai,
                                'jam_selesai' => $jamSelesai,
                            ];

                            $jadwalCache->push([
                                'hari' => $hari,
                                'ruang_id' => $ruang,
                                'dosen_id' => $jadwal->dosen_id,
                                'kelas_id' => $jadwal->kelas->first()->id ?? null,
                                'jam_mulai' => $jamMulai,
                                'jam_selesai' => $jamSelesai,
                            ]);

                            $foundSlot = true;
                            break 4; // keluar loop
                        }
                    }
                }

                if (!$foundSlot) {
                    // Tidak dapat slot baru, tetap jadwal lama
                    $jadwalReschedule[] = [
                        'id' => $jadwal->id,
                        'ruang_id' => $jadwal->ruang_id,
                        'hari' => $jadwal->hari,
                        'waktu_id' => $jadwal->waktuKuliah->first()->id ?? null,
                        'jam_mulai' => $jadwal->waktuKuliah->first()->time_start ?? null,
                        'jam_selesai' => $jadwal->waktuKuliah->first()->time_ended ?? null,
                    ];

                    $jadwalCache->push([
                        'hari' => $jadwal->hari,
                        'ruang_id' => $jadwal->ruang_id,
                        'dosen_id' => $jadwal->dosen_id,
                        'kelas_id' => $jadwal->kelas->first()->id ?? null,
                        'jam_mulai' => $jadwal->waktuKuliah->first()->time_start ?? null,
                        'jam_selesai' => $jadwal->waktuKuliah->first()->time_ended ?? null,
                    ]);

                    Log::warning("Reschedule gagal menemukan slot baru untuk jadwal ID {$jadwal->id}");
                }
            }

            // Update DB jadwal sesuai hasil reschedule
            foreach ($jadwalReschedule as $jadwalUpdate) {
                $jadwalModel = JadwalKuliah::find($jadwalUpdate['id']);
                if (!$jadwalModel) continue;

                $jadwalModel->ruang_id = $jadwalUpdate['ruang_id'];
                $jadwalModel->hari = $jadwalUpdate['hari'];
                $jadwalModel->save();

                $jadwalModel->waktuKuliah()->sync([$jadwalUpdate['waktu_id']]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Jadwal berhasil diatur ulang tanpa bentrok.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal reschedule jadwal: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal reschedule jadwal: ' . $e->getMessage());
            
        }
    }
}
