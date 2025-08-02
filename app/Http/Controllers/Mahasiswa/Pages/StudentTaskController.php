<?php

namespace App\Http\Controllers\Mahasiswa\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\HasilStudi;
use App\Models\StudentTask;
use App\Models\StudentScore;
use App\Models\Pengaturan\WebSetting;

class StudentTaskController extends Controller
{
    public function index()
    {
        $user = Auth::guard('mahasiswa')->user();

        return view('mahasiswa.pages.stask-index', [
            'web' => WebSetting::find(1),
            'stask' => StudentTask::whereHas('jadkul', function($query) use ($user) {
                $query->where('kelas_id', $user->class_id);
            })->get(),
        ]);
    }

    public function view($code)
    {
        $task = StudentTask::where('code', $code)->firstOrFail();

        $score = StudentScore::where('stask_id', $task->id)
            ->where('student_id', Auth::guard('mahasiswa')->id())
            ->first();

        if ($score) {
            Alert::error('Error', 'Kamu sudah mengumpulkan tugas ini.');
            return back();
        }

        return view('mahasiswa.pages.stask-view', [
            'web' => WebSetting::find(1),
            'stask' => $task
        ]);
    }

    public function store(Request $request, $code)
    {
        $request->validate([
            'desc' => 'required',
            'file_1' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif|max:20480',
            'file_2' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif|max:20480',
            'file_3' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif|max:20480',
            'file_4' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif|max:20480',
            'file_5' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif|max:20480',
            'file_6' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif|max:20480',
            'file_7' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif|max:20480',
            'file_8' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif|max:20480',
        ]);

        $stask = StudentTask::where('code', $code)->firstOrFail();
        $user = Auth::guard('mahasiswa')->user();

        $task = new StudentScore;
        $task->stask_id = $stask->id;
        $task->desc = $request->desc;
        $task->code = Str::random(6);
        $task->student_id = $user->id;

        for ($i = 1; $i <= 8; $i++) {
            $fileKey = 'file_' . $i;
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $filename = time() . '-part-' . $i . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/uploads/tugas', $filename);
                $task->{'file_' . $i} = 'tugas/' . $filename;
            }
        }

        $task->save();

        Alert::success('Sukses', 'Tugas berhasil dikumpulkan');
        return redirect()->route('mahasiswa.akademik.tugas-index');
    }
}
