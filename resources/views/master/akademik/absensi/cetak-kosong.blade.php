<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Absensi Kosong</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
        }

        .container {
            width: 100%;
            margin: auto;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
        }

        .header p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            word-wrap: break-word;
        }

        thead {
            background-color: #dbeafe;
        }

        /* Kolom no, nim, nama mahasiswa */
        th.no, td.no {
            width: 5%;
            max-width: 5%;
        }

        th.nim, td.nim {
            width: 15%;
            max-width: 15%;
        }

        th.nama, td.nama {
            text-align: left;
            padding-left: 5px;
            width: auto;
        }

        /* Kolom pertemuan */
        @media print {
            th.pertemuan, td.pertemuan {
                width: 3.5%;
                max-width: 3.5%;
                font-size: 9px;
            }
        }

        .ttd {
            width: 100%;
            margin-top: 40px;
        }

        .ttd td {
            border: none;
            text-align: right;
            padding-right: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>DAFTAR HADIR MAHASISWA</h2>
            <p><strong>Mata Kuliah:</strong> {{ $jadwal->mataKuliah->name ?? '-' }} ({{ $jadwal->mataKuliah->kode ?? '-' }})</p>
            <p><strong>Kelas:</strong> {{ $jadwal->kelas->pluck('name')->implode(', ') ?? '-' }}</p>
            <p><strong>Hari:</strong> {{ $jadwal->days_id ?? '-' }} &nbsp;&nbsp; | &nbsp;&nbsp;
                <strong>Jam:</strong>
                @php
                    $waktu = $jadwal->waktuKuliah->first();
                @endphp
                {{ $waktu->jam_mulai ?? '-' }} - {{ $waktu->jam_selesai ?? '-' }}
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="no">No</th>
                    <th class="nim">NIM</th>
                    <th class="nama">Nama Mahasiswa</th>
                    @for ($i = 1; $i <= 16; $i++)
                        <th class="pertemuan">P-{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @forelse ($mahasiswas as $i => $mhs)
                    <tr>
                        <td class="no">{{ $i + 1 }}</td>
                        <td class="nim">{{ $mhs->numb_nim ?? '-' }}</td>
                        <td class="nama">{{ $mhs->name ?? '-' }}</td>
                        @for ($j = 1; $j <= 16; $j++)
                            <td class="pertemuan"></td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="19" style="text-align: center;">Tidak ada data mahasiswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <table class="ttd">
            <tr>
                <td>
                    Samarinda, {{ now()->translatedFormat('d F Y') }}<br>
                    Dosen Pengampu,<br><br><br><br>
                    <strong><u>{{ $jadwal->dosen->name ?? '-' }}</u></strong><br>
                    NIDN: {{ $jadwal->dosen->nidn ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
