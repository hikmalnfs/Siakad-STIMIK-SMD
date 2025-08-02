@extends('base.base-dash-index')

@section('menu')
    Kelas
@endsection

@section('submenu')
    Tugas Mahasiswa
@endsection

@section('urlmenu')
    {{ route('dosen.akademik.kelas-index') }}
@endsection

@section('subdesc')
    Halaman untuk melihat dan mengelola tugas mahasiswa pada kelas ini
@endsection

@section('title')
    @yield('submenu') - @yield('menu') - Siakad By Internal Developer
@endsection

@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">@yield('menu') - @yield('submenu')</h5>
                    <a href="@yield('urlmenu')" class="btn btn-warning">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>Judul Tugas</th>
                                <th>Deskripsi</th>
                                <th>Batas Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tugas as $key => $task)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td>{{ $task->title ?? '-' }}</td>
                                    <td>{{ Str::limit($task->description, 50, '...') ?? '-' }}</td>
                                    <td class="text-center">{{ $task->deadline ? date('d M Y', strtotime($task->deadline)) : '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('dosen.akademik.kelas-view-nilai', $task->code) }}" class="btn btn-sm btn-info" title="Lihat Nilai">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <a href="{{ route('dosen.akademik.stask-view', $task->code) }}" class="btn btn-sm btn-primary" title="Detail Tugas">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada tugas tersedia.</td>
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
