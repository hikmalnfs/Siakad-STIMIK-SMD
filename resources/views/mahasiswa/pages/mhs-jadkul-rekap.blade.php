@extends('base.base-dash-index')

@section('title', 'Rekap Absensi Mahasiswa')
@section('menu', 'Jadwal Kuliah')
@section('submenu', 'Rekap Absensi Detail')
@section('urlmenu') {{ route('mahasiswa.home-jadkul-index') }} @endsection
@section('subdesc', 'Detail rekap kehadiran Anda untuk mata kuliah yang dipilih.')

@section('custom-css')
<style>
    body, .container {
        font-family: 'Poppins', sans-serif;
    }

    .card {
        border: 1px solid #d1d7e0;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        background-color: #fff;
    }

    .card-header {
        background-color: #41559f;
        color: #fff;
        font-weight: 600;
        font-size: 1.1rem;
        padding: 0.75rem 1.25rem;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        user-select: none;
    }

    .table-container {
        overflow-x: auto;
        border-radius: 8px;
        margin: 1rem;
    }

    .table {
        width: 100%;
        min-width: 990px; /* muat 16 kolom */
        border-collapse: collapse;
        font-size: 0.87rem;
        table-layout: fixed;
    }

    /* Kolom pertama */
    .table thead th:first-child,
    .table tbody td:first-child {
        min-width: 130px;
        max-width: 130px;
        text-align: left;
        padding-left: 1rem;
        white-space: nowrap;
        font-weight: 600;
        vertical-align: middle;
    }

    /* Header pertemuan */
    .table thead th:not(:first-child) {
        border-bottom: 2px solid #41709f;
        padding: 0.6rem 0.5rem;
        text-transform: uppercase;
        color: #495057;
        white-space: nowrap;
        font-weight: 600;
        width: 50px;
        max-width: 50px;
    }

    /* Baris tanggal */
    .table thead tr:nth-child(2) th:not(:first-child) {
        color: #6c757d;
        font-weight: 500;
        font-size: 0.75rem;
        padding: 0.45rem 0.5rem;
        white-space: normal;
        word-break: break-word;
        max-width: 50px;
    }

    /* Isi tabel */
    .table tbody td {
        border-top: 1px solid #dee2e6;
        padding: 0.5rem 0.5rem;
        text-align: center;
        color: #3a3a3a;
        vertical-align: middle;
        white-space: nowrap;
    }

    /* Hover baris */
    .table tbody tr:hover {
        background-color: #f5f8ff;
    }

    /* Kolom pertama isi tabel */
    .table tbody td:first-child {
        font-weight: 600;
        color: #222;
        padding-left: 1rem;
        text-align: left;
        white-space: nowrap;
        max-width: 130px;
    }

    /* Ikon absensi */
    .table tbody td span {
        display: inline-block;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: default;
    }

    /* Warna teks */
    .text-success { color: #28a745 !important; }
    .text-warning { color: #ffc107 !important; }
    .text-info { color: #17a2b8 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-muted { color: #6c757d !important; }

    /* Summary statistik */
    .stat-summary {
        display: flex;
        gap: 1rem;
        margin: 1rem 1rem 0 1rem;
        font-weight: 600;
        font-size: 0.9rem;
        color: #333;
    }

    .stat-summary span {
        padding: 6px 14px;
        border-radius: 6px;
    }

    .bg-hadir {
        background-color: #d4edda;
        color: #155724;
    }

    .bg-persentase {
        background-color: #cce5ff;
        color: #004085;
    }

    .bg-status-m {
        background-color: #d4edda;
        color: #155724;
    }

    .bg-status-tm {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Legend */
    .legend {
        margin: 1rem;
        font-size: 0.85rem;
        color: #555;
        display: flex;
        gap: 1.2rem;
        user-select: none;
        flex-wrap: wrap;
    }

    .legend span {
        display: flex;
        gap: 0.3rem;
        align-items: center;
    }

    .legend .dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: inline-block;
    }

    .legend .hadir { background-color: #28a745; }
    .legend .izin { background-color: #ffc107; }
    .legend .sakit { background-color: #17a2b8; }
    .legend .alpa { background-color: #dc3545; }
    .legend .none {
        background-color: transparent;
        border: 1.5px solid #adb5bd;
    }
 /* Tombol */
    .btn-back {
        background-color: #414a9f;
        color: #fff;
        padding: 10px 22px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-bottom: 1.5rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
    }

    .btn-back:hover {
        background-color: #303973;
    }

    .btn-back i {
        font-size: 1.2rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .table {
            min-width: 700px;
            font-size: 0.8rem;
        }
        .card-header {
            font-size: 1rem;
            padding: 0.6rem 1rem;
        }
        .stat-summary {
            flex-direction: column;
            gap: 0.5rem;
            margin-left: 1rem;
            margin-right: 1rem;
        }
        .legend {
            justify-content: flex-start;
            margin-left: 1rem;
            margin-right: 1rem;
        }
        .btn-back {
            width: 100%;
            justify-content: center;
        }
    }
    
</style>
@endsection

@section('content')
<div class="container mt-4">

    <div class="btn-back-wrapper">
        <button class="btn-back" onclick="history.back()" aria-label="Kembali ke halaman sebelumnya">
            <i class="fas fa-arrow-left"></i> Kembali
        </button>
    </div>

    <p><strong>Total Mata Kuliah: {{ count($rekap) }}</strong></p>

    @foreach ($rekap as $data)
    <div class="card" role="region" aria-label="Rekap absensi mata kuliah {{ $data['mata_kuliah'] }}">
        <div class="card-header">
            {{ $data['mata_kuliah'] }} ({{ $data['sks'] }} SKS)<br>
            <small>Dosen: {{ $data['dosen'] }}</small>
        </div>
        <div class="card-body">
            <div class="table-container" tabindex="0" aria-label="Tabel absensi mata kuliah {{ $data['mata_kuliah'] }}">
                <table class="table" role="table" aria-describedby="summary-{{ $loop->index }}">
                    <thead>
                        <tr>
                            <th scope="col">Pertemuan</th>
                            @for ($i = 1; $i <= 16; $i++)
                                <th scope="col">{{ $i }}</th>
                            @endfor
                        </tr>
                        <tr>
                            <th scope="row">Tanggal</th>
                            @for ($i = 1; $i <= 16; $i++)
                                <th>
                                    <small class="text-muted" title="{{ $data['keterangan_pertemuan'][$i] ?? '-' }}">
                                        {{ \Illuminate\Support\Str::limit($data['keterangan_pertemuan'][$i] ?? '-', 12) }}
                                    </small>
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="row">Absensi</td>
                            @for ($i = 1; $i <= 16; $i++)
                                @php
                                    $type = strtolower(trim($data['absensiPerPertemuan'][$i] ?? '-'));
                                @endphp
                                <td>
                                    @if ($type === 'hadir')
                                        <span class="text-success fw-bold" title="Hadir">✔</span>
                                    @elseif ($type === 'izin')
                                        <span class="text-warning fw-bold" title="Izin">I</span>
                                    @elseif ($type === 'sakit')
                                        <span class="text-info fw-bold" title="Sakit">S</span>
                                    @elseif ($type === 'alpa')
                                        <span class="text-danger fw-bold" title="Alpa">×</span>
                                    @else
                                        <span class="text-muted" title="Tidak Ada Data">-</span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="summary-{{ $loop->index }}" class="stat-summary">
                {{-- <span class="bg-hadir">Jumlah Hadir: {{ $data['jumlah_hadir'] }}</span> --}}
                <span class="bg-persentase">Persentase: {{ $data['persentase'] }}%</span>
                <span class="{{ $data['persentase'] >= 75 ? 'bg-status-m' : 'bg-status-tm' }}">
                    Status: {{ $data['status'] }}
                </span>
            </div>

            <div class="legend" aria-label="Legenda status absensi">
                <span><span class="dot hadir"></span> Hadir</span>
                <span><span class="dot izin"></span> Izin</span>
                <span><span class="dot sakit"></span> Sakit</span>
                <span><span class="dot alpa"></span> Alpa</span>
                <span><span class="dot none"></span> Tidak Ada Data</span>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
