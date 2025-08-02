@extends('core-themes.core-backpage')

@section('title', 'Kenaikan Semester - Super Admin')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        /* Card styling */
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 10px;
        }

        .card-header {
            background: none;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Table styling */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid rgba(0,0,0,0.05);
            font-weight: 600;
            color: #6c757d;
            padding-top: 1rem;
            padding-bottom: 0.75rem;
            text-align: left;
        }

        .table td {
            vertical-align: middle;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            text-align: left;
        }

        .table th.text-center, .table td.text-center {
            text-align: center !important;
        }

        /* Badge styling */
        .badge-status {
            font-weight: 600;
            padding: 0.35em 0.75em;
            font-size: 0.85rem;
            border-radius: 20px;
            user-select: none;
        }
        .badge-success {
            background-color: #198754;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        /* Button styling */
        .btn-sm {
            border-radius: 5px;
            padding: 0.25rem 0.75rem;
            font-weight: 600;
            box-shadow: 0 3px 8px rgba(67,94,190,0.15);
            transition: all 0.3s ease;
        }
        .btn-sm:hover {
            box-shadow: 0 5px 12px rgba(67,94,190,0.3);
            transform: translateY(-2px);
        }

        /* Filter group styling */
        .filter-group {
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .filter-group label {
            font-weight: 600;
            white-space: nowrap;
        }
        .filter-group select {
            max-width: 180px;
            min-width: 140px;
            border-radius: 5px;
            padding: 0.4rem 1rem;
            border: 1px solid rgba(0,0,0,0.1);
            transition: border-color 0.3s ease;
        }
        .filter-group select:focus {
            border-color: #435ebe;
            outline: none;
            box-shadow: 0 0 8px rgba(67,94,190,0.3);
        }
    </style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="fw-bold"><i class="fas fa-layer-group me-2"></i>Kenaikan Semester Mahasiswa</h3>
        <a href="{{ route('master.akademik.kenaikan-semester.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-sync-alt me-1"></i> Refresh
        </a>
    </div>

    {{-- Info Summary --}}
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-2">
            <div class="p-3 bg-light-primary rounded text-center">
                <h6>Total Mahasiswa</h6>
                <h3 class="mb-0">{{ $mahasiswas->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="p-3 bg-light-success rounded text-center">
                <h6>Angkatan Terdaftar</h6>
                <h3 class="mb-0">{{ $angkatanList->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="p-3 bg-light-info rounded text-center">
                <h6>Semester Terdaftar</h6>
                <h3 class="mb-0">{{ $semesterList->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-2">
            <div class="p-3 bg-light-warning rounded text-center">
                <h6>Kelas Terdaftar</h6>
                <h3 class="mb-0">{{ $kelasList->count() }}</h3>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="d-flex filter-group mb-3 align-items-center">
        <label for="filterAngkatan" class="mb-0">Angkatan:</label>
        <select id="filterAngkatan" class="form-select form-select-sm">
            <option value="all" selected>Semua Angkatan</option>
            @foreach($angkatanList as $angkatan)
                <option value="{{ strtolower($angkatan) }}">{{ $angkatan }}</option>
            @endforeach
        </select>

        <label for="filterSemester" class="mb-0">Semester:</label>
        <select id="filterSemester" class="form-select form-select-sm">
            <option value="all" selected>Semua Semester</option>
            @foreach($semesterList as $semester)
                <option value="{{ strtolower($semester) }}">{{ $semester }}</option>
            @endforeach
        </select>

        <label for="filterKelas" class="mb-0">Kelas:</label>
        <select id="filterKelas" class="form-select form-select-sm">
            <option value="all" selected>Semua Kelas</option>
            @foreach($kelasList as $kelas)
                <option value="{{ strtolower($kelas) }}">{{ $kelas }}</option>
            @endforeach
        </select>
    </div>

    <div class="card shadow-sm rounded-4">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle text-sm" id="tableMahasiswa" aria-label="Tabel Kenaikan Semester Mahasiswa">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th class="text-center">Semester Saat Ini</th>
                            <th class="text-center">Angkatan</th>
                            <th class="text-center">Kelas</th>
                            <th class="text-center">Status Kenaikan</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswas as $index => $mhs)
                            @php
                                $cek = \App\Services\KenaikanSemesterService::cekKenaikanSemester($mhs->id, $currentTahunAkademikId);
                                $angkatan = strtolower($mhs->angkatan ?? '-');
                                $kelasName = strtolower($mhs->kelas->name ?? '-');
                            @endphp
                            <tr 
                                data-angkatan="{{ $angkatan }}" 
                                data-semester="{{ strtolower($mhs->semester) }}" 
                                data-kelas="{{ $kelasName }}"
                                aria-label="Baris data mahasiswa {{ $mhs->name }}"
                            >
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $mhs->name }}</td>
                                <td>{{ $mhs->numb_nim ?? '-' }}</td>
                                <td>{{ $mhs->programStudi->name ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark">{{ $mhs->semester ?? '-' }}</span>
                                </td>
                                <td class="text-center">{{ ucfirst($angkatan) }}</td>
                                <td class="text-center">{{ ucfirst($kelasName) }}</td>
                                <td class="text-center">
                                    @if ($cek['naik'])
                                        <span class="badge badge-status badge-success">Memenuhi Syarat</span>
                                    @else
                                        <span class="badge badge-status badge-danger">Belum Memenuhi</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($cek['naik'])
                                        <form method="POST" action="{{ route('superadmin.kenaikan-semester.naik', $mhs->id) }}" onsubmit="return confirm('Yakin ingin menaikkan semester mahasiswa ini?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" aria-label="Naikkan semester {{ $mhs->name }}">
                                                <i class="fas fa-arrow-up me-1"></i> Naikkan
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled aria-label="Belum bisa naikkan semester {{ $mhs->name }}">
                                            <i class="fas fa-ban me-1"></i> Belum Bisa
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <em><i class="fas fa-info-circle me-1"></i> Data mahasiswa belum tersedia.</em>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
    <script src="{{ asset('dist/assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#tableMahasiswa').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: -1 } // aksi
                ]
            });

            // Custom filtering for Angkatan, Semester, Kelas
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    const filterAngkatan = $('#filterAngkatan').val().toLowerCase();
                    const filterSemester = $('#filterSemester').val().toLowerCase();
                    const filterKelas = $('#filterKelas').val().toLowerCase();

                    const row = $('#tableMahasiswa tbody tr').eq(dataIndex);
                    const rowAngkatan = row.data('angkatan');
                    const rowSemester = row.data('semester');
                    const rowKelas = row.data('kelas');

                    if ((filterAngkatan === 'all' || filterAngkatan === rowAngkatan) &&
                        (filterSemester === 'all' || filterSemester === rowSemester) &&
                        (filterKelas === 'all' || filterKelas === rowKelas)) {
                        return true;
                    }
                    return false;
                }
            );

            // Event listeners for filter select
            $('#filterAngkatan, #filterSemester, #filterKelas').on('change', function() {
                table.draw();
            });
        });
    </script>
@endsection
