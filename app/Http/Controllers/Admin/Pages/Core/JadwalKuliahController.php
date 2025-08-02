<?php

namespace App\Http\Controllers\Admin\Pages\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

// Models
use App\Models\Akademik\MataKuliah;
use App\Models\Dosen;
use App\Models\Akademik\TahunAkademik;
use App\Models\Akademik\JadwalKuliah;
use App\Models\Akademik\WaktuKuliah;
use App\Models\Infrastruktur\Ruang;
use App\Models\Akademik\Kelas;
use App\Models\Akademik\JenisKelas;
use App\Models\Pengaturan\WebSetting;

class JadwalKuliahController extends Controller
{
    public function index()
    {
        $data = [
            'web'           => WebSetting::find(1),
            'pages'         => 'Jadwal Kuliah',
            'spref'         => 'academic.',
            'jadwal_kuliah' => JadwalKuliah::with(['mataKuliah', 'dosen', 'ruang', 'jenisKelas', 'waktuKuliah', 'kelas'])->get(),
            'dosen'         => Dosen::all(),
            'ruang'         => Ruang::all(),
            'mata_kuliah'   => MataKuliah::all(),
            'jenis_kelas'   => JenisKelas::all(),
            'waktu_kuliah'  => WaktuKuliah::all(),
            'kelas'         => Kelas::all(),
        ];

        return view('user.admin.master.admin-jadkul-index', $data);
    }

    public function store(Request $request)
    {
        // Pastikan waktu_kuliah_id dan kelas_ids berupa array integer yang valid
        $waktuIDs = array_filter((array) $request->input('waktu_kuliah_id'), fn($v) => is_numeric($v));
        $kelasIDs = array_filter((array) $request->input('kelas_ids'), fn($v) => is_numeric($v));

        // Override input agar validasi sesuai
        $request->merge([
            'waktu_kuliah_id' => $waktuIDs,
            'kelas_ids'       => $kelasIDs,
        ]);

        $validated = $request->validate([
            'bsks'              => 'required|integer|min:1|max:24',
            'matkul_id'         => 'required|exists:mata_kuliah,id',
            'kelas_ids'         => 'required|array|min:1',
            'kelas_ids.*'       => 'integer|exists:kelas,id',
            'dosen_id'          => 'required|exists:dosens,id',
            'ruang_id'          => 'required|exists:ruangs,id',
            'jenis_kelas_id'    => 'required|exists:jenis_kelas,id',
            'waktu_kuliah_id'   => 'required|array|min:1',
            'waktu_kuliah_id.*' => 'integer|exists:waktu_kuliahs,id',
            'pertemuan'         => 'required|integer|min:1|max:16',
            'hari'              => 'required|string',
            'metode'            => 'required|string|in:Tatap Muka,Teleconference',
            'tanggal'           => 'required|date',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'link'              => 'nullable|url',
        ]);

        $jadwal = JadwalKuliah::create(array_merge($validated, [
            'code' => Str::random(12),
        ]));

        $jadwal->waktuKuliah()->sync($validated['waktu_kuliah_id']);
        $jadwal->kelas()->sync($validated['kelas_ids']);

        Alert::success('Berhasil', 'Jadwal kuliah berhasil disimpan.');
        return back();
    }

    public function update(Request $request, $code)
    {
        $waktuIDs = array_filter((array) $request->input('waktu_kuliah_id'), fn($v) => is_numeric($v));
        $kelasIDs = array_filter((array) $request->input('kelas_ids'), fn($v) => is_numeric($v));

        $request->merge([
            'waktu_kuliah_id' => $waktuIDs,
            'kelas_ids'       => $kelasIDs,
        ]);

        $jadwal = JadwalKuliah::where('code', $code)->firstOrFail();

        $validated = $request->validate([
            'bsks'              => 'required|integer|min:1|max:24',
            'matkul_id'         => 'required|exists:mata_kuliah,id',
            'kelas_ids'         => 'required|array|min:1',
            'kelas_ids.*'       => 'integer|exists:kelas,id',
            'dosen_id'          => 'required|exists:dosens,id',
            'ruang_id'          => 'required|exists:ruangs,id',
            'jenis_kelas_id'    => 'required|exists:jenis_kelas,id',
            'waktu_kuliah_id'   => 'required|array|min:1',
            'waktu_kuliah_id.*' => 'integer|exists:waktu_kuliah,id',
            'pertemuan'         => 'required|integer|min:1|max:16',
            'hari'              => 'required|string',
            'metode'            => 'required|string|in:Tatap Muka,Teleconference',
            'tanggal'           => 'required|date',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'link'              => 'nullable|url',
        ]);

        $jadwal->update($validated);

        $jadwal->waktuKuliah()->sync($validated['waktu_kuliah_id']);
        $jadwal->kelas()->sync($validated['kelas_ids']);

        Alert::success('Berhasil', 'Jadwal kuliah berhasil diperbarui.');
        return back();
    }

    public function destroy($code)
    {
        $jadwal = JadwalKuliah::where('code', $code)->firstOrFail();
        $jadwal->waktuKuliah()->detach();
        $jadwal->kelas()->detach();
        $jadwal->delete();

        Alert::success('Berhasil', 'Jadwal kuliah berhasil dihapus.');
        return back();
    }
}
