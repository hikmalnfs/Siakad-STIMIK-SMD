<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Cetak Nilai</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
    </style>
</head>
<body>
    <h2>Daftar Nilai Mahasiswa</h2>
    <p><strong>Matakuliah:</strong> {{ $jadwal->mataKuliah->matkul ?? '' }}</p>
    <p><strong>Kelas:</strong> {{ $jadwal->kelas->pluck('nama')->join(', ') }}</p>
    <p><strong>Dosen:</strong> {{ $jadwal->dosen->nama ?? '' }}</p>
    <p><strong>Tanggal:</strong> {{ $tgl }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Nilai Absen</th>
                <th>Nilai Tugas</th>
                <th>Nilai UTS</th>
                <th>Nilai UAS</th>
                <th>Nilai Akhir</th>
                <th>Nilai Huruf</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->mahasiswa->nim ?? '-' }}</td>
                <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                <td>{{ $item->nilai_absen }}</td>
                <td>{{ $item->nilai_tugas }}</td>
                <td>{{ $item->nilai_uts }}</td>
                <td>{{ $item->nilai_uas }}</td>
                <td>{{ $item->nilai_akhir }}</td>
                <td>{{ $item->nilai_huruf }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.print();
    </script>
</body>
</html>
