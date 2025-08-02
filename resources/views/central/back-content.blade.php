@extends('core-themes.core-backpage')

@section('custom-css')
    <style>
        .stat-card {
            border-radius: 15px;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .recent-activity {
            max-height: 400px;
            overflow-y: auto;
        }
        .activity-item {
            padding: 1rem;
            border-left: 3px solid #4b6cb7;
            margin-bottom: 1rem;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
        }
        .chart-container {
            margin: 0 !important;
            padding: 0 !important;
            height: auto !important;
            min-height: 0 !important;
        }
        #studentDistributionChart {
            display: block;
            width: 100% !important;
            height: 425px !important;
            max-height: 400px;
        }
        .card-body .chart-container {
            margin-bottom: 0 !important;
        }
    </style>
@endsection

@section('content')
<div class="">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img src="{{ $user->photo }}" alt="Profile" class="rounded-circle me-3" style="width: 64px; height: 64px; object-fit: cover;">
                        <div>
                            <h4 class="mb-1">Welcome back, {{ $user->name }}!</h4>
                            <p class="text-muted mb-0">Here's what's happening with your account today.</p>
                        </div>
                    </div>
                    <span class="badge bg-white">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistic Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Mahasiswa</h6>
                            <h2 class="mb-0">{{ $totalMahasiswa }}</h2>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-25">
                            <i class="bi bi-people-fill fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Mata Kuliah</h6>
                            <h2 class="mb-0">{{ $totalMatkul }}</h2>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-25">
                            <i class="bi bi-journal-bookmark-fill fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Dosen</h6>
                            <h2 class="mb-0">{{ $totalDosen }}</h2>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-25">
                            <i class="bi bi-person-badge-fill fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-1">Total Jadwal Aktif</h6>
                            <h2 class="mb-0">{{ $totalJadwal }}</h2>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-25">
                            <i class="bi bi-calendar-event-fill fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Activities -->
    {{-- <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>Distribusi Mahasiswa per Prodi</h5></div>
                <div class="card-body chart-container">
                    <canvas id="studentDistributionChart"></canvas>
                </div>
            </div>
        </div> --}}

        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5>Aktivitas Terbaru</h5></div>
                <div class="card-body recent-activity">
                    @forelse($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">{{ $activity->actionDescription ?? class_basename($activity->model_type) }}</h6>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0">{{ $activity->description }}</p>
                        </div>
                    @empty
                        <p class="text-muted">Tidak ada aktivitas terbaru.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', async function () {
        const response = await Route::get('/admin/dashboard/chart-data', [DashboardController::class, 'getChartData']).name('chart.student.data');
        const chartData = await response.json();

        const labels = chartData.map(item => item.prodi);
        const values = chartData.map(item => item.total);

        const ctx = document.getElementById('studentDistributionChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Mahasiswa',
                    data: values,
                    backgroundColor: 'rgba(75, 108, 183, 0.7)',
                    borderColor: 'rgba(75, 108, 183, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endsection
