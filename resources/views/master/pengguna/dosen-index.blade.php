@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" />
    <style>
        /* Tambahan CSS jika diperlukan */
    </style>
@endsection

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8 col-12 mb-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $pages }}</h5>
                <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="false" aria-controls="collapseForm">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Pengguna
                </button>
            </div>
            <div class="card-body">

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-2">
                        <div class="p-3 bg-light-primary rounded">
                            <h6 class="mb-2">Total Pengguna</h6>
                            <h3 class="mb-0">{{ $dosen ? count($dosen) : 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="p-3 bg-light-success rounded">
                            <h6 class="mb-2">Pengguna Aktif</h6>
                            <h3 class="mb-0">{{ $dosen ? $dosen->where('dsn_stat', 'Aktif')->count() : 0 }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Collapsible Form -->
                <div class="collapse" id="collapseForm">
                    <div class="card card-body border">
                        <h5 class="card-title mb-3">Tambah Pengguna Baru</h5>
                        <form action="{{ route($spref . 'pengguna.dosen-handle') }}" method="post">
                            @csrf
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" required value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" required value="{{ old('email') }}">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">NIDN </label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" required value="{{ old('phone') }}">
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" required>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="dsn_stat" class="form-label">Status Dosen</label>
                                    <select class="form-select @error('dsn_stat') is-invalid @enderror" name="dsn_stat" id="dsn_stat" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Aktif" {{ old('dsn_stat') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="Tidak Aktif" {{ old('dsn_stat') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                    @error('dsn_stat')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Wali Kelas dropdown -->
                                <div class="col-md-6 mb-3">
                                    <label for="wali" class="form-label">Wali Kelas (Opsional)</label>
                                    <select class="form-select @error('wali') is-invalid @enderror" name="wali" id="wali">
                                        <option value="">-- Pilih Wali Kelas --</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id }}" {{ old('wali') == $k->id ? 'selected' : '' }}>
                                                {{ $k->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('wali')
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

                <!-- Table -->
                <div class="table-responsive mt-3">
                    <table class="table table-striped table-bordered" id="tableDosen">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>NIDN</th>
                                <th>Status</th>
                                <th>Wali Kelas</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dosen as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->dsn_stat }}</td>
                                    <td>{{ optional($item->waliKelas)->name ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route($spref.'pengguna.dosen-views', $item->code) }}" class="btn btn-sm btn-secondary" data-bs-toggle="tooltip" title="Lihat Pengguna">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#editData{{ $item->code }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit Pengguna">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route($spref . 'pengguna.dosen-delete', $item->code) }}" method="POST" class="d-inline" id="delete-form-{{ $item->code }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" data-confirm-delete="true" data-bs-toggle="tooltip" title="Hapus Pengguna" onclick="confirmDelete('{{ $item->code }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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

    <!-- Sidebar -->
    <div class="col-lg-4 col-12 mb-2">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Informasi Pengguna</h5>
            </div>
            <div class="card-body">
                <p>Bagian ini menampilkan informasi umum dan petunjuk terkait pengelolaan Pengguna.</p>

                <div class="alert alert-light-success">
                    <h6>Petunjuk Penggunaan:</h6>
                    <ul class="mb-0">
                        <li>Klik tombol "Tambah Pengguna" untuk menambahkan pengguna baru</li>
                        <li>Klik ikon <i class="fas fa-edit"></i> untuk mengedit data pengguna</li>
                        <li>Klik ikon <i class="fas fa-trash"></i> untuk menghapus pengguna</li>
                    </ul>
                </div>

                @if(count($dosen) > 0)
                    <div class="mt-4">
                        <h6>Pengguna Terbaru</h6>
                        <div class="list-group">
                            @foreach($dosen->sortByDesc('created_at')->take(3) as $user)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $user->name }}</h6>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $user->email }}</p>
                                    <small class="text-muted">{{ $user->phone }}</small>
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
@foreach ($dosen as $item)
    <div class="modal fade" id="editData{{ $item->code }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $item->code }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route($spref . 'pengguna.dosen-update', $item->code) }}" method="POST">
                    @method('patch')
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel{{ $item->code }}">Edit Pengguna - {{ $item->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="edit_name{{ $item->code }}" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="name" id="edit_name{{ $item->code }}" value="{{ old('name', $item->name) }}" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_email{{ $item->code }}" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email{{ $item->code }}" value="{{ old('email', $item->email) }}" required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_phone{{ $item->code }}" class="form-label">NIDN</label>
                                <input type="text" class="form-control" name="phone" id="edit_phone{{ $item->code }}" value="{{ old('phone', $item->phone) }}" required>
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_password{{ $item->code }}" class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" class="form-control" name="password" id="edit_password{{ $item->code }}">
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="edit_dsn_stat{{ $item->code }}" class="form-label">Status Dosen</label>
                                <select class="form-select" name="dsn_stat" id="edit_dsn_stat{{ $item->code }}" required>
                                    <option value="">Pilih Status</option>
                                    <option value="Aktif" {{ old('dsn_stat', $item->dsn_stat) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Tidak Aktif" {{ old('dsn_stat', $item->dsn_stat) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('dsn_stat')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Edit wali kelas -->
                            <div class="col-md-6 mb-3">
                                <label for="edit_wali{{ $item->code }}" class="form-label">Wali Kelas (Opsional)</label>
                                <select class="form-select" name="wali" id="edit_wali{{ $item->code }}">
                                    <option value="">-- Pilih Wali Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}" {{ old('wali', optional($item->waliKelas)->id) == $k->id ? 'selected' : '' }}>
                                            {{ $k->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('wali')
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
    <script src="{{ asset('dist/assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tableDosen').DataTable({
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
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
            });
        });

        function confirmDelete(code) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pengguna yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + code).submit();
                }
            });
        }
    </script>
@endsection
