<?php
namespace App\Exports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaPerSemesterSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $semester;
    protected $kelas;

    public function __construct($semester, $kelas = null)
    {
        $this->semester = $semester;
        $this->kelas = $kelas;
    }

    public function collection()
    {
        $query = Mahasiswa::with(['prodi', 'kelas'])
            ->where('semester', $this->semester);

        if ($this->kelas) {
            $query->whereHas('kelas', function ($q) {
                $q->where('name', $this->kelas);
            });
        }

        return $query->select('name', 'numb_nim', 'prodi_id', 'kelas_id', 'email', 'phone')->get();
    }

    public function map($item): array
    {
        return [
            $item->name,
            $item->numb_nim,
            optional($item->prodi)->name,
            optional($item->kelas)->name,
            $item->email,
            $item->phone,
        ];
    }

    public function headings(): array
    {
        return ['Nama', 'NIM', 'Program Studi', 'Kelas', 'Email', 'Phone'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Header bold
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $range = 'A1:' . $highestColumn . $highestRow;

                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFCCE5FF'],
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ]);
            },
        ];
    }
}
