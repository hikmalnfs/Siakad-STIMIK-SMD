@extends('base.base-dash-index')

@section('title', 'Kartu Rencana Studi (KRS)')

@section('content')
<div class="container">

    {{-- Header dan Tombol Aksi --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0">
            <i class="fas fa-file-alt me-2"></i>Kartu Rencana Studi (KRS)
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('mahasiswa.krs.create') }}" class="btn btn-primary" data-bs-toggle="tooltip"
                title="Tambah Mata Kuliah">
                <i class="fas fa-plus me-1"></i>Tambah
            </a>
            <a href="{{ route('mahasiswa.krs.show', 1) }}" target="_blank" class="btn btn-outline-secondary"
                data-bs-toggle="tooltip" title="Cetak KRS">
                <i class="fas fa-print me-1"></i>Cetak
            </a>
        </div>
    </div>

    {{-- Filter Riwayat KRS --}}
<form method="GET" class="row g-3 mb-4">
    <div class="col-md-6 col-lg-4">
        <label for="filter_tahun" class="form-label">Pilih Tahun Akademik</label>
        <select name="tahun_akademik" id="filter_tahun" class="form-select" onchange="this.form.submit()">
            <option value="">-- Semua Tahun Akademik --</option>
            @foreach ($tahunAkademikList as $tahun)
                <option value="{{ $tahun->id }}" {{ $filterTaId == $tahun->id ? 'selected' : '' }}>
                    {{ $tahun->name }}
                </option>
            @endforeach
        </select>
    </div>
</form>

    {{-- Informasi Akademik --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <div class="row text-secondary">
                <div class="col-md-4 mb-2">
                    <strong>Tahun Akademik:</strong><br> {{ $ta->name ?? '-' }}
                </div>
                    <div class="col-md-4 mb-2">
                        <strong>Dosen Wali:</strong><br>{{ $mahasiswa->kelas->wali->name ?? '-' }}
                    </div>
                <div class="col-md-4 mb-2">
                    <strong>Program Studi:</strong><br> {{ $fakultas->nama ?? 'Manajemen Komputer' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Total SKS --}}
    <div class="mb-3">
        <h5>Total SKS Diambil:
            <span id="sks-total" class="badge bg-primary fs-5">0</span> /
            <span class="text-muted" id="max-sks-label">{{ $maxSks ?? 24 }} SKS</span>
        </h5>
        <div id="sks-warning" class="alert alert-danger mt-2 d-none" role="alert">
            ⚠ Total SKS melebihi batas maksimum!
        </div>
    </div>

    {{-- Tabel KRS --}}
    @if ($items->isEmpty())
        <div class="alert alert-info d-flex align-items-center">
            <i class="fa fa-info-circle me-2"></i> Belum ada mata kuliah dalam KRS semester ini.
        </div>
    @else
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0 text-center" id="krs-table">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th class="text-start">Mata Kuliah</th>
                                <th>Dosen</th>
                                <th>Hari / Waktu</th>
                                <th>Ruang</th>
                                <th>SKS</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                @php
                                    $jadwal = $item->jadwal;
                                    $matkul = $jadwal->mataKuliah ?? null;
                                    $dosenName = $jadwal->dosen->name ?? '-';
                                    $hari = $jadwal->hari ?? '-';
                                    $jamMulai = \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i');
                                    $jamSelesai = \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i');
                                    $sks = $matkul->sks ?? ($matkul->bsks ?? 0);
                                @endphp
                                <tr id="row-{{ $item->id }}">
                                    <td>{{ $matkul->code ?? '-' }}</td>
                                    <td class="text-start">{{ $matkul->name ?? '-' }}</td>
                                    <td>{{ $dosenName }}</td>
                                    <td>{{ $hari }}<br><small class="text-muted">{{ $jamMulai }} - {{ $jamSelesai }}</small></td>
                                    <td>{{ $jadwal->ruang->name ?? '-' }}</td>
                                    <td class="sks-value" data-sks="{{ $sks }}">{{ $sks }}</td>
                                    <td>
                                        @switch($item->status)
                                            @case('Menunggu')
                                                <span class="badge bg-warning text-dark">Menunggu</span>
                                                @break
                                            @case('Disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                                @break
                                            @case('Ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $item->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('mahasiswa.krs.destroy', $item->id) }}" method="POST" class="d-inline" 
                                            onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini? Data tidak bisa dikembalikan!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        @endif
    });
</script>
@endpush
