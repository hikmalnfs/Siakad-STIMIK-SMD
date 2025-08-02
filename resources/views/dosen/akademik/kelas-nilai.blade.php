@extends('base.base-dash-index')

@section('menu')
    Kelas
@endsection

@section('submenu')
    Nilai Mahasiswa
@endsection

@section('urlmenu')
    {{ route('dosen.akademik.kelas-index') }}
@endsection

@section('subdesc')
    Halaman untuk melihat dan mengelola nilai mahasiswa pada kelas ini
@endsection

@section('title')
    @yield('submenu') - @yield('menu') - Siakad By Internal Developer
@endsection

@section('content')
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="card">

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title">@yield('menu') - @yield('submenu')</h5>
                    <a href="@yield('urlmenu')" class="btn btn-warning">
                        <i class="fa-solid fa-backward"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <h6>Kelas: {{ $jadwal->mataKuliah->name ?? '-' }} - {{ $jadwal->jenisKelas->name ?? '-' }}</h6>

                    <form method="POST" action="{{ route('dosen.akademik.kelas-update-nilai', $jadwal->id) }}">
                        @csrf
                        @method('PATCH')

                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>NIM Mahasiswa</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Nilai Angka</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jadwal->kelas->first()->mahasiswas as $key => $mahasiswa)
                                    @php
                                        $nilaiObj = $jadwal->nilai->firstWhere('mahasiswa_id', $mahasiswa->id);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td class="text-center">{{ $mahasiswa->numb_nim ?? $mahasiswa->num_nim ?? '-' }}</td>
                                        <td>{{ $mahasiswa->mhs_name ?? $mahasiswa->name ?? '-' }}</td>
                                        <td class="text-center">
                                            <input type="number" name="nilai[{{ $mahasiswa->id }}]" 
                                                value="{{ old('nilai.' . $mahasiswa->id, $nilaiObj->nilai_angka ?? '') }}" 
                                                min="0" max="100" class="form-control" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-primary mt-3">Simpan Nilai</button>
                    </form>

                </div>

            </div>
        </div>
    </div>
</section>
@endsection
