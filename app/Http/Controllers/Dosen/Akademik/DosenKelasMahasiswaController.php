<?php

namespace App\Http\Controllers\Dosen\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

// MODELS
use App\Models\Akademik\JadwalKuliah;
use App\Models\Mahasiswa;
use App\Models\AbsensiMahasiswa;
use App\Models\StudentTask;
use App\Models\StudentScore;
use App\Models\Pengaturan\WebSetting;

class DosenKelasMahasiswaController extends Controller
{
    protected $web;

    public function __construct()
    {
        $this->web = WebSetting::first();
    }

    public function index()
    {
        $dosen = Auth::guard('dosen')->user();
        $jadwal = JadwalKuliah::where('dosen_id', $dosen->id)->get();

        return view('dosen.pages.kelas.index', [
            'web' => $this->web,
            'jadwal' => $jadwal
        ]);
    }

    public function viewMahasiswa($code)
    {
        $jadkul = JadwalKuliah::where('code', $code)->with('kelas')->firstOrFail();
        $mahasiswa = Mahasiswa::where('kelas_id', $jadkul->kelas_id)->get();

        return view('dosen.pages.kelas.view-mahasiswa', [
            'web' => $this->web,
            'jadwal' => $jadkul,
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function viewAbsensi($code)
    {
        $absen = AbsensiMahasiswa::where('jadkul_code', $code)->get();

        return view('dosen.pages.kelas.view-absensi', [
            'web' => $this->web,
            'absen' => $absen,
        ]);
    }

    public function viewTugas($code)
    {
        // Dapatkan jadwal kuliah dulu berdasarkan code
        $jadkul = JadwalKuliah::where('code', $code)->with('tasks')->firstOrFail();

        return view('dosen.pages.kelas.view-tugas', [
            'web' => $this->web,
            'tugas' => $jadkul->tasks,
        ]);
    }

    public function viewNilai($task_code)
    {
        $task = StudentTask::where('code', $task_code)->firstOrFail();
        $scores = StudentScore::where('stask_id', $task->id)->get();

        return view('dosen.pages.kelas.view-nilai', [
            'web' => $this->web,
            'task' => $task,
            'scores' => $scores,
        ]);
    }
}
