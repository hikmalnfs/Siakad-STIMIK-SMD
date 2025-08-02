@extends('base.base-dash-index')

@section('title', 'Tambah KRS Mahasiswa')
@section('menu', 'Mahasiswa Bimbingan')
@section('submenu', 'Tambah KRS')
@section('subdesc', 'Pilih dan atur mata kuliah untuk KRS mahasiswa berdasarkan semester')
@include('sweetalert::alert')

@section('content')
<section class="content py-4">
    <div class="container-fluid">

        {{-- Informasi Mahasiswa --}}
        <div class="card shadow-sm rounded mb-4">
            <div class="card-body bg-gradient-to-r from-blue-700 to-indigo-700 text-white rounded-top">
                <h5 class="mb-0">Tambah KRS - {{ $mahasiswa->name }} ({{ $mahasiswa->numb_nim }})</h5>
                <small class="opacity-75">Program Studi: {{ $mahasiswa->prodi->name ?? '-' }} | Semester Aktif: {{ $semester_aktif ?? '-' }}</small>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <select id="filter-semester" class="form-select" onchange="filterJadwal()">
                    <option value="">Semua Semester</option>
                    @foreach($daftarSemester as $smt)
                        <option value="{{ $smt }}">Semester {{ $smt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2">
                <input type="text" id="search-matkul" class="form-control" placeholder="Cari nama mata kuliah..." onkeyup="filterJadwal()">
            </div>
        </div>

        {{-- Form Tambah KRS --}}
        <form method="POST" action="{{ route('dosen.mahasiswa-bimbingan.krs.store', $mahasiswa->id) }}">
            @csrf

            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                <table class="table table-bordered table-hover text-center align-middle" id="jadwal-table">
                    <thead class="sticky-top table-primary text-dark">
                        <tr>
                            <th></th>
                            <th>Mata Kuliah</th>
                            <th>Semester</th>
                            <th>SKS</th>
                            <th>Kelas</th>
                            <th>Dosen</th>
                        </tr>
                    </thead>
                    <tbody>
@forelse($validJadwal as $jadwal)
    <tr data-semester="{{ $jadwal->mataKuliah->semester ?? '' }}" data-nama="{{ strtolower($jadwal->mataKuliah->name ?? '') }}">
        <td>
            <input type="checkbox" name="jadwal_ids[]" value="{{ $jadwal->id }}">
        </td>
        <td class="text-start">{{ $jadwal->mataKuliah->name ?? '-' }}</td>
        <td>{{ $jadwal->mataKuliah->semester ?? '-' }}</td>
        <td>{{ $jadwal->mataKuliah->bsks ?? '-' }}</td>
        <td>
            @foreach($jadwal->kelas as $kelas)
                {{ $kelas->name }}<br>
            @endforeach
        </td>
        <td>{{ $jadwal->dosen->name ?? '-' }}</td>
    </tr>
@empty
    <tr><td colspan="6" class="text-muted">Tidak ada jadwal kuliah tersedia.</td></tr>
@endforelse

                    </tbody>
                </table>
            </div>

            {{-- Aksi --}}
            <div class="text-end mt-3">
                <a href="{{ route('dosen.mahasiswa-bimbingan.show', $mahasiswa->id) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan KRS</button>
            </div>
        </form>

    </div>
</section>
@endsection

@section('scripts')
<script>
    function filterJadwal() {
        const semester = document.getElementById('filter-semester').value;
        const keyword = document.getElementById('search-matkul').value.toLowerCase();
        const rows = document.querySelectorAll('#jadwal-table tbody tr');

        rows.forEach(row => {
            const rowSemester = row.getAttribute('data-semester');
            const rowNama = row.getAttribute('data-nama');

            const matchSemester = !semester || semester == rowSemester;
            const matchNama = !keyword || rowNama.includes(keyword);

            row.style.display = (matchSemester && matchNama) ? '' : 'none';
        });
    }
</script>
@endsection
