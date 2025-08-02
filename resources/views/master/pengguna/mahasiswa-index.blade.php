@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" />
    <style>
        /* Styling tetap sama seperti sebelumnya */
        /* ... (sesuai kode yang sudah ada) ... */
    </style>
@endsection

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8 col-12 mb-2">
                <!-- Modal Import Mahasiswa -->
                <div class="modal fade" id="importMahasiswaModal" tabindex="-1" aria-labelledby="importMahasiswaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route($spref . 'pengguna.mahasiswa-import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importMahasiswaModalLabel">Import Data Mahasiswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file_import" class="form-label">Pilih File Excel (.xls / .xlsx)</label>
                            <input type="file" name="file" id="file_import" class="form-control" accept=".xls,.xlsx" required>
                            <small class="text-muted">
                                Download format: <a href="{{ asset('format-import/format-mahasiswa.xlsx') }}" download>format-mahasiswa.xlsx</a>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i> Import</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $pages }}</h5>

                <!-- Tombol Import -->
                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importMahasiswaModal">
                    <i class="fas fa-file-import me-1"></i> Import
                </button>

                <!-- Tombol Export Semua Mahasiswa -->
                <a href="{{ route($spref . 'pengguna.mahasiswa-export') }}" class="btn btn-outline-primary" title="Export Semua Mahasiswa (1 Sheet)">
                    <i class="fas fa-file-export me-1"></i> Export Semua
                </a>

                <!-- Tombol Export Mahasiswa Per Semester (Multi Sheet) -->
                <button id="btnExport" class="btn btn-outline-info" title="Export Mahasiswa Per Semester (Multi Sheet)">
                    <i class="fas fa-file-export me-1"></i> Export Per Semester
                </button>

                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="false" aria-controls="collapseForm">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Mahasiswa
                </button>
            </div>

            <div class="card-body">
                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-2">
                        <div class="p-3 bg-light-primary rounded">
                            <h6 class="mb-2">Total Mahasiswa</h6>
                            <h3 class="mb-0">{{ $mahasiswa->count() ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="p-3 bg-light-success rounded">
                            <h6 class="mb-2">Mahasiswa Aktif</h6>
                            <h3 class="mb-0">{{ $mahasiswa->where('type', 1)->count() ?? 0 }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Collapsible Form -->
                <div class="collapse" id="collapseForm">
                    <div class="card card-body border">
                        <h5 class="card-title mb-3">Tambah Mahasiswa Baru</h5>
                        <form action="{{ route($spref . 'pengguna.mahasiswa-handle') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required />
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required />
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" required />
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" required />
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="numb_nim" class="form-label">NIM</label>
                                    <input type="text" name="numb_nim" id="numb_nim" class="form-control" value="{{ old('numb_nim') }}" required />
                                    @error('numb_nim')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="prodi_id" class="form-label">Program Studi</label>
                                    <select name="prodi_id" id="prodi_id" class="form-select" required>
                                        <option value="">Pilih Program Studi</option>
                                        @foreach ($prodi as $p)
                                            <option value="{{ $p->id }}" {{ old('prodi_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('prodi_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="kelas_id" class="form-label">Kelas</label>
                                    <select name="kelas_id" id="kelas_id" class="form-select" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                                {{ $k->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">Status</label>
                                    <select name="type" id="type" class="form-select" required>
                                        <option value="">Pilih Status</option>
                                        <option value="0" {{ old('type') === '0' ? 'selected' : '' }}>Calon Mahasiswa Baru</option>
                                        <option value="1" {{ old('type') == 1 ? 'selected' : '' }}>Mahasiswa Aktif</option>
                                        <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>Mahasiswa Non-Aktif</option>
                                        <option value="3" {{ old('type') == 3 ? 'selected' : '' }}>Mahasiswa Alumni</option>
                                    </select>
                                    @error('type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <input type="number" name="semester" id="semester" class="form-control" min="0" max="14" value="{{ old('semester', 0) }}" />
                                    @error('semester')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="filterSemester" class="form-label">Filter Semester</label>
                        <select id="filterSemester" class="form-select">
                            <option value="">Semua Semester</option>
                            @for ($i = 1; $i <= 14; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="filterKelas" class="form-label">Filter Kelas</label>
                        <select id="filterKelas" class="form-select">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->name }}">{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <!-- Table -->
                <div class="table-responsive mt-3">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 40px;">No</th>
                                <th>Nama</th>
                                <th>NIM</th>
                                <th>Program Studi</th>
                                <th>Semester</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mahasiswa as $key => $item)
                                <tr 
                                    data-semester="{{ $item->semester }}" 
                                    data-kelas="{{ $item->kelas->name ?? '' }}"
                                >
                                    <td class="text-center" data-label="No">{{ $loop->iteration }}</td>
                                    <td data-label="Nama">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->photo ?? asset('images/default-user.png') }}" alt="{{ $item->name }}" class="rounded-circle me-2" style="width:40px; height:40px; object-fit:cover; border:2px solid #e9ecef;">
                                            <div>
                                                <span class="fw-bold">{{ $item->name }}</span><br>
                                                <small class="text-muted">{{ $item->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="NIM">{{ $item->numb_nim }}</td>
                                    <td data-label="Program Studi">{{ $item->prodi->name ?? '-' }}</td>
                                    <td data-label="Semester">{{ $item->semester }}</td>
                                    <td data-label="Kelas">{{ $item->kelas->name ?? '-' }}</td>
                                    <td data-label="Status">
                                        @php
                                            $statusClass = [
                                                0 => 'bg-light-info text-info',
                                                1 => 'bg-light-success text-success',
                                                2 => 'bg-light-warning text-warning',
                                                3 => 'bg-light-secondary text-secondary',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClass[$item->raw_type] ?? 'bg-light-secondary text-secondary' }}">
                                            {{ $item->type }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Actions">
                                            <a href="{{ route($spref . 'pengguna.mahasiswa-views', $item->code) }}" class="btn btn-sm btn-secondary" title="Lihat Mahasiswa">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editData{{ $item->code }}" title="Edit Mahasiswa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route($spref . 'pengguna.mahasiswa-delete', $item->code) }}" method="POST" class="d-inline" id="delete-form-{{ $item->code }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $item->code }}')" title="Hapus Mahasiswa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Data mahasiswa tidak ditemukan.</td>
                                </tr>
                            @endforelse
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
                <h5 class="card-title">Informasi Mahasiswa</h5>
            </div>
            <div class="card-body">
                <p>Bagian ini menampilkan informasi umum dan petunjuk terkait pengelolaan data Mahasiswa.</p>

                <div class="alert alert-light-success">
                    <h6 class="">Petunjuk Penggunaan:</h6>
                    <ul class="mb-0">
                        <li>Klik tombol "Tambah Mahasiswa" untuk menambahkan mahasiswa baru</li>
                        <li>Klik ikon <i class="fas fa-eye"></i> untuk melihat detail mahasiswa</li>
                        <li>Klik ikon <i class="fas fa-edit"></i> untuk mengedit data mahasiswa</li>
                        <li>Klik ikon <i class="fas fa-trash"></i> untuk menghapus mahasiswa</li>
                    </ul>
                </div>

                @if($mahasiswa->count() > 0)
                    <div class="mt-4">
                        <h6>Mahasiswa Terbaru</h6>
                        <div class="list-group">
                            @foreach($mahasiswa->sortByDesc('created_at')->take(3) as $mhs)
                                <a href="{{ route($spref . 'pengguna.mahasiswa-views', $mhs->code) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $mhs->name }}</h6>
                                        <small class="text-muted">{{ $mhs->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $mhs->numb_nim }}</p>
                                    <small class="text-muted">{{ $mhs->prodi->name ?? '-' }}</small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Modals -->
@foreach ($mahasiswa as $item)
    <div class="modal fade" id="editData{{ $item->code }}" tabindex="-1" aria-labelledby="editModalLabel{{ $item->code }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route($spref . 'pengguna.mahasiswa-update', $item->code) }}" method="POST" autocomplete="off">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel{{ $item->code }}">Edit Mahasiswa - {{ $item->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_name{{ $item->code }}" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="name" id="edit_name{{ $item->code }}" value="{{ old('name', $item->name) }}" required />
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_email{{ $item->code }}" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email{{ $item->code }}" value="{{ old('email', $item->email) }}" required />
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_phone{{ $item->code }}" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" name="phone" id="edit_phone{{ $item->code }}" value="{{ old('phone', $item->phone) }}" required />
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_password{{ $item->code }}" class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" class="form-control" name="password" id="edit_password{{ $item->code }}" autocomplete="new-password" />
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_numb_nim{{ $item->code }}" class="form-label">NIM</label>
                                <input type="text" class="form-control" name="numb_nim" id="edit_numb_nim{{ $item->code }}" value="{{ old('numb_nim', $item->numb_nim) }}" required />
                                @error('numb_nim')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_prodi_id{{ $item->code }}" class="form-label">Program Studi</label>
                                <select class="form-select" name="prodi_id" id="edit_prodi_id{{ $item->code }}" required>
                                    <option value="">Pilih Program Studi</option>
                                    @foreach ($prodi as $p)
                                        <option value="{{ $p->id }}" {{ old('prodi_id', $item->prodi_id) == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('prodi_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_kelas_id{{ $item->code }}" class="form-label">Kelas</label>
                                <select class="form-select" name="kelas_id" id="edit_kelas_id{{ $item->code }}" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ $k->id }}" {{ old('kelas_id', $item->kelas_id) == $k->id ? 'selected' : '' }}>
                                            {{ $k->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelas_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_type{{ $item->code }}" class="form-label">Status</label>
                                <select class="form-select" name="type" id="edit_type{{ $item->code }}" required>
                                    <option value="">Pilih Status</option>
                                    <option value="0" {{ old('type', $item->raw_type) == 0 ? 'selected' : '' }}>Calon Mahasiswa Baru</option>
                                    <option value="1" {{ old('type', $item->raw_type) == 1 ? 'selected' : '' }}>Mahasiswa Aktif</option>
                                    <option value="2" {{ old('type', $item->raw_type) == 2 ? 'selected' : '' }}>Mahasiswa Non-Aktif</option>
                                    <option value="3" {{ old('type', $item->raw_type) == 3 ? 'selected' : '' }}>Mahasiswa Alumni</option>
                                </select>
                                @error('type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_semester{{ $item->code }}" class="form-label">Semester</label>
                                <input type="number" name="semester" id="edit_semester{{ $item->code }}" class="form-control" min="0" max="14" value="{{ old('semester', $item->semester) }}" />
                                @error('semester')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection

@section('custom-js')
<script>
    // Tooltip bootstrap init
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Konfirmasi hapus
    function confirmDelete(code) {
        if (confirm('Yakin ingin menghapus mahasiswa ini?')) {
            document.getElementById('delete-form-' + code).submit();
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const semesterFilter = document.getElementById('filterSemester');
        const kelasFilter = document.getElementById('filterKelas');
        const rows = document.querySelectorAll('table tbody tr');

        function filterRows() {
            const semester = semesterFilter.value;
            const kelas = kelasFilter.value;

            rows.forEach(row => {
                const rowSemester = row.getAttribute('data-semester');
                const rowKelas = row.getAttribute('data-kelas');

                const matchSemester = semester === "" || rowSemester === semester;
                const matchKelas = kelas === "" || rowKelas === kelas;

                if (matchSemester && matchKelas) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        semesterFilter.addEventListener('change', filterRows);
        kelasFilter.addEventListener('change', filterRows);

        // Export button event
        document.getElementById('btnExport').addEventListener('click', function () {
            const semester = semesterFilter.value;
            const kelas = kelasFilter.value;

            if (!semester) {
                alert('Pilih semester terlebih dahulu');
                return;
            }

            let url = `{{ route($spref . 'pengguna.mahasiswa-export-per-semester') }}?semester=${encodeURIComponent(semester)}`;
            if (kelas) {
                url += `&kelas=${encodeURIComponent(kelas)}`;
            }

            window.location.href = url;
        });
    });
</script>
@endsection

