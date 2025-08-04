@extends('base.base-dash-index')

@section('menu', 'Pengajuan KRS')
@section('submenu', 'Detail Pengajuan')
@section('urlmenu', route('dosen.pengajuan-krs.index'))
@section('subdesc', 'Detail pengajuan KRS mahasiswa')
@section('title', 'Detail Pengajuan - Pengajuan KRS - Siakad By Internal Developer')

@section('content')
<section class="content py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-12 col-12">
            <!-- Student Information Card -->
            <div class="card shadow-sm mb-5 border-0 rounded-4">
                <div class="card-header bg-gradient-primary text-white rounded-top-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3 bg-white bg-opacity-20 p-2 rounded">
                            <i class="fas fa-user-graduate fs-5"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">Informasi Mahasiswa</h5>
                            <small class="opacity-75">Detail mahasiswa pengaju KRS</small>
                        </div>
                    </div>
                    <div class="d-flex">
                        @php
                            $stats = [
                                'total' => $semuaPengajuan->count(),
                                'approved' => $semuaPengajuan->where('status', 'Disetujui')->count(),
                                'pending' => $semuaPengajuan->where('status', 'Menunggu')->count(),
                                'rejected' => $semuaPengajuan->where('status', 'Ditolak')->count()
                            ];
                        @endphp
                        <div class="me-3 text-center">
                            <div class="fs-4 fw-bold">{{ $stats['approved'] }}</div>
                            <small class="opacity-75">Disetujui</small>
                        </div>
                        <div class="me-3 text-center">
                            <div class="fs-4 fw-bold">{{ $stats['pending'] }}</div>
                            <small class="opacity-75">Menunggu</small>
                        </div>
                        <div class="text-center">
                            <div class="fs-4 fw-bold">{{ $stats['rejected'] }}</div>
                            <small class="opacity-75">Ditolak</small>
                        </div>
                    </div>
                </div>
                <div class="card-body bg-white rounded-bottom-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Nama Mahasiswa</label>
                                <div class="fw-semibold">{{ $pengajuan->mahasiswa->name ?? 'Data tidak tersedia' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">NIM</label>
                                <div class="fw-semibold">{{ $pengajuan->mahasiswa->numb_nim ?? 'Data tidak tersedia' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Program Studi</label>
                                <div class="fw-semibold">
                                    @if($pengajuan->mahasiswa->kelas && $pengajuan->mahasiswa->kelas->pstudi)
                                        {{ $pengajuan->mahasiswa->kelas->pstudi->name }}
                                    @else
                                        Data tidak tersedia
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Kelas</label>
                                <div class="fw-semibold">
                                    @if($pengajuan->mahasiswa->kelas)
                                        {{ $pengajuan->mahasiswa->kelas->name }}
                                    @else
                                        Data tidak tersedia
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($stats['total'] > 0)
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ ($stats['approved']/$stats['total'])*100 }}%" 
                             aria-valuenow="{{ $stats['approved'] }}" 
                             aria-valuemin="0" 
                             aria-valuemax="{{ $stats['total'] }}"></div>
                        <div class="progress-bar bg-warning" role="progressbar" 
                             style="width: {{ ($stats['pending']/$stats['total'])*100 }}%" 
                             aria-valuenow="{{ $stats['pending'] }}" 
                             aria-valuemin="0" 
                             aria-valuemax="{{ $stats['total'] }}"></div>
                        <div class="progress-bar bg-danger" role="progressbar" 
                             style="width: {{ ($stats['rejected']/$stats['total'])*100 }}%" 
                             aria-valuenow="{{ $stats['rejected'] }}" 
                             aria-valuemin="0" 
                             aria-valuemax="{{ $stats['total'] }}"></div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Submission List Card -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
                    <div class="d-flex align-items-center">
                        <div class="me-3 bg-white bg-opacity-20 p-2 rounded">
                            <i class="fas fa-list-check fs-5"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold">Daftar Pengajuan KRS</h5>
                            <small class="opacity-75">Detail mata kuliah yang diajukan</small>
                        </div>
                    </div>
                    <div>
                        <a href="@yield('urlmenu')" class="btn btn-sm btn-light text-primary rounded-3 shadow-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body p-0 rounded-bottom-4">
                    @if($semuaPengajuan->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 w-100" id="tablePengajuanAll">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" style="width: 5%">#</th>
                                    <th style="width: 25%">Mata Kuliah</th>
                                    <th class="text-center" style="width: 10%">SKS</th>
                                    <th class="text-center" style="width: 10%">Tahun</th>
                                    <th class="text-center" style="width: 10%">Kelas</th>
                                    <th class="text-center" style="width: 10%">Status</th>
                                    <th class="text-center" style="width: 15%">Pengajuan</th>
                                    <th class="text-center" style="width: 15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($semuaPengajuan as $key => $item)
                                @php 
                                    $status = strtolower($item->status);
                                    $statusColor = match($status) {
                                        'menunggu' => 'warning',
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <tr class="hover-highlight">
                                    <td class="text-center small">{{ $key + 1 }}</td>
                                    <td>
                                        @if($item->jadwal && $item->jadwal->mataKuliah)
                                        <div class="fw-semibold">{{ $item->jadwal->mataKuliah->name }}</div>
                                        <small class="text-muted">{{ $item->jadwal->mataKuliah->code ?? 'Kode tidak tersedia' }}</small>
                                        @else
                                        <div class="fw-semibold text-danger">Mata kuliah tidak ditemukan</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->jadwal && $item->jadwal->mataKuliah)
                                            {{ $item->jadwal->mataKuliah->sks }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center small">
                                        @if($item->jadwal && $item->jadwal->tahunAkademik)
                                            {{ $item->jadwal->tahunAkademik->name }}
                                        @else
                                            Data tidak tersedia
                                        @endif
                                    </td>
                                    <td class="text-center small">
                                        @if($item->jadwal && $item->jadwal->jenisKelas)
                                            {{ $item->jadwal->jenisKelas->name }}
                                        @else
                                            Data tidak tersedia
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} rounded-pill px-3 py-1">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="text-center small" title="{{ $item->created_at->format('d M Y H:i:s') }}">
                                        {{ $item->created_at->diffForHumans() }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            @if ($status === 'menunggu')
                                                <form action="{{ route('dosen.pengajuan-krs.approve', $item->id) }}" method="POST" class="me-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-3 shadow-sm" 
                                                        data-bs-toggle="tooltip" data-bs-title="Setujui pengajuan"
                                                        onclick="return confirm('Setujui pengajuan ini?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-sm btn-danger me-1 rounded-3 shadow-sm" 
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}"
                                                    data-bs-toggle="tooltip" data-bs-title="Tolak pengajuan">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif

                                            <button type="button" class="btn btn-sm btn-primary rounded-3 shadow-sm" 
                                                data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}"
                                                data-bs-toggle="tooltip" data-bs-title="Edit pengajuan">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-info-circle fs-4 text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada data pengajuan</h5>
                        <p class="text-muted small">Mahasiswa ini belum mengajukan KRS</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @foreach ($semuaPengajuan as $item)
    @php $status = strtolower($item->status); @endphp
    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('dosen.pengajuan-krs.reject', $item->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="modal-content rounded-4 shadow">
                    <div class="modal-header bg-danger text-white rounded-top-4">
                        <h5 class="modal-title">Tolak Pengajuan KRS</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mahasiswa</label>
                            <input type="text" class="form-control" value="{{ $item->mahasiswa->name }} ({{ $item->mahasiswa->numb_nim }})" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mata Kuliah</label>
                            <input type="text" class="form-control" value="{{ $item->jadwal->mataKuliah->name ?? 'Mata kuliah tidak ditemukan' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan{{ $item->id }}" class="form-label fw-semibold">Alasan Penolakan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan{{ $item->id }}" rows="3" required placeholder="Berikan alasan penolakan pengajuan ini..."></textarea>
                            <div class="invalid-feedback">Harap isi alasan penolakan.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-3 shadow-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 shadow-sm">
                            <i class="fas fa-paper-plane me-1"></i> Kirim Penolakan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('dosen.pengajuan-krs.update', $item->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-content rounded-4 shadow">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title">Edit Pengajuan KRS</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mahasiswa</label>
                            <input type="text" class="form-control" value="{{ $item->mahasiswa->name }} ({{ $item->mahasiswa->numb_nim }})" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mata Kuliah</label>
                            <input type="text" class="form-control" value="{{ $item->jadwal->mataKuliah->name ?? 'Mata kuliah tidak ditemukan' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="status{{ $item->id }}" class="form-label fw-semibold">Status</label>
                            <select name="status" id="status{{ $item->id }}" class="form-select" required>
                                <option value="Menunggu" {{ $status === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="Disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="Ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                            <div class="invalid-feedback">Status harus dipilih.</div>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan{{ $item->id }}" class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" id="keterangan{{ $item->id }}" rows="3" class="form-control" placeholder="Tambahkan keterangan jika diperlukan...">{{ old('keterangan', $item->keterangan) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-3 shadow-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-3 shadow-sm">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</section>
@endsection

@push('styles')
<style>
    .hover-highlight:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    .progress-bar {
        transition: width 0.6s ease;
    }
    .card-header {
        transition: all 0.3s ease;
    }
    .badge {
        transition: all 0.2s ease;
    }
    .table-responsive {
        min-height: 300px;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable if there's data
        @if($semuaPengajuan->count() > 0)
        $('#tablePengajuanAll').DataTable({
            responsive: true,
            autoWidth: false,
            lengthMenu: [5, 10, 25, 50],
            language: {
                search: "Cari:",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                lengthMenu: "Tampilkan _MENU_ data",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            },
            dom: '<"top"f>rt<"bottom"lip><"clear">'
        });
        @endif

        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Form validation
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // Status change confirmation
        $('select[name="status"]').change(function() {
            if ($(this).val() === 'Ditolak' && !confirm('Apakah Anda yakin ingin menolak pengajuan ini?')) {
                $(this).val('Menunggu');
            }
        });
    });
</script>
@endpush