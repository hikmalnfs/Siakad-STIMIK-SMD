<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Kartu Hasil Studi (KHS)</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: center;
        }
        .total {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 11px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>KARTU HASIL STUDI (KHS)</h2>
        <h3>STMIK Samarinda</h3>
    </div>

    <div class="info">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="text-align: left;">Nama: {{ $mahasiswa->name ?? '-' }}</td>
                <td style="text-align: left;">NIM: {{ $mahasiswa->numb_nim ?? '-' }}</td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    Program Studi : {{ $mahasiswa->prodi->name ?? 'Manajemem Komputer' }}
                </td>
                <td style="text-align: left;">Semester: {{ $semester }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode MK</th>
                <th>Nama Mata Kuliah</th>
                <th>SKS</th>
                <th>Absen</th>
                <th>Tugas</th>
                <th>UTS</th>
                <th>UAS</th>
                <th>Nilai Akhir</th>
                <th>Huruf</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalSks = 0;
                $totalBobot = 0;
                $nilaiBobot = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1, 'E' => 0];
            @endphp

            @foreach ($data as $key => $item)
                @php
                    $matkul = $item->jadwalKuliah->mataKuliah ?? $item->mataKuliah;
                    $sks = $matkul->bsks ?? 0;
                    $huruf = $item->nilai_huruf ?? '-';
                    $bobot = $nilaiBobot[$huruf] ?? 0;

                    $totalSks += $sks;
                    $totalBobot += $sks * $bobot;
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $matkul->code ?? '-' }}</td>
                    <td style="text-align: left;">{{ $matkul->name ?? '-' }}</td>
                    <td>{{ $sks }}</td>
                    <td>{{ $item->nilai_absen ?? '-' }}</td>
                    <td>{{ $item->nilai_tugas ?? '-' }}</td>
                    <td>{{ $item->nilai_uts ?? '-' }}</td>
                    <td>{{ $item->nilai_uas ?? '-' }}</td>
                    <td>{{ $item->nilai_akhir ?? '-' }}</td>
                    <td>{{ $huruf }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total SKS: <strong>{{ $totalSks }}</strong></p>
        <p>IPS Semester Ini: <strong>{{ $totalSks > 0 ? number_format($totalBobot / $totalSks, 2) : '0.00' }}</strong></p>
    </div>

</body>
</html>
