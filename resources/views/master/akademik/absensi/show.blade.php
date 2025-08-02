@extends('core-themes.core-backpage')

@section('title', 'Kelola Absensi Pertemuan Ke-' . $pertemuan)

@section('custom-css')
<style>
    /* ---------- Warna status absensi ---------- */
    .status-belum { background-color: #6c757d; color: white; }
    .status-hadir { background-color: #198754; color: white; }
    .status-sakit { background-color: #ffc107; color: #212529; }
    .status-izin  { background-color: #0dcaf0; color: #212529; }
    .status-alpha { background-color: #dc3545; color: white; }

    /* ---------- Info Box Mata Kuliah ---------- */
    .info-box {
        background: linear-gradient(135deg, #e9f0fd, #f8f9fa);
        border-left: 6px solid #0d6efd;
        padding: 1.5rem 2rem;
        border-radius: 14px;
        margin-bottom: 2rem;
        box-shadow: 0 3px 12px rgb(13 110 253 / 0.2);
        font-weight: 600;
        font-size: 1.1rem;
        user-select: none;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem 3rem;
        align-items: center;
    }
    .info-box > div {
        white-space: nowrap;
    }
    .info-box strong {
        color: #212529;
        min-width: 110px;
        display: inline-block;
    }
    .info-box span.badge {
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* ---------- Status Summary Card ---------- */
    .status-summary-card {
        background: linear-gradient(135deg, #0d6efd, #3a8dff);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 8px 24px rgb(13 110 253 / 0.25);
        display: flex;
        justify-content: space-between;
        align-items: center;
        user-select: none;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .status-summary-card h5 {
        margin: 0 0 0.25rem;
        font-weight: 700;
        font-size: 1.4rem;
        letter-spacing: 0.03em;
    }
    .status-summary-card p {
        margin: 0;
        font-weight: 400;
        opacity: 0.85;
        font-size: 1rem;
    }
    .status-summary-card i {
        opacity: 0.6;
    }

    /* ---------- Tabel Absensi ---------- */
    .table-responsive {
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgb(0 0 0 / 0.05);
    }
    table.table {
        border-collapse: separate;
        border-spacing: 0;
    }
    thead th {
        position: sticky;
        top: 0;
        z-index: 1020;
        background-color: #f8f9fa;
        box-shadow: inset 0 -2px 5px rgb(0 0 0 / 0.1);
        font-weight: 600;
        font-size: 0.95rem;
        vertical-align: middle;
        text-align: center;
        padding: 1rem 0.75rem;
        user-select: none;
    }
    tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        font-size: 0.95rem;
    }
    tbody tr:hover {
        background-color: #e3f2fd;
        cursor: pointer;
        transition: background-color 0.25s ease;
    }
    tbody td.text-start {
        text-align: left;
        font-weight: 600;
    }

    /* ---------- Select Status Absensi ---------- */
    select.form-select-sm {
        padding: 0.3rem 0.5rem;
        font-weight: 600;
        border-radius: 0.3rem;
        transition: background-color 0.3s ease, color 0.3s ease;
        user-select: none;
        min-width: 120px;
    }
    select.form-select-sm:focus {
        box-shadow: 0 0 0 0.3rem rgba(13, 110, 253, 0.35);
        border-color: #0d6efd;
        outline: none;
    }
    select.form-select-sm.status-belum { background-color: #6c757d; color: white; }
    select.form-select-sm.status-hadir { background-color: #198754; color: white; }
    select.form-select-sm.status-sakit { background-color: #ffc107; color: #212529; }
    select.form-select-sm.status-izin  { background-color: #0dcaf0; color: #212529; }
    select.form-select-sm.status-alpha { background-color: #dc3545; color: white; }

    /* ---------- Tombol Simpan dan Kembali ---------- */
    .action-buttons {
        margin-top: 2.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
    }
    .btn-primary, .btn-outline-secondary {
        min-width: 140px;
        font-weight: 700;
        box-shadow: 0 6px 16px rgb(13 110 253 / 0.28);
        border-radius: 40px;
        padding: 0.6rem 1.8rem;
        transition: all 0.35s ease;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        justify-content: center;
        font-size: 1rem;
    }
    .btn-primary:hover {
        box-shadow: 0 8px 26px rgb(13 110 253 / 0.48);
        transform: translateY(-4px);
    }
    .btn-outline-secondary:hover {
        box-shadow: 0 8px 26px rgb(108 117 125 / 0.42);
        transform: translateY(-4px);
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-4">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-header bg-white d-flex align-items-center gap-3 border-bottom rounded-top-4">
            <i class="fas fa-user-check fs-4 text-primary"></i>
            <h4 class="mb-0 fw-bold text-dark">Kelola Absensi Mahasiswa</h4>
        </div>

        <div class="card-body px-4 py-4">

            <!-- Info Mata Kuliah -->
            <div class="info-box" aria-label="Informasi Mata Kuliah dan Pertemuan">
                <div>
                    <strong>Mata Kuliah:</strong>
                    <span class="text-primary">{{ $jadwal->mataKuliah->name ?? '-' }}</span>
                </div>
                <div>
                    <strong>Kelas:</strong>
                    @if($jadwal->kelas->isNotEmpty())
                        <span class="text-success">{{ $jadwal->kelas->pluck('name')->join(', ') }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
                <div>
                    <strong>Pertemuan ke:</strong>
                    <span class="-">{{ $pertemuan }}</span>
                </div>
            </div>

            <!-- Status Ringkasan -->
            <section class="status-summary-card" aria-label="Ringkasan Status Absensi">
                <div>
                    <h5>Status Absensi:</h5>
                    <p>Periksa dan kelola kehadiran mahasiswa secara langsung.</p>
                </div>
                <i class="fas fa-clipboard-list fa-2x" aria-hidden="true"></i>
            </section>

            <!-- Form Absensi -->
            <form method="POST" action="{{ route('master.akademik.absensi.store-pertemuan', [$jadwal->id, $pertemuan]) }}" aria-label="Form Pengelolaan Absensi Mahasiswa">
                @csrf
                <div class="table-responsive rounded-4 border shadow-sm">
                    <table class="table table-bordered table-hover align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">No</th>
                                <th class="text-start">Nama Mahasiswa</th>
                                <th style="width: 120px;">NIM</th>
                                <th style="width: 160px;">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mahasiswas as $index => $mhs)
                                @php
                                    $absenStatus = $absensi_mahasiswa->has($mhs->id) ? $absensi_mahasiswa[$mhs->id]->absen_type : 'Belum Absen';
                                    $absenStatusLower = strtolower($absenStatus);
                                    $statusClass = match($absenStatusLower) {
                                        'hadir' => 'status-hadir',
                                        'sakit' => 'status-sakit',
                                        'izin' => 'status-izin',
                                        'alpha' => 'status-alpha',
                                        'belum absen' => 'status-belum',
                                        default => ''
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start fw-semibold">{{ $mhs->name }}</td>
                                    <td>{{ $mhs->numb_nim }}</td>
                                    <td>
                                        <select name="absensi[{{ $mhs->id }}]"
                                            class="form-select form-select-sm {{ $statusClass }}"
                                            data-bs-toggle="tooltip"
                                            title="Pilih status absensi mahasiswa">
                                            <option value="Belum Absen" {{ $absenStatus == 'Belum Absen' ? 'selected' : '' }}>Belum Absen</option>
                                            <option value="Hadir" {{ $absenStatus == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="Sakit" {{ $absenStatus == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="Izin" {{ $absenStatus == 'Izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="Alpha" {{ $absenStatus == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                                        </select>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted fst-italic">Tidak ada mahasiswa terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary shadow-sm" aria-label="Simpan Absensi">
                        <i class="fas fa-save"></i> Simpan Absensi
                    </button>
                    <a href="{{ route('master.akademik.absensi.jadwal', $jadwal->id) }}" 
                       class="btn btn-outline-secondary shadow-sm" aria-label="Kembali ke Jadwal Absensi">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selects = document.querySelectorAll('select[name^="absensi"]');
        selects.forEach(select => {
            updateSelectClass(select);
            select.addEventListener('change', () => updateSelectClass(select));
        });

        function updateSelectClass(select) {
            select.className = 'form-select form-select-sm';
            switch (select.value.toLowerCase()) {
                case 'hadir': select.classList.add('status-hadir'); break;
                case 'sakit': select.classList.add('status-sakit'); break;
                case 'izin': select.classList.add('status-izin'); break;
                case 'alpha': select.classList.add('status-alpha'); break;
                case 'belum absen': select.classList.add('status-belum'); break;
            }
        }

        // Bootstrap tooltip init
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    });
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        timer: 2500,
        showConfirmButton: false,
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '{{ session("error") }}',
        timer: 3000,
        showConfirmButton: false,
    });
</script>
@endif
@endsection
