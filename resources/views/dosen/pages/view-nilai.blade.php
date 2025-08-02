@extends('base.base-dash-index')

@section('menu')
    Kelas
@endsection

@section('submenu')
    Nilai Tugas
@endsection

@section('urlmenu')
    {{ route('dosen.akademik.kelas-view-tugas', $task->jadkul->code) }}
@endsection

@section('subdesc')
    Halaman untuk melihat nilai tugas mahasiswa pada tugas: <strong>{{ $task->title }}</strong>
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

                    <h6>Judul Tugas: {{ $task->title }}</h6>
                    <p>Deskripsi: {!! nl2br(e($task->detail_task)) !!}</p>
                    <p>Batas Waktu: {{ $task->exp_date }} {{ $task->exp_time }}</p>

                    <table class="table table-bordered" id="table1">
                        <thead class="text-center">
                            <tr>
                                <th>#</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($scores as $key => $score)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">{{ $score->student->mhs_nim ?? '-' }}</td>
                                    <td>{{ $score->student->mhs_name ?? '-' }}</td>
                                    <td class="text-center">{{ $score->score ?? '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('dosen.akademik.stask-edit-score', $score->code) }}" class="btn btn-sm btn-primary" title="Edit Nilai">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada nilai yang diinput.</td>
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
