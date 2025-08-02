@extends('core-themes.core-backpage')

@section('custom-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        /* --- General styles from your base + enhancements --- */

        /* Card header with gradient */
        .card-header-gradient {
            background: linear-gradient(90deg, #0062E6 0%, #33AEFF 100%);
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
            padding: 1rem 1.5rem;
            border-radius: .75rem .75rem 0 0;
            box-shadow: 0 4px 15px rgb(51 174 255 / 0.4);
            user-select: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Table styling + hover */
        .table th,
        .table td {
            vertical-align: middle;
            transition: background-color 0.3s ease;
        }

        tbody tr:hover {
            background-color: #d0ebff;
            cursor: pointer;
            transform: translateX(5px);
            transition: all 0.2s ease;
            box-shadow: 2px 3px 8px rgb(0 123 255 / 0.15);
        }

        /* Button hover enhancements */
        .btn-outline-primary:hover,
        .btn-outline-secondary:hover,
        .btn-outline-dark:hover,
        .btn-outline-success:hover,
        .btn-outline-danger:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            transform: translateY(-3px);
            transition: all 0.3s ease;
        }

        .btn-back:hover i {
            transform: translateX(-7px);
        }

        /* Status badges */
        .badge-status {
            font-size: 1rem;
            padding: 0.5em 1.1rem;
            border-radius: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.6em;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            user-select: none;
            box-shadow: 0 3px 10px rgb(0 0 0 / 0.2);
            transition: transform 0.15s ease;
        }

        .badge-status:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 18px rgb(0 0 0 / 0.3);
        }

        .badge-status.bg-success {
            background-color: #198754 !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgb(25 135 84 / 0.8);
        }

        .badge-status.bg-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
            box-shadow: 0 4px 12px rgb(255 193 7 / 0.8);
        }

        .badge-status.bg-info {
            background-color: #0dcaf0 !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgb(13 202 240 / 0.8);
        }

        .badge-status.bg-secondary {
            background-color: #6c757d !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgb(108 117 125 / 0.8);
        }

        /* Progress bar */
        .progress {
            height: 22px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgb(0 0 0 / 0.1);
        }

        .progress-bar {
            font-weight: 700;
            line-height: 22px;
            letter-spacing: 0.04em;
            text-shadow: 0 1px 1px rgb(0 0 0 / 0.2);
        }

        /* Responsive tweaks */
        @media (max-width: 576px) {

            .table thead tr th,
            .table tbody tr td {
                font-size: 0.85rem;
                padding: 0.4rem 0.5rem;
            }

            .btn-sm {
                font-size: 0.75rem;
                padding: 0.35rem 0.8rem;
            }

            .card-header-gradient {
                font-size: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
    <div class="mb-3">
        <a href="{{ route('akademik.jadwal-kuliah-render') }}"
            class="btn btn-outline-primary rounded-pill btn-back fw-semibold d-inline-flex align-items-center shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>
            Kembali ke Jadwal Perkuliahan
        </a>
    </div>



    <div class="card shadow-sm mb-4 border-0 rounded-4">

        <div class="card-header-gradient">
            <i class="fas fa-book-open"></i> Mata Kuliah: <span class="ms-2">{{ $jadwal->mataKuliah->name }}</span>
        </div>
        <div class="card-body text-secondary fw-semibold">
            <div class="row gy-2">
                <div class="col-md-4">
                    <i class="fas fa-chalkboard-teacher text-primary me-2"></i>Dosen Pengampu:
                    <span class="text-dark">{{ $jadwal->dosen->name ?? '-' }}</span>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-users text-primary me-2"></i>Kelas:
                    <span class="text-dark">{{ $jadwal->kelas->pluck('name')->join(', ') }}</span>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-layer-group text-primary me-2"></i>Jenis Kelas:
                    <span class="text-dark">{{ $jadwal->jenisKelas->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        @php
            // Hitung statistik ringkas
            $totalPertemuan = 16;
            $filledCount = $absensi_status->where('status', 'sudah diisi')->count();
            $notFilledCount = $totalPertemuan - $filledCount;
            $openCount = $absensi_status->where('is_active', true)->count();
            $filledPercent = round(($filledCount / $totalPertemuan) * 100);
            $openPercent = round(($openCount / $totalPertemuan) * 100);
        @endphp

        <div class="mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-4 col-12">
                    <div
                        class="p-3 shadow-sm rounded-4 border border-success d-flex align-items-center gap-3 bg-success bg-opacity-10">
                        <i class="fas fa-check-circle fa-2x text-success badge-pulse"></i>
                        <div>
                            <div class="fw-bold text-success" style="font-size: 1.15rem;">{{ $filledCount }} /
                                {{ $totalPertemuan }} Pertemuan</div>
                            <small class="text-success">Sudah Diisi</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div
                        class="p-3 shadow-sm rounded-4 border border-warning d-flex align-items-center gap-3 bg-warning bg-opacity-10">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning badge-pulse"></i>
                        <div>
                            <div class="fw-bold text-warning" style="font-size: 1.15rem;">{{ $notFilledCount }} /
                                {{ $totalPertemuan }} Pertemuan</div>
                            <small class="text-warning">Belum Diisi</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div
                        class="p-3 shadow-sm rounded-4 border border-info d-flex align-items-center gap-3 bg-info bg-opacity-10">
                        <i class="fas fa-unlock-alt fa-2x text-info badge-pulse"></i>
                        <div>
                            <div class="fw-bold text-info" style="font-size: 1.15rem;">{{ $openCount }} /
                                {{ $totalPertemuan }} Pertemuan</div>
                            <small class="text-info">Sedang Dibuka</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div class="d-flex flex-wrap gap-2 mt-3">

            {{-- Export Absensi --}}
            <a href="{{ route('master.akademik.absensi.export', $jadwal->id) }}"
                class="btn btn-outline-success rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2"
                data-bs-toggle="tooltip" data-bs-placement="top" title="Unduh absensi mahasiswa dalam format Excel (.xls)">
                <i class="fas fa-file-excel"></i>
                <span>Export Absensi</span>
            </a>

            {{-- Import Absensi --}}
            <a href="{{ route('master.akademik.absensi.import', $jadwal->id) }}"
                class="btn btn-outline-primary rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2"
                data-bs-toggle="tooltip" data-bs-placement="top" title="Unggah file absensi dari Excel (.xls)">
                <i class="fas fa-file-upload"></i>
                <span>Import Absensi</span>
            </a>

            {{-- Cetak PDF Kosong --}}
            <a href="{{ route('dosen.absensi.cetak-kosong', $jadwal->id) }}" target="_blank"
                class="btn btn-outline-danger rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2"
                data-bs-toggle="tooltip" data-bs-placement="top" title="Cetak daftar hadir kosong (PDF)">
                <i class="bi bi-printer"></i>
                <span>Cetak PDF Kosong</span>
            </a>

        </div>
        {{-- <div class="mb-4">
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-3 text-primary">
                <i class="fas fa-clipboard-list fs-3"></i> Kelola Absensi Kuliah
            </h4>
            <p class="text-muted fs-6 mb-0">
                Kelola status absensi, isi daftar hadir, dan cetak laporan pertemuan kuliah. Total <strong>16
                    pertemuan</strong>
                terjadwal.
            </p>
        </div> --}}

        <div class="text-muted fst-italic small d-flex align-items-center gap-2">
            <i class="fas fa-info-circle"></i>
            Klik tombol <strong>"Kelola Absensi"</strong> untuk mengisi daftar hadir mahasiswa tiap pertemuan.
        </div>
    </div>

    <div class="table-responsive shadow-sm rounded-4 border">
        <table class="table table-bordered table-hover align-middle mb-0 text-center">
            <thead class="table-light fs-6">
                <tr>
                    <th style="width: 140px;">Pertemuan</th>
                    <th style="min-width: 180px;">Status Absensi</th>
                    <th style="min-width: 180px;">Status Pembukaan Absensi</th>
                    <th style="width: 220px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 16; $i++)
                    @php
                        $status = $absensi_status->firstWhere('pertemuan', $i);
                        $isFilled = $status && strtolower($status->status) === 'sudah diisi';
                        $isOpen = $status && $status->is_active;
                    @endphp
                    <tr>
                        <td class="fw-bold text-primary fs-5">
                            Pertemuan {{ $i }}
                        </td>
                        <td>
                            @if ($isFilled)
                                <span class="badge bg-success badge-status badge-pulse" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Absensi pertemuan {{ $i }} sudah lengkap diisi oleh dosen pengampu">
                                    <i class="fas fa-check-circle fs-4"></i> Sudah Diisi
                                </span>
                            @else
                                <span class="badge bg-warning badge-status badge-pulse" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Absensi pertemuan {{ $i }} belum diisi, harap segera diisi untuk data valid">
                                    <i class="fas fa-exclamation-triangle fs-4"></i> Belum Diisi
                                </span>
                            @endif
                        </td>
                        <td>
                            @if ($isOpen)
                                <span class="badge bg-info badge-status badge-pulse" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Absensi pertemuan {{ $i }} sedang dibuka dan dapat diisi atau diubah">
                                    <i class="fas fa-unlock-alt fs-4"></i> Dibuka
                                </span>
                            @else
                                <span class="badge bg-secondary badge-status" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Absensi pertemuan {{ $i }} saat ini tertutup dan tidak dapat diubah">
                                    <i class="fas fa-lock fs-4"></i> Tertutup
                                </span>
                            @endif
                        </td>
                        <td class="d-flex justify-content-center flex-wrap gap-3">
                            <a href="{{ route('master.akademik.absensi.show-pertemuan', [$jadwal->id, $i]) }}"
                                class="btn btn-sm btn-outline-primary rounded-pill px-4 py-2 fw-semibold shadow-sm"
                                data-bs-toggle="tooltip" title="Kelola Absensi Pertemuan {{ $i }}">
                                <i class="fas fa-edit me-2"></i> Kelola Absensi
                            </a>

                            @if ($isOpen)
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger rounded-pill px-4 py-2 fw-semibold shadow-sm btn-lock"
                                    data-action="{{ route('master.akademik.absensi.lock', [$jadwal->id, $i]) }}"
                                    data-pertemuan="{{ $i }}"
                                    title="Tutup Absensi Pertemuan {{ $i }}">
                                    <i class="fas fa-lock me-2"></i> Tutup
                                </button>
                            @else
                                <button type="button"
                                    class="btn btn-sm btn-outline-success rounded-pill px-4 py-2 fw-semibold shadow-sm btn-open"
                                    data-action="{{ route('master.akademik.absensi.open', [$jadwal->id, $i]) }}"
                                    data-pertemuan="{{ $i }}"
                                    title="Buka Absensi Pertemuan {{ $i }}">
                                    <i class="fas fa-unlock me-2"></i> Buka
                                </button>
                            @endif
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
@endsection

@section('custom-js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // SweetAlert2 for lock buttons
            document.querySelectorAll('.btn-lock').forEach(function(button) {
                button.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    const pertemuan = this.getAttribute('data-pertemuan');

                    Swal.fire({
                        title: `<i class="fas fa-lock me-2"></i> Konfirmasi Tutup Absensi`,
                        html: `Apakah Anda yakin ingin <strong>menutup</strong> absensi pada pertemuan <strong>${pertemuan}</strong>?<br><small class="text-muted">Setelah ditutup, absensi tidak dapat diubah kecuali dibuka kembali oleh superadmin.</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Tutup Absensi',
                        cancelButtonText: 'Batal',
                        customClass: {
                            confirmButton: 'btn btn-danger rounded-pill px-4 py-2 fw-semibold',
                            cancelButton: 'btn btn-outline-secondary rounded-pill px-4 py-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = action;
                            const csrfToken = '{{ csrf_token() }}';
                            const inputCsrf = document.createElement('input');
                            inputCsrf.type = 'hidden';
                            inputCsrf.name = '_token';
                            inputCsrf.value = csrfToken;
                            form.appendChild(inputCsrf);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

            // SweetAlert2 for open buttons
            document.querySelectorAll('.btn-open').forEach(function(button) {
                button.addEventListener('click', function() {
                    const action = this.getAttribute('data-action');
                    const pertemuan = this.getAttribute('data-pertemuan');

                    Swal.fire({
                        title: `<i class="fas fa-unlock me-2"></i> Konfirmasi Buka Absensi`,
                        html: `Apakah Anda yakin ingin <strong>membuka</strong> absensi pada pertemuan <strong>${pertemuan}</strong>?<br><small class="text-muted">Setelah dibuka, Anda dapat mengisi daftar hadir mahasiswa.</small>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Buka Absensi',
                        cancelButtonText: 'Batal',
                        customClass: {
                            confirmButton: 'btn btn-success rounded-pill px-4 py-2 fw-semibold',
                            cancelButton: 'btn btn-outline-secondary rounded-pill px-4 py-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = action;
                            const csrfToken = '{{ csrf_token() }}';
                            const inputCsrf = document.createElement('input');
                            inputCsrf.type = 'hidden';
                            inputCsrf.name = '_token';
                            inputCsrf.value = csrfToken;
                            form.appendChild(inputCsrf);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

        });
    </script>
@endsection
