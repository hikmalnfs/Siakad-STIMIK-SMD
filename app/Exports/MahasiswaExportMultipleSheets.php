<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Mahasiswa;

class MahasiswaExportMultipleSheets implements WithMultipleSheets
{
    protected $semesters;

    public function __construct()
    {
        // Ambil semua semester unik dari mahasiswa yang ada, urutkan ascending
        $this->semesters = Mahasiswa::select('semester')
            ->distinct()
            ->orderBy('semester', 'asc')
            ->pluck('semester')
            ->toArray();
    }

    /**
     * Menghasilkan array sheet, tiap semester satu sheet.
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->semesters as $semester) {
            // Buat instance sheet per semester, passing semester sebagai parameter
            $sheets[] = new MahasiswaPerSemesterSheet($semester);
        }

        return $sheets;
    }
}
