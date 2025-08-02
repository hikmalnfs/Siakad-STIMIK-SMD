@extends('base.base-dash-index')

@section('title')
    Jadwal Mengajar - Siakad By Internal Developer
@endsection

@section('menu')
    Jadwal Mengajar
@endsection

@section('submenu')
    Lihat Data
@endsection

@section('urlmenu')
    #
@endsection

@section('subdesc')
    Halaman untuk melihat Jadwal Mengajar
@endsection

@section('content')
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@yield('menu')</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="table1">
                        <thead class="text-center">
                            <tr>
                                <th>#</th>
                                <th>Nama Kelas</th>
                                <th>Nama Mata Kuliah</th>
                                <th>Dosen Pengajar</th>
                                <th>Metode</th>
                                <th>Lokasi</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jadkul as $key => $item)
                                <tr class="text-center">
                                    <td>{{ $key + 1 }}</td>
                                    
                                    {{-- Jika kelas many-to-many, tampilkan semua kode kelas, dipisah koma --}}
                                    <td>
                                        {{ $item->kelas->pluck('code')->join(', ') ?: '-' }}
                                    </td>

                                    {{-- Mata Kuliah --}}
                                    <td>
                                        {{ $item->matakuliah->name ?? $item->matkul->name ?? '-' }}<br>
                                        <small>{{ $item->pert_id ?? '-' }} - {{ $item->bsks ?? $item->sks ?? '-' }} SKS</small>
                                    </td>

                                    {{-- Dosen Pengajar --}}
                                    <td>{{ $item->dosen->dsn_name ?? $item->dosen->name ?? '-' }}</td>

                                    {{-- Metode --}}
                                    <td>{{ $item->jenisKelas->name ?? '-' }}</td>

                                    {{-- Lokasi --}}
                                    <td>
                                        {{ $item->ruang->gedung->name ?? '-' }}<br>
                                        <small>{{ $item->ruang->name ?? '-' }} - Lantai {{ $item->ruang->floor ?? '-' }}</small>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td>
                                        {{ $item->days_id ?? '-' }}<br>
                                        <small>
                                            @if(!empty($item->date))
                                                {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </small>
                                    </td>

                                    {{-- Waktu --}}
                                    <td>
                                        {{ $item->start ?? '-' }}<br> - <br>{{ $item->ended ?? '-' }}
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="d-flex justify-content-center">
                                        <a href="{{ route('dosen.akademik.jadwal-view-absen', $item->code ?? $item->id) }}" class="btn btn-success btn-sm me-1" title="Lihat Absen">
                                            <i class="fas fa-calendar-check"></i>
                                        </a>
                                        <a href="{{ route('dosen.akademik.jadwal-view-feedback', $item->code ?? $item->id) }}" class="btn btn-warning btn-sm" title="Lihat Feedback">
                                            <i class="fas fa-star"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada jadwal tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
