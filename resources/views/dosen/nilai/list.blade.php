@extends('base.base-dash-index')

@section('title', 'Daftar Nilai')
@section('menu', 'Nilai')
@section('submenu', 'Daftar Nilai')
@section('urlmenu', route('dosen.nilai.list'))

@section('content')
<div class="container mt-4">
    <h3>Daftar Jadwal Kuliah</h3>

    {{-- Filter Semester --}}
    <form method="GET" action="{{ route('dosen.nilai.list') }}" class="mb-4 row">
        <div class="col-md-4">
            <label for="semester" class="form-label">Filter Semester</label>
            <select name="semester" id="semester" class="form-control" onchange="this.form.submit()">
                <option value="">-- Semua Semester --</option>
                <option value="ganjil" {{ request('semester') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                <option value="genap" {{ request('semester') == 'genap' ? 'selected' : '' }}>Genap</option>
            </select>
        </div>
    </form>

    <div class="row">
        @forelse($jadwals as $jadwal)
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>
                    <p><strong>Mata Kuliah :</strong> 
                        {{ $jadwal->mataKuliah->name ?? 'Matkul Tidak Ditemukan' }}
                    </h5>

                    <p><strong>Kelas :</strong> 
                        {{ $jadwal->kelas->isNotEmpty() ? $jadwal->kelas->pluck('name')->join(', ') : '-' }}
                    </p>

                    <p><strong>Dosen :</strong> 
                        {{ $jadwal->dosen->name ?? 'Tidak diketahui' }}
                    </p>

                    <p><strong>Semester :</strong> 
                        {{ $jadwal->mataKuliah->semester ?? '-' }}
                    </p>

                    <a href="{{ route('dosen.nilai.show', $jadwal->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> Lihat Nilai
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning text-center">
                Belum ada jadwal kuliah yang diampu.
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
