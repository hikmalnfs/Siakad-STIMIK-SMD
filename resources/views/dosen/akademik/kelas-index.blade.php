@extends('base.base-dash-index')

@section('menu')
    Akademik
@endsection

@section('submenu')
    Kelas Mengajar
@endsection

@section('urlmenu')
    {{ route('dosen.akademik.kelas-index') }}
@endsection

@section('subdesc')
    Halaman manajemen kelas yang Anda ampu
@endsection

@section('title')
    @yield('submenu') - @yield('menu') - Siakad By Internal Developer
@endsection

@section('content')
<section class="section">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card dashboard-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl me-3">
                                <i class="bi bi-calendar-week-fill"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Jadwal Mengajar</h5>
                                <small class="text-muted">Overview kelas yang Anda ampu</small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-sliders me-1"></i> Filter
                            </button>
                            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 320px;">
                                <form method="GET" action="{{ route('dosen.akademik.kelas-index') }}">
                                    <div class="mb-3">
                                        <label for="tahun_akademik_id" class="form-label">Tahun Akademik</label>
                                        <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select select2">
                                            <option value="">Semua Tahun Akademik</option>
                                            @foreach ($tahunAkademikList as $ta)
                                                <option value="{{ $ta->id }}" {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                                                    {{ $ta->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="semester" class="form-label">Semester</label>
                                        <select name="semester" id="semester" class="form-select">
                                            <option value="">Semua Semester</option>
                                            <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                            <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-funnel-fill me-1"></i> Terapkan Filter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Tabs untuk setiap hari -->
                    <ul class="nav nav-tabs" id="scheduleTabs" role="tablist">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ \Carbon\Carbon::now()->isoFormat('dddd') === $hari ? 'active' : '' }}" 
                                    id="{{ strtolower($hari) }}-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#{{ strtolower($hari) }}" 
                                    type="button" 
                                    role="tab" 
                                    aria-controls="{{ strtolower($hari) }}" 
                                    aria-selected="{{ \Carbon\Carbon::now()->isoFormat('dddd') === $hari ? 'true' : 'false' }}">
                                {{ $hari }}
                                @if(\Carbon\Carbon::now()->isoFormat('dddd') === $hari)
                                    <span class="badge bg-primary ms-2">Hari Ini</span>
                                @endif
                            </button>
                        </li>
                        @endforeach
                    </ul>

                    <div class="tab-content pt-4" id="scheduleTabsContent">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                        <div class="tab-pane fade {{ \Carbon\Carbon::now()->isoFormat('dddd') === $hari ? 'show active' : '' }}" 
                             id="{{ strtolower($hari) }}" 
                             role="tabpanel" 
                             aria-labelledby="{{ strtolower($hari) }}-tab">
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="15%">Waktu</th>
                                            <th>Mata Kuliah</th>
                                            <th width="15%">Kelas</th>
                                            <th width="15%">Ruang</th>
                                            <th width="10%">SKS</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $jadwalHariIni = $jadwals->where('hari', $hari)->sortBy('jam_mulai');
                                            $isEmpty = $jadwalHariIni->isEmpty();
                                        @endphp

                                        @forelse($jadwalHariIni as $jadwal)
                                        @php
                                            $currentTime = now()->format('H:i');
                                            $isCurrent = \Carbon\Carbon::now()->isoFormat('dddd') === $hari && 
                                                         $currentTime >= \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') && 
                                                         $currentTime <= \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i');
                                        @endphp
                                        <tr class="{{ $isCurrent ? 'table-success' : '' }}">
                                            <td>
                                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                @if($isCurrent)
                                                    <span class="badge bg-success ms-2">Berlangsung</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $jadwal->mataKuliah->name ?? '-' }}</strong>
                                                <div class="text-muted small">
                                                    Semester {{ $jadwal->mataKuliah->semester ?? '-' }}
                                                </div>
                                            </td>
                                            <td>{{ $jadwal->kelas->pluck('name')->join(', ') }}</td>
                                            <td>{{ $jadwal->ruang->name ?? '-' }}</td>
                                            <td>{{ $jadwal->mataKuliah->sks ?? '0' }} SKS</td>
                                            <td>
                                                <a href="{{ route('dosen.akademik.kelas-view-absensi', $jadwal->id) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-clipboard-check me-1"></i> Absensi
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <div class="empty-state">
                                                    <i class="bi bi-calendar-x fs-1"></i>
                                                    <h5 class="mt-3">Tidak ada jadwal tersedia</h5>
                                                    <p class="text-muted">Anda tidak memiliki jadwal mengajar pada hari {{ $hari }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .dashboard-card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem;
    }
    
    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background-color: #f0f7ff;
        color: #3a86ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 12px 20px;
        position: relative;
    }
    
    .nav-tabs .nav-link.active {
        color: #3a86ff;
        background-color: transparent;
        border-bottom: 3px solid #3a86ff;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #3a86ff;
    }
    
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #dee2e6;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(58, 134, 255, 0.05);
    }
    
    .table-success {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2').select2({
                placeholder: "Pilih Tahun Akademik",
                allowClear: true
            });
        }
    });
</script>
@endsection