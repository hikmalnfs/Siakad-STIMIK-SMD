@extends('base.base-dash-index')

@section('title', 'Detail Mahasiswa Bimbingan')
@section('menu', 'Mahasiswa Bimbingan')
@section('submenu', 'Detail Mahasiswa')
@section('subdesc', 'Detail lengkap mahasiswa bimbingan akademik Anda')

@section('content')
<section class="content py-4">
    <div class="container-fluid">
        <!-- Student Profile Section -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar avatar-xl bg-primary bg-opacity-10 text-primary rounded-3 me-4">
                                <i class="ti ti-user fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-1">{{ $mahasiswa->name }}</h3>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                                        <i class="ti ti-id me-1"></i> {{ $mahasiswa->numb_nim }}
                                    </span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">
                                        <i class="ti ti-school me-1"></i> {{ $mahasiswa->kelas->name ?? 'Belum ditentukan' }}
                                    </span>
                                    @php
                                        $statusColor = match($mahasiswa->status) {
                                            'Aktif' => 'success',
                                            'Cuti' => 'warning',
                                            'DO', 'Drop Out' => 'danger',
                                            'Lulus' => 'primary',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} rounded-pill px-3 py-1">
                                        {{ $mahasiswa->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <h6 class="text-muted mb-3">Status Akademik</h6>
                                    <form method="POST" action="{{ route('dosen.mahasiswa-bimbingan.update-status-akademik', $mahasiswa->id) }}">
                                        @csrf @method('PATCH')
                                        <div class="row g-2">
                                            <div class="col-md-7">
                                                <select name="status_akademik" class="form-select border-0 bg-white shadow-sm" required>
                                                    @foreach(['Aktif', 'Cuti', 'DO', 'Drop Out', 'Lulus'] as $status)
                                                        <option value="{{ $status }}" {{ $mahasiswa->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-0">Semester</span>
                                                    <input type="number" name="semester_baru" value="{{ old('semester_baru', $mahasiswa->semester_terakhir ?? 1) }}" 
                                                        class="form-control border-0 bg-white shadow-sm" required min="1" max="14">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <input type="text" name="catatan" value="{{ old('catatan', $mahasiswa->catatan_status) }}" 
                                                    class="form-control border-0 bg-white shadow-sm mt-2" placeholder="Catatan...">
                                            </div>
                                            <div class="col-12 mt-2">
                                                <button class="btn btn-primary w-100 rounded-2">
                                                    <i class="ti ti-check me-1"></i> Update Status
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <h6 class="text-muted mb-3">Ringkasan Akademik</h6>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">IPK</span>
                                        <span class="fw-bold">{{ number_format($ipk, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Total SKS</span>
                                        <span class="fw-bold">{{ $sksTotal }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">KRS Aktif</span>
                                        <span class="badge {{ $krsAktif->count() ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill px-3 py-1">
                                            {{ $krsAktif->count() ? $krsAktif->count().' matkul' : 'Belum ada' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Progress Kelulusan</h5>
                        <div class="text-center mb-3">
                            <div class="position-relative d-inline-block" style="width: 160px; height: 160px;">
                                <svg class="circular-progress" viewBox="0 0 36 36" style="width: 160px; height: 160px;">
                                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                    <path class="circle-progress" stroke-dasharray="{{ min(100, ($sksLulus / 144) * 100) }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <div class="fs-2 fw-bold">{{ round(min(100, ($sksLulus / 144) * 100)) }}%</div>
                                    <div class="text-muted small">{{ $sksLulus }}/144 SKS</div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('dosen.mahasiswa-bimbingan.krs.create', $mahasiswa->id) }}" class="btn btn-primary rounded-2">
                                <i class="ti ti-plus me-1"></i> Buat KRS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning Alerts -->
        @if (($ipk ?? 0) < 2.00 || isset($dropOut) || isset($studiLama))
        <div class="row mb-4">
            @if (($ipk ?? 0) < 2.00)
            <div class="col-md-4">
                <div class="alert alert-danger d-flex align-items-center p-3 rounded-3">
                    <i class="ti ti-alert-triangle me-3 fs-4"></i>
                    <div>
                        <h6 class="alert-heading mb-1">IPK Rendah</h6>
                        <p class="mb-0 small">IPK di bawah standar minimum (2.00)</p>
                    </div>
                </div>
            </div>
            @endif
            @if (isset($dropOut) && $dropOut)
            <div class="col-md-4">
                <div class="alert alert-warning d-flex align-items-center p-3 rounded-3">
                    <i class="ti ti-alert-circle me-3 fs-4"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Risiko DO</h6>
                        <p class="mb-0 small">SKS yang ditempuh sangat rendah</p>
                    </div>
                </div>
            </div>
            @endif
            @if (isset($studiLama) && $studiLama)
            <div class="col-md-4">
                <div class="alert alert-secondary d-flex align-items-center p-3 rounded-3">
                    <i class="ti ti-clock-hour-4 me-3 fs-4"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Masa Studi</h6>
                        <p class="mb-0 small">Sudah menempuh >8 semester</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Academic Performance -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Perkembangan IP Semester</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                                IPK: {{ number_format($ipk, 2) }}
                            </span>
                        </div>
                        
                        @if(count($ipList) > 0)
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="ipChart"></canvas>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-borderless">
                                <thead>
                                    <tr>
                                        <th class="small text-muted">Semester</th>
                                        <th class="small text-muted text-end">IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ipList as $ip)
                                    <tr>
                                        <td class="small">{{ $ip['semester'] }}</td>
                                        <td class="text-end fw-bold">{{ number_format($ip['ip'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="ti ti-info-circle fs-4 text-muted mb-3"></i>
                            <p class="text-muted mb-0">Data IP semester belum tersedia</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Distribusi Nilai</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                                {{ $khs->count() }} Matkul
                            </span>
                        </div>
                        
                        @if($khs->count() > 0)
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="gradeChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <div class="row g-2 text-center">
                                <div class="col">
                                    <div class="bg-success bg-opacity-10 rounded-2 p-2">
                                        <div class="fs-5 fw-bold text-success">{{ $khs->where('grade', 'A')->count() }}</div>
                                        <small class="text-muted">A</small>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="bg-primary bg-opacity-10 rounded-2 p-2">
                                        <div class="fs-5 fw-bold text-primary">{{ $khs->where('grade', 'B')->count() }}</div>
                                        <small class="text-muted">B</small>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="bg-warning bg-opacity-10 rounded-2 p-2">
                                        <div class="fs-5 fw-bold text-warning">{{ $khs->where('grade', 'C')->count() }}</div>
                                        <small class="text-muted">C</small>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="bg-danger bg-opacity-10 rounded-2 p-2">
                                        <div class="fs-5 fw-bold text-danger">{{ $khs->whereIn('grade', ['D', 'E'])->count() }}</div>
                                        <small class="text-muted">D/E</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="ti ti-info-circle fs-4 text-muted mb-3"></i>
                            <p class="text-muted mb-0">Data KHS belum tersedia</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- KRS Active -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1">KRS Aktif</h5>
                        <p class="text-muted small mb-0">Kartu Rencana Studi semester ini</p>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                        {{ $krsAktif->count() }} Mata Kuliah
                    </span>
                </div>
                
                @if($krsAktif->count() > 0)
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 5%">#</th>
                                <th style="width: 30%">Mata Kuliah</th>
                                <th class="text-center" style="width: 10%">SKS</th>
                                <th style="width: 25%">Dosen</th>
                                <th style="width: 20%">Jadwal</th>
                                <th class="text-center" style="width: 10%">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($krsAktif as $i => $krs)
                            @php
                                $jadwal = $krs->jadwal;
                                $matkul = $jadwal->mataKuliah ?? null;
                                $statusColor = match($krs->status) {
                                    'diterima' => 'success',
                                    'ditolak' => 'danger',
                                    default => 'warning'
                                };
                            @endphp
                            <tr class="border-top">
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $matkul->name ?? 'Mata kuliah tidak ditemukan' }}</div>
                                    <small class="text-muted">{{ $matkul->kode_matkul ?? '-' }}</small>
                                </td>
                                <td class="text-center">{{ $matkul->sks ?? '-' }}</td>
                                <td>
                                    <small>{{ $jadwal->dosen->name ?? 'Dosen tidak ditentukan' }}</small>
                                </td>
                                <td>
                                    <small>
                                        @if($jadwal)
                                            {{ $jadwal->hari }}, {{ $jadwal->waktu_mulai ?? '--:--' }} - {{ $jadwal->waktu_selesai ?? '--:--' }}
                                        @else
                                            Jadwal tidak tersedia
                                        @endif
                                    </small>
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('dosen.mahasiswa-bimbingan.krs.update-status', $krs->id) }}">
                                        @csrf @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" 
                                            class="form-select form-select-sm border-{{ $statusColor }} bg-{{ $statusColor }}-subtle">
                                            @foreach(['pending', 'diterima', 'ditolak'] as $s)
                                                <option value="{{ $s }}" {{ $krs->status === $s ? 'selected' : '' }}>
                                                    {{ ucfirst($s) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="ti ti-info-circle fs-4 text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada KRS aktif</h6>
                    <p class="text-muted small mb-3">Mahasiswa belum mengajukan KRS untuk semester ini</p>
                    <a href="{{ route('dosen.mahasiswa-bimbingan.krs.create', $mahasiswa->id) }}" class="btn btn-primary rounded-2">
                        <i class="ti ti-plus me-1"></i> Buat KRS
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- KHS History -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1">Riwayat KHS</h5>
                        <p class="text-muted small mb-0">Kartu Hasil Studi per semester</p>
                    </div>
                    <a href="{{ route('dosen.mahasiswa-bimbingan.khs.show', $mahasiswa->id) }}" class="btn btn-sm btn-outline-primary rounded-2">
                        <i class="ti ti-eye me-1"></i> Lihat Semua
                    </a>
                </div>
                
                @if($khs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 15%">Semester</th>
                                <th style="width: 45%">Mata Kuliah</th>
                                <th class="text-center" style="width: 20%">Nilai</th>
                                <th class="text-center" style="width: 20%">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($khs->take(5) as $k)
                            @php
                                $gradeColor = match($k->grade) {
                                    'A' => 'success',
                                    'B' => 'primary',
                                    'C' => 'warning',
                                    'D', 'E' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <tr class="border-top">
                                <td class="fw-semibold">Semester {{ $k->semester }}</td>
                                <td>
                                    <div>{{ $k->mataKuliah->name ?? 'Mata kuliah tidak ditemukan' }}</div>
                                    <small class="text-muted">{{ $k->mataKuliah->kode_matkul ?? '-' }}</small>
                                </td>
                                <td class="text-center">{{ $k->nilai ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $gradeColor }}-subtle text-{{ $gradeColor }} rounded-pill px-3 py-1">
                                        {{ $k->grade ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($khs->count() > 5)
                <div class="text-center mt-3">
                    <a href="{{ route('dosen.mahasiswa-bimbingan.khs.show', $mahasiswa->id) }}" class="btn btn-sm btn-outline-primary rounded-2">
                        Lihat lebih banyak <i class="ti ti-chevron-right ms-1"></i>
                    </a>
                </div>
                @endif
                @else
                <div class="text-center py-4">
                    <i class="ti ti-info-circle fs-4 text-muted mb-3"></i>
                    <h6 class="text-muted">Data KHS belum tersedia</h6>
                    <p class="text-muted small">Belum ada hasil studi yang tercatat</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .circular-progress {
        transform: rotate(-90deg);
    }
    .circle-bg {
        fill: none;
        stroke: #f3f4f6;
        stroke-width: 3;
    }
    .circle-progress {
        fill: none;
        stroke: #3b82f6;
        stroke-width: 3;
        stroke-linecap: round;
        animation: circle-progress 1.5s ease-in-out forwards;
    }
    @keyframes circle-progress {
        0% { stroke-dasharray: 0, 100; }
    }
    .chart-container {
        position: relative;
    }
    .table-borderless td, .table-borderless th {
        border: none;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // IP Semester Chart
        @if(count($ipList) > 0)
        const ipCtx = document.getElementById('ipChart').getContext('2d');
        new Chart(ipCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($ipList, 'semester')) !!},
                datasets: [{
                    label: 'IP Semester',
                    data: {!! json_encode(array_column($ipList, 'ip')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#3b82f6',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'IP: ' + context.raw.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 4,
                        ticks: {
                            stepSize: 0.5
                        }
                    }
                }
            }
        });
        @endif

        // Grade Distribution Chart
        @if($khs->count() > 0)
        const gradeCtx = document.getElementById('gradeChart').getContext('2d');
        new Chart(gradeCtx, {
            type: 'doughnut',
            data: {
                labels: ['A', 'B', 'C', 'D/E'],
                datasets: [{
                    data: [
                        {{ $khs->where('grade', 'A')->count() }},
                        {{ $khs->where('grade', 'B')->count() }},
                        {{ $khs->where('grade', 'C')->count() }},
                        {{ $khs->whereIn('grade', ['D', 'E'])->count() }}
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(234, 179, 8, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgba(34, 197, 94, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(234, 179, 8, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = Math.round((value / total) * 100);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
        @endif
    });
</script>
@endpush