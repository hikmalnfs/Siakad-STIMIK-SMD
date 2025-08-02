@extends('base.base-dash-index')

@section('title', 'Mahasiswa Bimbingan')
@section('menu', 'Mahasiswa Bimbingan')
@section('submenu', 'Daftar Mahasiswa')
@section('subdesc', 'Daftar mahasiswa yang berada dalam bimbingan akademik Anda')

@section('content')
<section class="content py-4">
    <div class="card shadow-sm rounded">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white flex-wrap gap-2">
            <h3 class="card-title mb-0">Daftar Mahasiswa Bimbingan</h3>
            <form method="GET" action="{{ route('dosen.mahasiswa-bimbingan.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <input type="search" name="search" class="form-control form-control-sm" placeholder="Cari nama / NIM" value="{{ request('search') }}" autocomplete="off" style="min-width:200px;" />
                <select name="semester_filter" class="form-select form-select-sm" style="width:130px;">
                    <option value="">-- Filter Semester --</option>
                    @foreach(range(1, 14) as $sem)
                        <option value="{{ $sem }}" {{ request('semester_filter') == $sem ? 'selected' : '' }}>Semester {{ $sem }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-light">Filter</button>
            </form>
        </div>

        <div class="card-body table-responsive p-3">
            @if($mahasiswas->count())
                <table class="table table-hover table-bordered text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th class="text-start">Nama</th>
                            <th>NIM</th>
                            <th>Kelas</th>
                            <th>Status Akademik</th>
                            <th>IPK</th>
                            <th>SKS</th>
                            <th>Semester</th>
                            <th>Rawan DO</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mahasiswas as $i => $mhs)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $mhs->name }}</td>
                                <td>{{ $mhs->numb_nim }}</td>
                                <td>{{ $mhs->kelas->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $status = $mhs->status_akademik;
                                    @endphp
                                    <span class="badge 
                                        {{ $status === 'Aktif' ? 'bg-success' : 
                                           ($status === 'Cuti' ? 'bg-warning text-dark' : 
                                           ($status === 'DO' || $status === 'Drop Out' ? 'bg-danger' : 'bg-secondary')) }}">
                                        {{ $status ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ number_format($mhs->ipk, 2) }}</td>
                                <td>{{ $mhs->bsks ?? '-' }}</td>
                                <td>{{ $mhs->semester ?? '-' }}</td>
                                <td>
                                    @if($mhs->rawan_do)
                                        <span class="badge bg-danger">Ya</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('dosen.mahasiswa-bimbingan.show', $mhs->id) }}" class="btn btn-sm btn-primary" title="Lihat Detail">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Ringkasan --}}
                <div class="mt-4 d-flex justify-content-between fs-6 flex-wrap gap-3">
                    <div><strong>Total Mahasiswa:</strong> {{ $jumlahMahasiswa }}</div>
                    <div><strong>Rata-rata IPK:</strong> {{ number_format($rataIpk, 2) }}</div>
                    <div><strong>Jumlah Rawan DO:</strong> {{ $mahasiswaRawanDO }}</div>
                </div>
            @else
                <div class="alert alert-warning text-center mb-0">
                    Tidak ada mahasiswa bimbingan ditemukan.
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
