@extends('base.base-dash-index')

@section('menu')
    Akademik
@endsection

@section('submenu')
    Daftar Mahasiswa
@endsection

@section('urlmenu')
    {{ route('dosen.akademik.kelas-index') }}
@endsection

@section('subdesc')
    Halaman daftar mahasiswa pada kelas <strong>{{ $jadwal->code ?? '-' }}</strong>
@endsection

@section('title')
    @yield('submenu') - @yield('menu') - Siakad By Internal Developer
@endsection

@section('content')
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        @yield('submenu') - Kelas {{ $jadwal->code ?? '-' }}
                    </h5>
                    <a href="{{ route('dosen.akademik.kelas-index') }}" class="btn btn-warning btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="mahasiswaTable">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>#</th>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Program Studi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mahasiswas as $key => $mhs)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">{{ $mhs->mhs_nim ?? $mhs->nim ?? '-' }}</td>
                                    <td>{{ $mhs->mhs_name ?? $mhs->name ?? '-' }}</td>
                                    <td>{{ $mhs->programStudi->name ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada mahasiswa di kelas ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
