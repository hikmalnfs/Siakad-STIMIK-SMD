<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>KRS - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>

    <h1>Kartu Rencana Studi (KRS)</h1>
    <p><strong>Nama Mahasiswa:</strong> {{ $user->name }}</p>
    <p><strong>Tahun Akademik:</strong> {{ $items->first()->jadwal->tahunAkademik->name ?? 'Tidak Ditemukan' }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Mata Kuliah</th>
                <th>SKS</th>
                <th>Dosen</th>
                <th>Ruang</th>
                <th>Hari / Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                @php
                    $jadwal = $item->jadwal;
                    $matkul = $jadwal->mataKuliah ?? null;
                    $dosen = $jadwal->dosen ?? null;
                    $ruang = $jadwal->ruang ?? null;
                    $waktu = $jadwal->waktuKuliah ?? collect();
                    $hari = $jadwal->hari ?? '-';
                    $jamMulai = $waktu->isNotEmpty() ? $waktu->pluck('time_start')->first() : '-';
                    $jamSelesai = $waktu->isNotEmpty() ? $waktu->pluck('time_ended')->last() : '-';
                @endphp
                <tr>
                    <td>{{ $matkul->code ?? '-' }}</td>
                    <td>{{ $matkul->name ?? '-' }}</td>
                    <td>{{ $matkul->bsks ?? '-' }}</td>
                    <td>{{ $dosen->name ?? '-' }}</td>
                    <td>{{ $ruang->name ?? '-' }}</td>
                    <td>{{ $hari }} / {{ $jamMulai }} - {{ $jamSelesai }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
