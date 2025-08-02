@extends('base.base-dash-index')
@section('title')
    Data Master Peguruan Tinggi - Siakad By Internal Developer
@endsection
@section('menu')
    Data Master Peguruan Tinggi
@endsection
@section('submenu')
    Daftar Data Peguruan Tinggi
@endsection
@section('submenu0')
    Tambah Data Peguruan Tinggi
@endsection
@section('urlmenu')
    #
@endsection
@section('subdesc')
    Halaman untuk mengelola Data Peguruan Tinggi
@endsection
@section('content')
<section class="section row">

    <div class="col-lg-4 col-12">
        <form action="{{ route($prefix.'master.fakultas-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">@yield('submenu0')</h5>
                    <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-paper-plane"></i></button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Nama Peguruan Tinggi</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Inputkan nama Peguruan Tinggi...">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="code">Kode  ( 3 Huruf Kapital )</label>
                        <input type="text" class="form-control" name="code" id="code" placeholder="Inputkan kode Peguruan Tinggi..." maxlength="3" uppercase onkeydown="return /[a-zA-Z]/i.test(event.key)" >
                        @error('code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="head_id">Kepala Peguruan Tinggi </label>
                        <select name="head_id" id="head_id" class="form-select">
                            <option value="" selected>Pilih Kepala Peguruan Tinggi</option>
                            @foreach ($dosen as $item)
                                <option value="{{ $item->id }}">{{ $item->dsn_name }}</option>
                            @endforeach
                        </select>
                        @error('head_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg-8 col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">@yield('submenu')</h5>

            </div>
            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <th class="text-center">#</th>
                        <th class="text-center">Nama Peguruan Tinggi</th>
                        <th class="text-center">Kode Peguruan Tinggi</th>
                        <th class="text-center">Kepala Peguruan Tinggi</th>
                        <th class="text-center">Button</th>
                    </thead>
                    <tbody>
                        @foreach ($fakultas as $key => $item)
                            <tr class="">
                                <td data-label="Number">{{ ++$key }}</td>
                                <td data-label="Nama Peguruan Tinggi">{{ $item->name }}</td>
                                <td data-label="Kode Peguruan Tinggi">{{ $item->code }}</td>
                                <td data-label="Kepala Peguruan Tinggi">{{ $item->head->dsn_name }}</td>
                                <td class="d-flex justify-content-center align-items-center">
                                    <a href="#" style="margin-right: 10px" data-bs-toggle="modal" data-bs-target="#updateFakultas{{ $item->code }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    {{-- <a href="{{ route($prefix.'staffmanager-dosen-view', $item->code) }}"  style="margin-right: 10px" class="btn btn-outline-info"><i class="fa-solid fa-eye"></i></a> --}}
                                    <form id="delete-form-{{ $item->code }}"
                                        action="{{ route($prefix.'master.fakultas-destroy', $item->code) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <a type="button" class="bs-tooltip btn btn-rounded btn-outline-danger"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"
                                            data-original-title="Delete"
                                            data-url="{{ route($prefix.'master.fakultas-destroy', $item->code) }}"
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
    </div>

</section>
<div class="me-1 mb-1 d-inline-block">

    <!--Extra Large Modal -->
    @foreach ($fakultas as $item)
    <form action="{{ route($prefix.'master.fakultas-update', $item->code) }}" method="POST" enctype="multipart/form-data">
        @method('patch')
        @csrf
        <div class="modal fade text-left w-100" id="updateFakultas{{$item->code}}" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel16" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-l"
                role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel16">Edit Peguruan Tinggi - {{ $item->name }}</h4>
                        <div class="">
    
                            <button type="submit" class="btn btn-outline-primary" >
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <label for="name">Nama Peguruan Tinggi</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Inputkan nama Peguruan Tinggi..." value="{{ $item->name }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="code">Kode Peguruan Tinggi ( 3 Huruf Kapital )</label>
                                <input type="text" class="form-control" name="code" id="code" placeholder="Inputkan kode Peguruan Tinggi..." value="{{ $item->code }}" maxlength="3" uppercase onkeydown="return /[a-zA-Z]/i.test(event.key)" >
                                @error('code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="head_id">Kepala Peguruan Tinggi</label>
                                <select name="head_id" id="head_id" class="form-select">
                                    <option value="" selected>Pilih Kepala Peguruan Tinggi</option>
                                    @foreach ($dosen as $dsn)
                                        <option value="{{ $dsn->id }}" {{ $item->head_id == $dsn->id ? 'selected' : '' }}>{{ $dsn->dsn_name }}</option>
                                    @endforeach
                                </select>
                                @error('head_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endforeach
</div>
@endsection
