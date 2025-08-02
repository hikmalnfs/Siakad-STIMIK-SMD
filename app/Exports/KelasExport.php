<?php

namespace App\Exports;

use App\Models\Akademik\JadwalKuliah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class KelasExport implements FromView
{
    protected $tahunAkademikId;
    protected $semester;

    public function __construct($tahunAkademikId = null, $semester = null)
    {
        $this->tahunAkademikId = $tahunAkademikId;
        $this->semester = $semester;
    }

    public function view(): View
    {
        $query = JadwalKuliah::with(['matkul', 'kelas', 'ruang', 'dosen', 'tahunAkademik'])
            ->when($this->tahunAkademikId, function ($q) {
                $q->where('tahun_akademik_id', $this->tahunAkademikId);
            })
            ->when($this->semester, function ($q) {
                $q->where('semester', $this->semester);
            })
            ->orderBy('kelas_id')
            ->get();

        return view('exports.kelas-excel', [
            'jadwal' => $query
        ]);
    }
}
