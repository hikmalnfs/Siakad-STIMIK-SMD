@extends('base.base-dash-index')
@section('title')
    Data Master Jadwal Kuliah - Siakad By Internal Developer
@endsection
@section('menu')
    Data Master Jadwal Kuliah
@endsection
@section('submenu')
    Data Master Jadwal Kuliah
@endsection
@section('urlmenu')
    #
@endsection
@section('subdesc')
    Halaman untuk mengelola Jadwal Kuliah
@endsection
@section('custom-css')

@endsection
@section('content')
<section class="section">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title d-flex justify-content-between align-items-center">
                @yield('submenu')
                <div class="">
                    <a href="{{ route($prefix.'master.jadkul-create') }}" class="btn btn-outline-primary"><i class="fa-solid fa-plus"></i></a>
                </div>
            </h5>
        </div>
        <div class="card-body">
            <table class="table table-striped"  id="table1">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Program Studi</th>
                        <th class="text-center">Nama Kelas</th>
                        <th class="text-center">Nama Mata Kuliah</th>
                        <th class="text-center">Dosen Pengajar</th>
                        <th class="text-center">Metode Perkuliahan</th>
                        <th class="text-center">Tanggal Perkuliahan</th>
                        <th class="text-center">Waktu Perkuliahan</th>
                        <th class="text-center">Button</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadkul as $key => $item)
                    <tr>
                        <td data-label="Number">{{ ++$key }}</td>
                        <td data-label="Program Studi">
                            {{ optional($item->kelas->pstudi->fakultas)->name ?? '-' }} <br>
                            {{ optional($item->kelas->pstudi)->name ?? '-' }}
                        </td>
                        <td data-label="Nama Kelas">{{ optional($item->kelas)->code ?? '-' }}</td>
                        <td data-label="Mata Kuliah">
                            {{ optional($item->matkul)->name ?? '-' }} <br>
                            {{ $item->pertemuan ?? '-' }} - {{ $item->bsks ?? '-' }} SKS
                        </td>
                        <td data-label="Nama Dosen">{{ optional($item->dosen)->dsn_name ?? '-' }}</td>
                        <td data-label="Metode">{{ $item->metode ?? '-' }}</td>
                        <td data-label="Tanggal Kuliah">
                            {{ $item->hari ?? '-' }} <br> - <br> 
                            @if($item->tanggal)
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td data-label="Waktu Perkuliahan">
                            {{-- Debug data mentah, hapus kalau sudah oke --}}
                            {{-- start raw: {{ $item->start ?? 'null' }} <br> ended raw: {{ $item->ended ?? 'null' }} <br> --}}

                            @if($item->start)
                                {{ \Carbon\Carbon::parse($item->start)->format('H:i') }}
                            @else
                                -
                            @endif
                            <br> - <br>
                            @if($item->ended)
                                {{ \Carbon\Carbon::parse($item->ended)->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="d-flex justify-content-center align-items-center">
                            <a href="#" style="margin-right: 10px" data-bs-toggle="modal" data-bs-target="#updateJadkul{{ $item->code }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <a href="{{ route($prefix.'master.jadkul-absen-view', $item->code) }}"  style="margin-right: 10px" class="btn btn-outline-info"><i class="fa-solid fa-user-check"></i></a>
                            <form id="delete-form-{{ $item->code }}" action="{{ route($prefix.'master.jadkul-destroy', $item->code) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a type="button" class="bs-tooltip btn btn-rounded btn-outline-danger"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"
                                    data-original-title="Delete"
                                    data-url="{{ route($prefix.'master.jadkul-destroy', $item->code) }}"
                                    data-name="{{ $item->name }}"
                                    onclick="deleteData('{{ $item->code }}')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- Modal edit, tetap pakai sesuai kode lama --}}

@endsection
@section('custom-js')
<script>
    // Fungsi deleteData sesuai kebutuhan, pastikan ada di blade layout utama
    function deleteData(code) {
        if(confirm('Apakah anda yakin ingin menghapus jadwal dengan kode ' + code + '?')) {
            document.getElementById('delete-form-' + code).submit();
        }
    }
</script>
@endsection
