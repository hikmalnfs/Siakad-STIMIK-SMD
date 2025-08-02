@extends('base.base-dash-index')

@section('title')
    Kelola Tugas - Siakad By Internal Developer
@endsection

@section('menu')
    Kelola Tugas
@endsection

@section('submenu')
    Edit Tugas Kuliah
@endsection

@section('urlmenu')
    {{ route('dosen.akademik.stask-index') }}
@endsection

@section('subdesc')
    Halaman untuk mengedit Tugas Kuliah
@endsection

@section('custom-css')
    <link rel="stylesheet" href="{{ asset('dist/assets/extensions/choices.js/public/assets/styles/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/assets/extensions/summernote/summernote-lite.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/assets/compiled/css/form-editor-summernote.css') }}">
@endsection

@section('content')
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-12">
            <form action="{{ route('dosen.akademik.stask-update', $task->code) }}" method="POST" enctype="multipart/form-data">
                @method('PATCH')
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            @yield('submenu') - {{ $task->jadkul->matkul->name ?? '-' }} - Pertemuan {{ $task->jadkul->pert_id ?? '-' }}
                            <div>
                                <button type="submit" class="btn btn-primary mt-1">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                                <a href="@yield('urlmenu')" class="btn btn-warning mt-1">
                                    <i class="fa-solid fa-backward"></i>
                                </a>
                            </div>
                        </h5>
                    </div>
                    <div class="card-body row">
                        <div class="form-group col-lg-4 col-12">
                            <label for="jadkul_id">Pilih Jadwal Kuliah</label>
                            <select name="jadkul_id" id="jadkul_id" class="choices form-select">
                                @foreach ($jadkul as $item)
                                    <option value="{{ $item->id }}" {{ $item->id == $task->jadkul_id ? 'selected' : '' }}>
                                        {{ $item->kelas->name ?? '-' }} - {{ $item->matkul->name ?? '-' }} - Pertemuan {{ $item->pert_id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jadkul_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-lg-4 col-12">
                            <label for="exp_date">Batas Akhir Tanggal</label>
                            <input type="date" id="exp_date" name="exp_date" value="{{ $task->exp_date }}" class="form-control">
                            @error('exp_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-lg-4 col-12">
                            <label for="exp_time">Batas Akhir Waktu</label>
                            <input type="time" id="exp_time" name="exp_time" value="{{ $task->exp_time }}" class="form-control">
                            @error('exp_time')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-lg-12 col-12">
                            <label for="title">Judul Tugas</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="Masukkan judul tugas..." value="{{ $task->title }}">
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-lg-12 col-12">
                            <label for="detail_task">Detail Tugas</label>
                            <textarea name="detail_task" id="summernote" cols="30" rows="10" class="form-control">{!! $task->detail_task !!}</textarea>
                            @error('detail_task')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Daftar tugas lainnya --}}
        <div class="col-lg-12 col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Daftar Tugas Terakhir</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="table1">
                        <thead class="text-center">
                            <tr>
                                <th>#</th>
                                <th>Nama Mata Kuliah</th>
                                <th>Nama Kelas</th>
                                <th>Judul Tugas</th>
                                <th>Batas Akhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stask as $key => $item)
                                <tr class="text-center">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->jadkul->matkul->name ?? '-' }}<br><small>Pertemuan {{ $item->jadkul->pert_id ?? '-' }}</small></td>
                                    <td>{{ $item->jadkul->kelas->name ?? '-' }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($item->exp_date)->translatedFormat('d M Y') }}<br>
                                        {{ \Carbon\Carbon::parse($item->exp_time)->format('H:i') }}
                                    </td>
                                    <td class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('dosen.akademik.stask-edit', $item->code) }}" class="btn btn-primary btn-sm me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('dosen.akademik.stask-destroy', $item->code) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
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
    </div>
</section>
@endsection

@section('custom-js')
    <script src="{{ asset('dist/assets/extensions/choices.js/public/assets/scripts/choices.js') }}"></script>
    <script src="{{ asset('dist/assets/static/js/pages/form-element-select.js') }}"></script>
    <script src="{{ asset('dist/assets/extensions/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ asset('dist/assets/static/js/pages/summernote.js') }}"></script>
@endsection
