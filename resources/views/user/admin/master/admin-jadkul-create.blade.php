@extends('base.base-dash-index')

@section('title')
    Tambah Jadwal Kuliah - Siakad By Internal Developer
@endsection

@section('menu')
    Data Master Jadwal Kuliah
@endsection

@section('submenu')
    Tambah Jadwal Kuliah
@endsection

@section('content')
<section class="section row">
    <div class="col-lg-12 col-12">
        <form action="{{ isset($prefix) ? route($prefix.'master.jadkul-store') : route('master.jadkul-store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">@yield('submenu')</h5>
                    <div>
                        <a href="{{ isset($prefix) ? route($prefix.'master.jadkul-index') : route('master.jadkul-index') }}" class="btn btn-outline-warning">
                            <i class="fa-solid fa-backward"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa-solid fa-paper-plane"></i> Simpan
                        </button>
                    </div>
                </div>
                <div class="card-body row">
                    <!-- Mata Kuliah -->
                    <div class="form-group col-lg-4 col-12">
                        <label for="matkul_id">Mata Kuliah</label>
                        <select name="matkul_id" id="matkul_id" class="form-select">
                            <option value="" selected>Pilih Mata Kuliah</option>
                            @foreach ($mata_kuliah as $matkul)
                                <option value="{{ $matkul->id }}" {{ old('matkul_id') == $matkul->id ? 'selected' : '' }}>
                                    {{ $matkul->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('matkul_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Dosen -->
                    <div class="form-group col-lg-4 col-12">
                        <label for="dosen_id">Dosen Pengajar</label>
                        <select name="dosen_id" id="dosen_id" class="form-select">
                            <option value="" selected>Pilih Dosen</option>
                            @foreach ($dosen as $d)
                                <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->dsn_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('dosen_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Ruang -->
                    <div class="form-group col-lg-4 col-12">
                        <label for="ruang_id">Ruangan</label>
                        <select name="ruang_id" id="ruang_id" class="form-select">
                            <option value="" selected>Pilih Ruangan</option>
                            @foreach ($ruang as $r)
                                <option value="{{ $r->id }}" {{ old('ruang_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('ruang_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Jenis Kelas -->
                    <div class="form-group col-lg-3 col-12">
                        <label for="jenis_kelas_id">Jenis Kelas</label>
                        <select name="jenis_kelas_id" id="jenis_kelas_id" class="form-select">
                            <option value="" selected>Pilih Jenis Kelas</option>
                            @foreach ($jenis_kelas as $jk)
                                <option value="{{ $jk->id }}" {{ old('jenis_kelas_id') == $jk->id ? 'selected' : '' }}>
                                    {{ $jk->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenis_kelas_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Waktu Kuliah -->
                    <div class="form-group col-lg-3 col-12">
                        <label for="waktu_kuliah_id">Waktu Kuliah (Pilih Banyak)</label>
                        <select name="waktu_kuliah_id[]" id="waktu_kuliah_id" class="form-select" multiple size="4">
                            @foreach ($waktu_kuliah as $wk)
                                <option value="{{ $wk->id }}" {{ (collect(old('waktu_kuliah_id'))->contains($wk->id)) ? 'selected' : '' }}>
                                    {{ $wk->start_time }} - {{ $wk->end_time }}
                                </option>
                            @endforeach
                        </select>
                        @error('waktu_kuliah_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Beban SKS -->
                    <div class="form-group col-lg-3 col-12">
                        <label for="bsks">Beban SKS</label>
                        <input type="number" name="bsks" id="bsks" class="form-control" min="1" max="8" placeholder="Jumlah SKS" value="{{ old('bsks') }}">
                        @error('bsks')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Pertemuan -->
                    <div class="form-group col-lg-3 col-12">
                        <label for="pertemuan">Pertemuan Ke-</label>
                        <input type="number" name="pertemuan" id="pertemuan" class="form-control" min="1" max="16" placeholder="Pertemuan ke-" value="{{ old('pertemuan') }}">
                        @error('pertemuan')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Hari -->
                    <div class="form-group col-lg-3 col-12">
                        <label for="hari">Hari</label>
                        <select name="hari" id="hari" class="form-select">
                            <option value="" selected>Pilih Hari</option>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)
                                <option value="{{ $day }}" {{ old('hari') == $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                        @error('hari')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Metode -->
                    <div class="form-group col-lg-3 col-12">
                        <label for="metode">Metode Perkuliahan</label>
                        <select name="metode" id="metode" class="form-select">
                            <option value="" selected>Pilih Metode</option>
                            <option value="Tatap Muka" {{ old('metode') == 'Tatap Muka' ? 'selected' : '' }}>Tatap Muka</option>
                            <option value="Teleconference" {{ old('metode') == 'Teleconference' ? 'selected' : '' }}>Teleconference</option>
                        </select>
                        @error('metode')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Tanggal -->
                    <div class="form-group col-lg-4 col-12">
                        <label for="tanggal">Tanggal Perkuliahan</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal') }}">
                        @error('tanggal')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Link -->
                    <div class="form-group col-lg-8 col-12">
                        <label for="link">Link Perkuliahan (opsional)</label>
                        <input type="url" name="link" id="link" class="form-control" placeholder="https://..." value="{{ old('link') }}">
                        @error('link')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Kelas -->
                    <div class="form-group col-lg-12 col-12">
                        <label for="kelas_ids">Pilih Kelas (Bisa pilih banyak)</label>
                        <select name="kelas_ids[]" id="kelas_ids" class="form-select" multiple size="5">
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}" {{ (collect(old('kelas_ids'))->contains($k->id)) ? 'selected' : '' }}>
                                    {{ $k->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas_ids')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
                <!-- Tambahkan tombol submit di bawah -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-paper-plane"></i> Simpan Jadwal Kuliah
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
