<?php

namespace App\Http\Controllers\Dosen\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Akademik\JadwalKuliah;
use App\Models\Mahasiswa;
use App\Models\HasilStudi;
use App\Models\StudentScore;
use App\Models\StudentTask;
use App\Models\Pengaturan\WebSetting;

class StudentTaskController extends Controller
{
    public function index()
    {
        $data['web'] = WebSetting::find(1);
        $data['jadkul'] = JadwalKuliah::all();
        $data['stask'] = StudentTask::all();

        return view('dosen.pages.student-task-index', $data);
    }

    public function create()
    {
        $data['web'] = WebSetting::find(1);
        $data['jadkul'] = JadwalKuliah::all();
        $data['stask'] = StudentTask::latest()->paginate(5);

        return view('dosen.pages.student-task-create', $data);
    }

    public function view($code)
    {
        $data['web'] = WebSetting::find(1);
        $data['jadkul'] = JadwalKuliah::all();
        $data['stask'] = StudentTask::latest()->paginate(5);
        $data['task'] = StudentTask::where('code', $code)->first();

        if (!$data['task']) {
            Alert::error('Data tugas tidak ditemukan');
            return redirect()->route('dosen.akademik.stask-index');
        }

        $data['score'] = StudentScore::where('stask_id', $data['task']->id)->get();

        return view('dosen.pages.student-task-view', $data);
    }

    public function viewDetail($code)
    {
        $data['web'] = WebSetting::find(1);
        $data['stask'] = StudentTask::latest()->paginate(5);
        $data['score'] = StudentScore::where('code', $code)->first();

        if (!$data['score']) {
            Alert::error('Data skor tidak ditemukan');
            return redirect()->route('dosen.akademik.stask-index');
        }

        return view('dosen.pages.student-task-view-score', $data);
    }

    public function edit($code)
    {
        $data['web'] = WebSetting::find(1);
        $data['jadkul'] = JadwalKuliah::all();
        $data['stask'] = StudentTask::latest()->paginate(5);
        $data['task'] = StudentTask::where('code', $code)->first();

        if (!$data['task']) {
            Alert::error('Data tugas tidak ditemukan');
            return redirect()->route('dosen.akademik.stask-index');
        }

        return view('dosen.pages.student-task-edit', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'jadkul_id'   => 'required',
            'exp_date'    => 'required|date',
            'exp_time'    => 'required',
            'title'       => 'required|string',
            'detail_task' => 'required|string',
        ], [
            'jadkul_id.required'   => 'Jadwal kuliah wajib dipilih.',
            'exp_date.required'    => 'Batas Akhir Tanggal wajib diisi.',
            'exp_date.date'        => 'Format tanggal tidak valid.',
            'exp_time.required'    => 'Batas Akhir Waktu wajib diisi.',
            'title.required'       => 'Judul tugas kuliah wajib diisi.',
            'detail_task.required' => 'Detail tugas kuliah wajib diisi.',
        ]);

        $stask = new StudentTask();
        $stask->dosen_id = Auth::guard('dosen')->id();
        $stask->code = Str::random(6);
        $stask->jadkul_id = $request->jadkul_id;
        $stask->exp_date = $request->exp_date;
        $stask->exp_time = $request->exp_time;
        $stask->title = $request->title;
        $stask->detail_task = $request->detail_task;
        $stask->save();

        Alert::success('Data berhasil ditambahkan');
        return back();
    }

    public function update(Request $request, $code)
    {
        $request->validate([
            'jadkul_id'   => 'required',
            'exp_date'    => 'required|date',
            'exp_time'    => 'required',
            'title'       => 'required|string',
            'detail_task' => 'required|string',
        ], [
            'jadkul_id.required'   => 'Jadwal kuliah wajib dipilih.',
            'exp_date.required'    => 'Batas Akhir Tanggal wajib diisi.',
            'exp_date.date'        => 'Format tanggal tidak valid.',
            'exp_time.required'    => 'Batas Akhir Waktu wajib diisi.',
            'title.required'       => 'Judul tugas kuliah wajib diisi.',
            'detail_task.required' => 'Detail tugas kuliah wajib diisi.',
        ]);

        $stask = StudentTask::where('code', $code)->first();

        if (!$stask) {
            Alert::error('Data tugas tidak ditemukan');
            return redirect()->route('dosen.akademik.stask-index');
        }

        $stask->dosen_id = Auth::guard('dosen')->id();
        $stask->jadkul_id = $request->jadkul_id;
        $stask->exp_date = $request->exp_date;
        $stask->exp_time = $request->exp_time;
        $stask->title = $request->title;
        $stask->detail_task = $request->detail_task;
        $stask->save();

        Alert::success('Data berhasil diupdate');
        return back();
    }

    public function updateScore($code, Request $request)
    {
        $score = StudentScore::where('code', $code)->first();

        if (!$score) {
            Alert::error('Data skor tidak ditemukan');
            return redirect()->route('dosen.akademik.stask-index');
        }

        $score->score = $request->score;
        $score->save();

        $user = Mahasiswa::find($request->student_id);
        if (!$user) {
            Alert::error('Data mahasiswa tidak ditemukan');
            return back();
        }

        $khs = HasilStudi::where('student_id', $user->id)
            ->where('smt_id', $user->taka->raw_semester)
            ->first();

        if (!$khs) {
            $ckhs = new HasilStudi();
            $ckhs->student_id = $user->id;
            $ckhs->taka_id = $user->taka->id;
            $ckhs->smt_id = $user->taka->raw_semester;
            $ckhs->score_tugas = $request->score;
            $ckhs->max_tugas = 1;
            $ckhs->code = Str::random(6);
            $ckhs->save();
        } else {
            $khs->score_tugas += $request->score;
            $khs->max_tugas += 1;
            $khs->save();
        }

        Alert::success('Score berhasil diupdate');
        return back();
    }

    public function destroy($code)
    {
        $stask = StudentTask::where('code', $code)->first();

        if (!$stask) {
            Alert::error('Data tugas tidak ditemukan');
            return redirect()->route('dosen.akademik.stask-index');
        }

        $stask->delete();

        Alert::success('Data berhasil dihapus');
        return back();
    }
}
