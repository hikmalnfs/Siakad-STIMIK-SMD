@php
    $user = Auth::guard('dosen')->user();
    $foto = $user->photo ?? 'default.jpg';
@endphp

@extends('base.base-dash-index')

@section('title', 'SIAKAD PT - Internal Developer')
@section('menu', 'Profile')
@section('submenu', 'Edit Profile')
@section('urlmenu', route('web-admin.home-index'))
@section('subdesc', 'Halaman untuk mengubah profile pengguna')

@section('content')
<section class="section row">
    {{-- Foto Profile --}}
    <div class="col-lg-4 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Ubah Foto Profile</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('dosen.home-profile-save-image') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <img src="{{ asset('storage/images/' . $foto) }}" class="card-img-top mb-3" alt="Foto Profile">

                    <div class="form-group">
                        <label for="photo">Upload Foto Profile</label>
                        <div class="d-flex">
                            <input type="file" class="form-control" name="photo" id="photo">
                            <button type="submit" class="btn btn-outline-primary ms-2">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                        @error('photo') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tab Personal / Kontak / Security --}}
    <div class="col-lg-8 col-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" id="profileTab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#personal">Personal</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#kontak">Kontak</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#security">Security</a></li>
                </ul>

                <div class="tab-content mt-3">
                    {{-- Tab Personal --}}
                    <div class="tab-pane fade show active" id="personal">
                        <form action="{{ route('dosen.home-profile-save-data') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>NIDN</label>
                                    <input type="text" name="numb_nidn" class="form-control" value="{{ $user->numb_nidn }}">
                                    @error('numb_nidn') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="bio_placebirth" class="form-control" value="{{ $user->bio_placebirth }}">
                                    @error('bio_placebirth') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="bio_datebirth" class="form-control" value="{{ $user->bio_datebirth }}">
                                    @error('bio_datebirth') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Jenis Kelamin</label>
                                    <select name="bio_gender" class="form-select">
                                        <option value="">Pilih</option>
                                        <option value="L" {{ $user->bio_gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ $user->bio_gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('bio_gender') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-save"></i> Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Tab Kontak --}}
                    <div class="tab-pane fade" id="kontak">
                        <form action="{{ route('dosen.home-profile-save-kontak') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Nomor HP</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-save"></i> Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Tab Security --}}
                    <div class="tab-pane fade" id="security">
                        <form action="{{ route('dosen.home-profile-save-password') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Password Lama</label>
                                    <input type="password" name="old_password" class="form-control">
                                    @error('old_password') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Password Baru</label>
                                    <input type="password" name="new_password" class="form-control">
                                    @error('new_password') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Konfirmasi Password Baru</label>
                                    <input type="password" name="new_password_confirmed" class="form-control">
                                    @error('new_password_confirmed') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-save"></i> Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div> {{-- tab-content --}}
            </div>
        </div>
    </div>
</section>
@endsection

@section('custom-js')
<script>
    // Preview image sebelum upload
    document.getElementById("photo").onchange = function(event) {
        var reader = new FileReader();
        reader.onload = function() {
            document.querySelector('.card-img-top').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    };
</script>
@endsection
