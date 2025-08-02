@extends('base.base-dash-index')

@section('title', 'Detail Mahasiswa Bimbingan')
@section('menu', 'Mahasiswa Bimbingan')
@section('submenu', 'Detail Mahasiswa')
@section('subdesc', 'Detail lengkap mahasiswa bimbingan akademik Anda')

@section('content')
<section class="content py-4">
    <div class="container-fluid">

        {{-- PROFIL MAHASISWA --}}
        <div class="card shadow-sm rounded mb-4">
            <div class="card-header bg-light">
                <h4 class="mb-0">Profil Mahasiswa</h4>
            </div>
            <div class="card-body fs-6">
                <div><strong>Nama:</strong> {{ $mahasiswa->name }}</div>
                <div><strong>NIM:</strong> {{ $mahasiswa->numb_nim }}</div>
                <div><strong>Kelas:</strong> {{ $mahasiswa->kelas->name ?? '-' }}</div>
            </div>
        </div>

        {{-- STATUS AKADEMIK --}}
        <div class="card shadow-sm rounded mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Status Akademik & Semester</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('dosen.mahasiswa-bimbingan.update-status-akademik', $mahasiswa->id) }}" class="row g-3">
                    @csrf
                    @method('PATCH')

                    <div class="col-md-3">
                        <label class="form-label">Status Akademik</label>
                        <select name="status_akademik" class="form-select" required>
                            @foreach(['Aktif', 'Cuti', 'DO', 'Drop Out', 'Lulus'] as $status)
                                <option value="{{ $status }}" {{ $mahasiswa->status_akademik === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Semester Terakhir</label>
                        <input type="number" name="semester_baru" value="{{ old('semester_baru', $mahasiswa->semester_terakhir ?? 1) }}" class="form-control" required min="1">
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Catatan (opsional)</label>
                        <input type="text" name="catatan" value="{{ old('catatan', $mahasiswa->catatan_status) }}" class="form-control">
                    </div>

                    <div class="col-md-2 align-self-end">
                        <button class="btn btn-dark w-100">Update</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ALERT KONDISI KHUSUS --}}
        <div class="mb-4">
            @if (($ipk ?? 0) < 2.00)
                <div class="alert alert-danger shadow-sm">⚠️ IPK mahasiswa di bawah 2.00.</div>
            @endif
            @if (isset($dropOut) && $dropOut)
                <div class="alert alert-warning shadow-sm">⚠️ Mahasiswa berisiko Drop Out karena SKS rendah.</div>
            @endif
            @if (isset($studiLama) && $studiLama)
                <div class="alert alert-secondary shadow-sm">📌 Mahasiswa sudah menempuh lebih dari 8 semester.</div>
            @endif
        </div>

        {{-- STATISTIK AKADEMIK --}}
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm rounded">
                    <div class="card-header bg-white d-flex justify-content-between">
                        <h5 class="mb-0">IP Semester</h5>
                        <span class="badge bg-light border">IPK: {{ number_format($ipk, 2) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0 text-center">
                            <thead><tr><th>Semester</th><th>IP</th></tr></thead>
                            <tbody>
                                @forelse($ipList as $ip)
                                    <tr><td>{{ $ip['semester'] }}</td><td>{{ number_format($ip['ip'], 2) }}</td></tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted">Tidak ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- SKS & KRS --}}
            <div class="col-lg-6">
                <div class="card shadow-sm rounded">
                    <div class="card-header bg-white d-flex justify-content-between">
                        <h5 class="mb-0">SKS & KRS</h5>
                        <a href="{{ route('dosen.mahasiswa-bimbingan.krs.create', $mahasiswa->id) }}" class="btn btn-sm btn-outline-secondary">+ Tambah KRS</a>
                    
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0 text-center">
                            <tbody>
                                <tr><th>SKS Lulus</th><td>{{ $sksLulus }}</td></tr>
                                <tr><th>Total SKS</th><td>{{ $sksTotal }}</td></tr>
                                <tr><th>Rata SKS/Semester</th><td>{{ $avgSks }}</td></tr>
                                <tr><th>KRS Aktif</th>
                                    <td>
                                        @if($krsAktif->count())
                                            <span class="badge bg-light border text-success">Sudah</span>
                                        @else
                                            <span class="badge bg-light border text-danger">Belum</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- DAFTAR KRS --}}
        <div class="card shadow-sm rounded mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Daftar KRS Tahun Ini</h5>
            </div>
            <div class="card-body p-0">
                @if($krsAktif->count())
                    <table class="table table-hover table-bordered mb-0 text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Dosen</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($krsAktif as $i => $krs)
                                @php
                                    $jadwal = $krs->jadwal;
                                    $matkul = $jadwal->mataKuliah ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-start">{{ $matkul->name ?? '-' }}</td>
                                    <td>{{ $matkul->sks ?? '-' }}</td>
                                    <td>{{ $jadwal->dosen->name ?? '-' }}</td>
                                    <td>{{ $jadwal->hari ?? '-' }} ({{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }})</td>
                                    <td>
                                        <form method="POST" action="{{ route('dosen.mahasiswa-bimbingan.krs.update-status', $krs->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                                @foreach(['pending', 'diterima', 'ditolak'] as $s)
                                                    <option value="{{ $s }}" {{ $krs->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-secondary mb-0 text-center">Mahasiswa belum memiliki KRS aktif.</div>
                @endif
            </div>
        </div>

        {{-- KHS --}}
        <div class="card shadow-sm rounded mt-4">
            <div class="card-header bg-white d-flex justify-content-between">
                <h5 class="mb-0">KHS Mahasiswa</h5>
                <a href="{{ route('dosen.mahasiswa-bimbingan.khs.show', $mahasiswa->id) }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="card-body p-0 table-responsive">
                @if($khs->count())
                    <table class="table table-bordered mb-0 text-center">
                        <thead>
                            <tr>
                                <th>Semester</th>
                                <th>Mata Kuliah</th>
                                <th>Nilai</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($khs as $k)
                                <tr>
                                    <td>{{ $k->semester }}</td>
                                    <td>{{ $k->mataKuliah->name ?? '-' }}</td>
                                    <td>{{ $k->nilai ?? '-' }}</td>
                                    <td>{{ $k->grade ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-light text-center mb-0">Data KHS belum tersedia.</div>
                @endif
            </div>
        </div>

    </div>
</section>
@endsection
