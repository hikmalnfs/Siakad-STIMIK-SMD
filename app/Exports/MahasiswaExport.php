<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class MahasiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    public function collection()
    {
        $mahasiswas = Mahasiswa::with(['prodi', 'kelas'])
            ->select('name', 'numb_nim', 'prodi_id', 'kelas_id', 'semester', 'email', 'phone')
            ->orderBy('name', 'asc')
            ->get();

        // Urutkan collection berdasarkan kelas name dan semester setelah nama
        $mahasiswas = $mahasiswas->sortBy([
            fn($a, $b) => strcmp($a->name, $b->name),
            fn($a, $b) => strcmp(optional($a->kelas)->name ?? '', optional($b->kelas)->name ?? ''),
            fn($a, $b) => $a->semester <=> $b->semester,
        ]);

        return $mahasiswas->values(); // reset index koleksi
    }

    public function map($item): array
    {
        return [
            $item->name,
            $item->numb_nim,
            optional($item->prodi)->name,
            optional($item->kelas)->name,
            $item->semester,
            $item->email,
            $item->phone,
        ];
    }

    public function headings(): array
    {
        return ['Nama', 'NIM', 'Program Studi', 'Kelas', 'Semester', 'Email', 'Phone'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Baris heading bold
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
