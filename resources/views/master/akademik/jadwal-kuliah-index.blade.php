@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        /* Stats cards */
        .bg-light-primary {
            background-color: rgba(67, 94, 190, 0.1);
        }

        .bg-light-success {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .bg-light-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-light-info {
            background-color: rgba(23, 162, 184, 0.1);
        }

        /* Card styling */
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .card-header {
            background: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
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
            border-bottom: 2px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            color: #6c757d;
            padding-top: 1rem;
            /* Added padding top */
            padding-bottom: 0.75rem;
            text-align: left;
            /* Default left align for headers */
        }

        .table td {
            vertical-align: middle;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            text-align: left;
            /* Default left align for cells */
        }

        /* Specific text alignment for certain columns */
        .table th.text-center,
        .table td.text-center {
            text-align: center !important;
        }

        /* Button styling */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        /* Form styling */
        .form-control,
        .form-select {
            border-radius: 5px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            padding: 0.5rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.25);
        }

        /* Badge styling */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* Collapsible form */
        .collapse {
            transition: all 0.3s ease;
        }

        .collapse.show {
            margin-top: 1rem;
        }

        /* Responsive styling */
        @media screen and (max-width: 768px) {

            .table td[data-label] .d-flex.align-items-center,
            .table td[data-label] .d-flex.flex-column.align-items-center {
                align-items: flex-end !important;
                text-align: right;
            }
        }

        @media (max-width: 768px) {

            .table-responsive table,
            .table-responsive thead,
            .table-responsive tbody,
            .table-responsive th,
            .table-responsive td,
            .table-responsive tr {
                display: block;
                width: 100%;
            }

            .table-responsive thead {
                display: none;
            }

            .table-responsive tr {
                margin-bottom: 1rem;
                border-bottom: 2px solid #eee;
            }

            .table-responsive td {
                position: relative;
                padding-left: 50%;
                text-align: left !important;
                /* Ensure left alignment in mobile view */
                border: none;
                border-bottom: 1px solid #eee;
            }

            .table-responsive td:before {
                position: absolute;
                top: 0;
                left: 0;
                width: 48%;
                padding-left: 1rem;
                white-space: nowrap;
                font-weight: bold;
                color: #888;
                content: attr(data-label);
            }
        }
    </style>
@endsection

@php
    $prefix = $prefix ?? '';
@endphp

@section('content')
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8 col-12 mb-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $pages }}</h5>
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm"
                        aria-expanded="false" aria-controls="collapseForm">
                        <i class="fas fa-plus-circle me-2"></i>Tambah Jadwal Kuliah
                    </button>
<form id="generateForm" class="d-inline-block">
    @csrf
    <input type="hidden" name="tahun_akademik_id" value="{{ request('tahun_akademik_id') ?? 1 }}">
    <button type="submit" class="btn btn-sm btn-success">
        <i class="fas fa-bolt"></i> Generate Semua Jadwal Otomatis
    </button>
</form>

<!-- Progress bar loading -->
<div class="progress mt-3 d-none" id="loadingBarWrapper" style="height: 20px;">
    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="loadingBar"
         role="progressbar" style="width: 0%">0%
    </div>
</div>

<!-- Tempat preview hasil generate -->
<div id="previewContainer" class="mt-4"></div>
                </div>
                
                <div class="card-body">
                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-primary rounded">
                                <h6 class="mb-2">Total Jadwal</h6>
                                <h3 class="mb-0">{{ count($jadwal_kuliah) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-success rounded">
                                <h6 class="mb-2">Jadwal Tatap Muka</h6>
                                <h3 class="mb-0">{{ $jadwal_kuliah->where('metode', 'Tatap Muka')->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="p-3 bg-light-warning rounded">
                                <h6 class="mb-2">Jadwal Teleconference</h6>
                                <h3 class="mb-0">{{ $jadwal_kuliah->where('metode', 'Teleconference')->count() }}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Collapsible Form -->
                    <div class="collapse" id="collapseForm">
                        <div class="card card-body border">
                            <h5 class="card-title mb-3">Tambah Jadwal Kuliah Baru</h5>
                            <form action="{{ route('akademik.jadwal-kuliah-handle') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="semester" class="form-label">Semester</label>
                                        <select class="form-select" name="semester" id="semester">
                                            <option value="">Pilih Semester</option>
                                            @for ($i = 1; $i <= 14; $i++)
                                                <option value="{{ $i }}">Semester {{ $i }}</option>
                                            @endfor
                                        </select>
                                        @error('semester')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="matkul_id" class="form-label">Mata Kuliah</label>
                                        <select class="form-select" name="matkul_id" id="matkul_id">
                                            <option value="">Pilih Mata Kuliah</option>
                                        </select>
                                        @error('matkul_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="dosen_id" class="form-label">Dosen</label>
                                        <select class="form-select" name="dosen_id" id="dosen_id">
                                            <option value="">Pilih Dosen</option>
                                        </select>
                                        @error('dosen_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="ruang_id" class="form-label">Ruang</label>
                                        <select class="form-select " name="ruang_id" id="ruang_id">
                                            <option value="">Pilih Ruang</option>
                                            @foreach ($ruang as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('ruang_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="jenis_kelas_id" class="form-label">Jenis Kelas</label>
                                        <select class="form-select " name="jenis_kelas_id" id="jenis_kelas_id">
                                            <option value="">Pilih Jenis Kelas</option>
                                            @foreach ($jenis_kelas as $jk)
                                                <option value="{{ $jk->id }}">{{ $jk->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('jenis_kelas_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="waktu_kuliah_id">Waktu Kuliah</label>
                                        <select name="waktu_kuliah_id[]" multiple class="form-select select2"
                                            id="waktu_kuliah_id">
                                            @foreach ($waktu_kuliah as $waktu)
                                                <option value="{{ $waktu->id }}">
                                                    {{ $waktu->name }}
                                                    ({{ \Carbon\Carbon::parse($waktu->time_start)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($waktu->time_ended)->format('H:i') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('waktu_kuliah_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="kelas_ids" class="form-label">Kelas</label>
                                        <select class="form-select select2-multiple" name="kelas_ids[]" id="kelas_ids"
                                            multiple="multiple">
                                            @foreach ($kelas as $k)
                                                <option value="{{ $k->id }}">{{ $k->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('kelas_ids')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="bsks" class="form-label">Beban SKS</label>
                                        <input type="number" class="form-control" name="bsks" id="bsks"
                                            min="1" max="24" placeholder="Contoh: 3">
                                        @error('bsks')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="pertemuan" class="form-label">Pertemuan</label>
                                        <input type="number" class="form-control" name="pertemuan" id="pertemuan"
                                            min="1" max="16" placeholder="Contoh: 1">
                                        @error('pertemuan')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="hari" class="form-label">Hari</label>
                                        <select class="form-select " name="hari" id="hari">
                                            <option value="">Pilih Hari</option>
                                            <option value="Senin">Senin</option>
                                            <option value="Selasa">Selasa</option>
                                            <option value="Rabu">Rabu</option>
                                            <option value="Kamis">Kamis</option>
                                            <option value="Jumat">Jumat</option>
                                            <option value="Sabtu">Sabtu</option>
                                            <option value="Minggu">Minggu</option>
                                        </select>
                                        @error('hari')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="metode" class="form-label">Metode</label>
                                        <select class="form-select" name="metode" id="metode">
                                            <option value="">Pilih Metode</option>
                                            <option value="Tatap Muka">Tatap Muka</option>
                                            <option value="Teleconference">Teleconference</option>
                                        </select>
                                        @error('metode')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control" name="tanggal" id="tanggal">
                                        @error('tanggal')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="link" class="form-label">Link (Opsional)</label>
                                        <input type="url" class="form-control" name="link" id="link"
                                            placeholder="Contoh: https://meet.google.com/xxx-yyyy-zzz">
                                        @error('link')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan
                                        </button>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tahun_akademik_id" class="form-label">Tahun Akademik</label>
                                        <select class="form-select" name="tahun_akademik_id" id="tahun_akademik_id">
                                            <option value="">Pilih Tahun Akademik</option>
                                            @foreach ($tahun_akademik as $tahun)
                                                <option value="{{ $tahun->id }}">{{ $tahun->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('tahun_akademik_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('akademik.jadwal-kuliah-render') }}" class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="semesterFilter" class="form-label">Filter Semester</label>
                            <select name="semester" id="semesterFilter" class="form-select">
                                <option value="">-- Semua Semester --</option>
                                @foreach ($semester_list as $semester)
                                    <option value="{{ $semester }}" {{ $selected_semester == $semester ? 'selected' : '' }}>
                                        Semester {{ $semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="ganjil_genap" class="form-label">Filter Ganjil / Genap</label>
                            <select name="ganjil_genap" id="ganjil_genap" class="form-select">
                                <option value="">-- Semua --</option>
                                <option value="ganjil" {{ request('ganjil_genap') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="genap" {{ request('ganjil_genap') == 'genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                        </div> 

                        <div class="col-md-3">
                            <label for="kelasFilter" class="form-label">Filter Kelas</label>
                            <select name="kelas_id" id="kelasFilter" class="form-select">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                                                
                        <div class="col-auto align-self-end">
                            <button type="submit" class="btn btn-primary">Tampilkan</button>
                        </div>
                    </form>

                <!-- Table -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>No</th>
                                <th>Mata Kuliah</th>
                                <th>Dosen</th>
                                <th>Kelas</th>
                                <th>Ruang</th>
                                <th>Hari</th>
                                <th>Waktu</th>
                                <th>Metode</th>
                                <th>Tahun Akademik</th>
                                <th>Semester</th> <!-- Tambahan Ganjil/Genap -->
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jadwal_kuliah as $key => $item)
                                <tr>
                                    <td class="text-center">{{ ++$key }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->mataKuliah->name }}</div>
                                        <div class="text-muted small">{{ $item->jenisKelas->name }}</div>
                                    </td>
                                    <td>{{ $item->dosen->name }}</td>
                                    <td>{{ $item->kelas->pluck('name')->join(', ') ?: '-' }}</td>
                                    <td>
                                        {{ $item->ruang_id == 0 ? 'Teleconference' : $item->ruang->name }}
                                    </td>
                                    <td>{{ $item->hari }}</td>
                                    <td>
                                        {!! $item->waktuKuliah->map(function ($w) {
                                            return "<div>" .
                                                $w->name . ' (' .
                                                \Carbon\Carbon::parse($w->time_start)->format('H:i') . ' - ' .
                                                \Carbon\Carbon::parse($w->time_ended)->format('H:i') . ")</div>";
                                        })->implode('') !!}
                                    </td>
                                    <td class="text-center">
                                        @if ($item->metode == 'Tatap Muka')
                                            <span class="badge bg-success text-white px-3 py-2 rounded-pill shadow-sm">
                                                Tatap Muka
                                            </span>
                                        @else
                                            <span class="badge bg-info text-white px-3 py-2 rounded-pill shadow-sm">
                                                {{ $item->metode }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ optional($item->tahunAkademik)->name ?? '-' }}</td>
                                    <td>
                                        {{ optional($item->mataKuliah)->semester ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm" role="group">
                                            {{-- Tombol Edit --}}
                                            <a href="#" class="btn btn-sm btn-outline-primary rounded-start"
                                                title="Edit Jadwal"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                data-bs-target="#editData{{ $item->code }}"
                                                onclick="$('#editData{{ $item->code }}').modal('show')">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>

                                            {{-- Tombol Hapus --}}
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete('{{ $item->code }}')"
                                                title="Hapus Jadwal"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top">
                                                <i class="fas fa-trash-alt me-1"></i> Hapus
                                            </button>

                                            <form id="delete-form-{{ $item->code }}"
                                                action="{{ route('akademik.jadwal-kuliah-delete', $item->code) }}"
                                                method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            {{-- Tombol Kelola Absensi --}}
                                            <a href="{{ route('master.akademik.absensi.jadwal', $item->id) }}"
                                                class="btn btn-sm btn-outline-success rounded-end"
                                                title="Lihat dan atur kehadiran mahasiswa"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top">
                                                <i class="bi bi-clipboard-check me-1"></i> Absensi
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                </div>
            </div>
        </div>

        <!-- Sidebar Content -->
        <div class="col-lg-4 col-12 mb-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Jadwal Kuliah</h5>
                </div>
                <div class="card-body">
                    <p>Bagian ini menampilkan informasi umum dan petunjuk terkait pengelolaan Jadwal Kuliah.</p>

                    <div class="alert alert-light-success">
                        <h6 class="">Petunjuk Penggunaan:</h6>
                        <ul class="mb-0">
                            <li>Klik tombol "Tambah Jadwal Kuliah" untuk menambahkan jadwal baru</li>
                            <li>Klik ikon <i class="fas fa-edit"></i> untuk mengedit data jadwal</li>
                            <li>Klik ikon <i class="fas fa-trash"></i> untuk menghapus jadwal</li>
                        </ul>
                    </div>

                    @if (count($jadwal_kuliah) > 0)
                        <div class="mt-4">
                            <h6>Jadwal Terbaru</h6>
                            <div class="list-group">
                                @foreach ($jadwal_kuliah->take(5) as $jk)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $jk->mataKuliah->name }}</h6>
                                            <small class="text-muted">{{ $jk->hari }}</small>
                                        </div>
                                        <p class="mb-1">{{ $jk->dosen->name }}</p>
                                        <small>{{ $jk->waktuKuliah->pluck('nama_waktu')->implode(', ') }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modals -->
    @foreach ($jadwal_kuliah as $item)
        <div class="modal fade" id="editData{{ $item->code }}" tabindex="-1" role="dialog"
            aria-labelledby="editModalLabel{{ $item->code }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <form action="{{ route('akademik.jadwal-kuliah-update', $item->code) }}" method="POST">
                        @method('patch')
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel{{ $item->code }}">Edit Jadwal Kuliah -
                                {{ $item->mataKuliah->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_dosen_id{{ $item->code }}" class="form-label">Dosen</label>
                                    <select class="form-select select2" name="dosen_id"
                                        id="edit_dosen_id{{ $item->code }}">
                                        <option value="">Pilih Dosen</option>
                                        @foreach ($dosen as $dsn)
                                            <option value="{{ $dsn->id }}"
                                                {{ $item->dosen_id == $dsn->id ? 'selected' : '' }}>{{ $dsn->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('dosen_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_ruang_id{{ $item->code }}" class="form-label">Ruang</label>
                                    <select class="form-select select2" name="ruang_id"
                                        id="edit_ruang_id{{ $item->code }}">
                                        <option value="">Pilih Ruang</option>
                                        @foreach ($ruang as $rwang)
                                            <option value="{{ $rwang->id }}"
                                                {{ $item->ruang_id == $rwang->id ? 'selected' : '' }}>{{ $rwang->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ruang_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_matkul_id{{ $item->code }}" class="form-label">Mata Kuliah</label>
                                    <select class="form-select select2" name="matkul_id"
                                        id="edit_matkul_id{{ $item->code }}">
                                        <option value="">Pilih Mata Kuliah</option>
                                        @foreach ($mata_kuliah as $mk)
                                            <option value="{{ $mk->id }}"
                                                {{ $item->matkul_id == $mk->id ? 'selected' : '' }}>{{ $mk->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('matkul_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_jenis_kelas_id{{ $item->code }}" class="form-label">Jenis
                                        Kelas</label>
                                    <select class="form-select select2" name="jenis_kelas_id"
                                        id="edit_jenis_kelas_id{{ $item->code }}">
                                        <option value="">Pilih Jenis Kelas</option>
                                        @foreach ($jenis_kelas as $jk)
                                            <option value="{{ $jk->id }}"
                                                {{ $item->jenis_kelas_id == $jk->id ? 'selected' : '' }}>
                                                {{ $jk->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis_kelas_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_waktu_kuliah_id{{ $item->code }}" class="form-label">Waktu
                                        Kuliah</label>
                                    <select name="waktu_kuliah_id[]" multiple class="form-select select2"
                                        id="edit_waktu_kuliah_id{{ $item->code }}">
                                        @php
                                            // Ambil array id waktu kuliah yang sudah dipilih agar bisa auto-selected
                                            $selectedWaktu = $item->waktuKuliah
                                                ? $item->waktuKuliah->pluck('id')->toArray()
                                                : [];
                                        @endphp
                                        @foreach ($waktu_kuliah as $waktu)
                                            <option value="{{ $waktu->id }}"
                                                {{ in_array($waktu->id, $selectedWaktu) ? 'selected' : '' }}>
                                                {{ $waktu->name }}
                                                ({{ \Carbon\Carbon::parse($waktu->time_start)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($waktu->time_ended)->format('H:i') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('waktu_kuliah_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="edit_kelas_ids{{ $item->code }}" class="form-label">Kelas</label>
                                    <select class="form-select select2-multiple" name="kelas_ids[]"
                                        id="edit_kelas_ids{{ $item->code }}" multiple="multiple">
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}"
                                                {{ in_array($k->id, $item->kelas->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $k->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kelas_ids')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_bsks{{ $item->code }}" class="form-label">Beban SKS</label>
                                    <input type="number" class="form-control" name="bsks"
                                        id="edit_bsks{{ $item->code }}" min="1" max="24"
                                        value="{{ $item->bsks }}">
                                    @error('bsks')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_pertemuan{{ $item->code }}" class="form-label">Pertemuan</label>
                                    <input type="number" class="form-control" name="pertemuan"
                                        id="edit_pertemuan{{ $item->code }}" min="1" max="16"
                                        value="{{ $item->pertemuan }}">
                                    @error('pertemuan')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_hari{{ $item->code }}" class="form-label">Hari</label>
                                    <select class="form-select select2" name="hari"
                                        id="edit_hari{{ $item->code }}">
                                        <option value="">Pilih Hari</option>
                                        <option value="Senin" {{ $item->hari == 'Senin' ? 'selected' : '' }}>Senin
                                        </option>
                                        <option value="Selasa" {{ $item->hari == 'Selasa' ? 'selected' : '' }}>Selasa
                                        </option>
                                        <option value="Rabu" {{ $item->hari == 'Rabu' ? 'selected' : '' }}>Rabu
                                        </option>
                                        <option value="Kamis" {{ $item->hari == 'Kamis' ? 'selected' : '' }}>Kamis
                                        </option>
                                        <option value="Jumat" {{ $item->hari == 'Jumat' ? 'selected' : '' }}>Jumat
                                        </option>
                                        <option value="Sabtu" {{ $item->hari == 'Sabtu' ? 'selected' : '' }}>Sabtu
                                        </option>
                                        <option value="Minggu" {{ $item->hari == 'Minggu' ? 'selected' : '' }}>Minggu
                                        </option>
                                    </select>
                                    @error('hari')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_metode{{ $item->code }}" class="form-label">Metode</label>
                                    <select class="form-select select2" name="metode"
                                        id="edit_metode{{ $item->code }}">
                                        <option value="">Pilih Metode</option>
                                        <option value="Tatap Muka" {{ $item->metode == 'Tatap Muka' ? 'selected' : '' }}>
                                            Tatap Muka</option>
                                        <option value="Teleconference"
                                            {{ $item->metode == 'Teleconference' ? 'selected' : '' }}>Teleconference
                                        </option>
                                    </select>
                                    @error('metode')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_tanggal{{ $item->code }}" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal"
                                        id="edit_tanggal{{ $item->code }}" value="{{ $item->tanggal }}">
                                    @error('tanggal')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="edit_link{{ $item->code }}" class="form-label">Link (Opsional)</label>
                                    <input type="url" class="form-control" name="link"
                                        id="edit_link{{ $item->code }}" value="{{ $item->link }}"
                                        placeholder="Contoh: https://meet.google.com/xxx-yyyy-zzz">
                                    @error('link')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tahun_akademik_id" class="form-label">Tahun Akademik</label>
                                    <select class="form-select" name="tahun_akademik_id" id="tahun_akademik_id">
                                        <option value="">Pilih Tahun Akademik</option>
                                        @foreach ($tahun_akademik as $tahun)
                                            <option value="{{ $tahun->id }}"
                                                {{ $item->tahun_akademik_id == $tahun->id ? 'selected' : '' }}>
                                                {{ $tahun->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tahun_akademik_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@section('custom-js')
<script src="{{ asset('dist') }}/assets/extensions/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // DataTable
        $('.table').DataTable({
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
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "Semua"]
            ],
            order: [[0, 'asc']],
            columnDefs: [{
                orderable: false,
                targets: -1
            }]
        });

        // Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        $('.select2-multiple').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih Kelas',
            allowClear: true
        });

        $('#waktu_kuliah_id').select2({
            placeholder: "Pilih waktu kuliah",
            allowClear: true
        });

        // Event saat semester berubah
        $('#semester').change(function () {
            const semester = $(this).val();
            $('#matkul_id').html('<option value="">Memuat...</option>');
            $('#dosen_id').html('<option value="">Pilih Dosen</option>');

            if (semester) {
                $.ajax({
                    url: '{{ route("api.get-matkul-by-semester") }}',
                    method: 'GET',
                    data: { semester: semester },
                    success: function (data) {
                        let options = '<option value="">Pilih Mata Kuliah</option>';
                        data.forEach(function (mk) {
                            options += `<option value="${mk.id}">${mk.name}</option>`;
                        });
                        $('#matkul_id').html(options);
                    },
                    error: function () {
                        $('#matkul_id').html('<option value="">Gagal memuat data</option>');
                    }
                });
            } else {
                $('#matkul_id').html('<option value="">Pilih Mata Kuliah</option>');
            }
        });

        // Event saat mata kuliah berubah
        $('#matkul_id').change(function () {
            const matkulId = $(this).val();
            $('#dosen_id').html('<option value="">Memuat...</option>');

            if (matkulId) {
                $.ajax({
                    url: '{{ route("api.get-dosen-by-matkul") }}',
                    method: 'GET',
                    data: { matkul_id: matkulId },
                    success: function (res) {
                        let options = '<option value="">Pilih Dosen</option>';
                        if (res.success && res.dosen.length > 0) {
                            res.dosen.forEach(function (d) {
                                options += `<option value="${d.id}">${d.name}</option>`;
                            });
                        } else {
                            options = '<option value="">Tidak ada dosen</option>';
                        }
                        $('#dosen_id').html(options);
                    },
                    error: function () {
                        $('#dosen_id').html('<option value="">Gagal mengambil data</option>');
                    }
                });
            } else {
                $('#dosen_id').html('<option value="">Pilih Dosen</option>');
            }
        });
    });

    // SweetAlert konfirmasi delete
    function confirmDelete(code) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data jadwal kuliah yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + code).submit();
            }
        });
    }

    // Submit form generate jadwal otomatis
    document.getElementById('generateForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Yakin generate seluruh jadwal?',
            text: "Proses ini akan mengatur ulang jadwal bentrok.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, lanjutkan!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(this);

                // Tampilkan progress bar
                const bar = document.getElementById('loadingBar');
                const wrapper = document.getElementById('loadingBarWrapper');
                wrapper.classList.remove('d-none');
                bar.style.width = '0%';
                bar.innerText = '0%';

                let progress = 0;
                const interval = setInterval(() => {
                    if (progress < 90) {
                        progress += 5;
                        bar.style.width = progress + '%';
                        bar.innerText = progress + '%';
                    }
                }, 150);

                fetch("{{ route('akademik.jadwal-kuliah-index') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    clearInterval(interval);
                    bar.style.width = '100%';
                    bar.innerText = '100%';

                    setTimeout(() => {
                        wrapper.classList.add('d-none');
                        bar.style.width = '0%';
                    }, 500);

                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success');
                        if (data.html) {
                            document.getElementById('previewContainer').innerHTML = data.html;
                        }
                    } else {
                        Swal.fire('Gagal', data.message ?? 'Terjadi kesalahan.', 'error');
                    }
                })
                .catch(err => {
                    clearInterval(interval);
                    wrapper.classList.add('d-none');
                    Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
                });
            }
        });
    });
</script>
@endsection
