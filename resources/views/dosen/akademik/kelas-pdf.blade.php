<!DOCTYPE html>
<html>
<head>
    <title>Data Kelas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 5px; border: 1px solid #000; text-align: center; }
    </style>
</head>
<body>
    <h3>Data Kelas</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kode Kelas</th>
                <th>Nama Kelas</th>
                <th>Tahun Akademik</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $i => $kelas)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $kelas->kode }}</td>
                    <td>{{ $kelas->nama }}</td>
                    <td>{{ $kelas->tahunAkademik->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
