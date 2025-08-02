@extends('base.base-dash-index')
@section('title')
    SIAKAD PT - Internal Developer
@endsection
@section('menu')
    Profile
@endsection
@section('submenu')
    Edit Profile
@endsection
@section('urlmenu')
    {{ route('web-admin.home-index') }}
@endsection
@section('subdesc')
    Halaman untuk mengubah profile pengguna
@endsection
@section('content')
    <section class="section row">
        <div class="col-lg-4 col-12">

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Ubah Foto Profile</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('mahasiswa.home-profile-save-image') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <img src="{{ asset('storage/images/' . Auth::guard('mahasiswa')->user()->mhs_image) }}" class="card-img-top" alt="">
                        <hr>
                        <div class="form-group">
                            <label for="mhs_image">Upload Foto Profile</label>
                            <div class="d-flex justify-content-between align-items-center">

                                <input type="file" class="form-control" name="mhs_image" id="mhs_image">
                                @error('mhs_image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <button type="submit" class="btn btn-outline-primary" style="margin-left: 10px"><i class="fa-solid fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12">

            <div class="card">
                <div class="card-body">

                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"> Personal</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false"> Kontak</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"> Security</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                            <hr>
                            <form action="{{ route('mahasiswa.home-profile-save-data') }}" method="POST" enctype="multipart/form-data">
                                @method('PATCH')
                                @csrf

                        <div class="row">
                            <div class="form-group col-lg-6 col-12">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Nama lengkap..." readonly value="{{ Auth::guard('mahasiswa')->user()->name }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6 col-12">
                                <label for="numb_nim">Nomor NIM</label>
                                <input type="text" name="numb_nim" id="numb_nim" class="form-control" placeholder="Nomor NIM..." readonly value="{{ Auth::guard('mahasiswa')->user()->numb_nim }}">
                                @error('numb_nim')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6 col-12">
                                <label for="years_id">Tahun Masuk</label>
                                <input type="text" class="form-control" readonly value="Angkatan {{ optional(Auth::guard('mahasiswa')->user()->kelas?->taka)->year_start ?? 'Tidak diketahui' }}">
                                @error('years_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6 col-12">
                                <label for="faku_id">Peguruan Tinggi</label>
                                <input type="text" class="form-control" readonly
                                    value="{{ optional(Auth::guard('mahasiswa')->user()->kelas?->pstudi?->fakultas)->name ?? 'Tidak diketahui' }}">
                                @error('faku_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6 col-12">
                                <label for="class_id">Program Studi</label>
                                <input type="text" name="class_id" id="class_id" class="form-control" readonly
                                    placeholder="Nama Program Studi..."
                                    value="{{ optional(Auth::guard('mahasiswa')->user()->kelas?->pstudi)->name 
                                        ? optional(Auth::guard('mahasiswa')->user()->kelas?->pstudi)->name . ' - ' . optional(Auth::guard('mahasiswa')->user()->kelas)->name 
                                        : 'Tidak diketahui' }}">
                                @error('class_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6 col-12">
                                <label for="class_code">Kelas</label>
                                <input type="text" name="class_code" id="class_code" class="form-control" placeholder="Nama Kelas..." readonly value="{{ optional(Auth::guard('mahasiswa')->user()->kelas)->code ?? 'Tidak diketahui' }}">
                                @error('class_code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6 col-12">
                                <label for="bio_gender">Jenis Kelamin</label>
                                <select name="bio_gender" id="bio_gender" class="form-select">
                                    <option value="" selected>Pilih Jenis Kelamin</option>
                                    <option value="L" {{ Auth::guard('mahasiswa')->user()->bio_gender === 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="P" {{ Auth::guard('mahasiswa')->user()->bio_gender === 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('bio_gender')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6 col-12">
                                <label for="bio_placebirth">Tempat Lahir</label>
                                <input type="text" name="bio_placebirth" id="bio_placebirth" class="form-control" placeholder="Tempat Lahir..." value="{{ Auth::guard('mahasiswa')->user()->bio_placebirth}}">
                                @error('bio_placebirth')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6 col-12">
                                <label for="bio_datebirth">Tanggal Lahir</label>
                                <input type="date" name="bio_datebirth" id="bio_datebirth" class="form-control" placeholder="Tanggal Lahir..." value="{{ Auth::guard('mahasiswa')->user()->bio_datebirth }}">
                                @error('bio_datebirth')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-lg-6 col-12">
                                <label for="mhs_reli">Agama</label>
                                <select name="mhs_reli" id="mhs_reli" class="form-select">
                                    <option value="" selected>Pilih Agama</option>
                                    <option value="1" {{ Auth::guard('mahasiswa')->user()->raw_bio_religion === '1' ? 'selected' : '' }}>Islam</option>
                                    <option value="2" {{ Auth::guard('mahasiswa')->user()->raw_bio_religion === '2' ? 'selected' : '' }}>Kristen Protestan</option>
                                    <option value="3" {{ Auth::guard('mahasiswa')->user()->raw_bio_religion === '3' ? 'selected' : '' }}>Katolik</option>
                                    <option value="4" {{ Auth::guard('mahasiswa')->user()->raw_bio_religion === '4' ? 'selected' : '' }}>Hindu</option>
                                    <option value="5" {{ Auth::guard('mahasiswa')->user()->raw_bio_religion === '5' ? 'selected' : '' }}>Buddha</option>
                                    <option value="6" {{ Auth::guard('mahasiswa')->user()->raw_mbio_religion === '6' ? 'selected' : '' }}>Konghucu</option>
                                </select>
                                @error('mhs_reli')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end align-items-center">
                                <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-save"></i> Save</button>
                            </div>
                        </div>


                        </div>
                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                            <hr>
                            <form action="{{ route('mahasiswa.home-profile-save-kontak') }}" method="POST" enctype="multipart/form-data">
                                @method('PATCH')
                                @csrf

                                <div class="row">
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="phone">Nomor HandPhone</label>
                                        <input type="text" class="form-control" name="phone" id="phone" value="{{ Auth::guard('mahasiswa')->user()->phone }}">
                                        @error('phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="email">Alamat Email</label>
                                        <input type="text" class="form-control" name="email" id="email" readonly value="{{ Auth::guard('mahasiswa')->user()->email }}">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="mhs_parent_father">Nama Ayah</label>
                                        <input type="text" class="form-control" name="mhs_parent_father" id="mhs_parent_father" placeholder="nama ayah..." value="{{ Auth::guard('mahasiswa')->user()->father_name }}">
                                        @error('mhs_parent_father')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="mhs_parent_father_phone">Nomor Telepon Ayah</label>
                                        <input type="text" class="form-control" name="mhs_parent_father_phone" id="mhs_parent_father_phone" placeholder="nomor telepon ayah..." value="{{ Auth::guard('mahasiswa')->user()->father_phone }}">
                                        @error('mhs_parent_father_phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="mhs_parent_mother">Nama Ibu</label>
                                        <input type="text" class="form-control" name="mhs_parent_mother" id="mhs_parent_mother" placeholder="nama ibu..." value="{{ Auth::guard('mahasiswa')->user()->mother_name }}">
                                        @error('mhs_parent_mother')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="mhs_parent_mother_phone">Nomor Telepon Ibu</label>
                                        <input type="text" class="form-control" name="mhs_parent_mother_phone" id="mhs_parent_mother_phone" placeholder="nomor telepon ibu..." value="{{ Auth::guard('mahasiswa')->user()->mother_phone }}">
                                        @error('mhs_parent_mother_phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="mhs_wali_name">Nama Wali Mahasiswa</label>
                                        <input type="text" class="form-control" name="mhs_wali_name" id="mhs_wali_name" placeholder="nama wali..." value="{{ Auth::guard('mahasiswa')->user()->guard_name }}">
                                        @error('mhs_wali_name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="mhs_wali_phone">Nomor Telepon Wali</label>
                                        <input type="text" class="form-control" name="mhs_wali_phone" id="mhs_wali_phone" placeholder="nomor telepon wali..." value="{{ Auth::guard('mahasiswa')->user()->guard_phone }}">
                                        @error('mhs_wali_phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-12 col-12">
                                        <label for="mhs_addr_domisili">Alamat Lengkap Domisili / Tempat Tinggal</label>
                                        <textarea cols="15" rows="4" class="form-control" name="mhs_addr_domisili" id="mhs_addr_domisili" placeholder="alamat lengkap domisili / tempat tinggal..." value="{{ Auth::guard('mahasiswa')->user()->domicile_addres }}">{{ Auth::guard('mahasiswa')->user()->domicile_addres == null ? 'inputkan alamat lengkap / domisili' : Auth::guard('mahasiswa')->user()->domicile_addres }}</textarea>
                                        @error('mhs_addr_domisili')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="mhs_addr_kelurahan">Kelurahan</label>
                                        <input type="text" class="form-control" name="mhs_addr_kelurahan" id="mhs_addr_kelurahan" placeholder="nama kelurahan..." value="{{ Auth::guard('mahasiswa')->user()->domicile_village }}">
                                        @error('mhs_addr_kelurahan')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="mhs_addr_kecamatan">Kecamatan</label>
                                        <input type="text" class="form-control" name="mhs_addr_kecamatan" id="mhs_addr_kecamatan" placeholder="nama kecamatan..." value="{{ Auth::guard('mahasiswa')->user()->domicile_subdistrict }}">
                                        @error('mhs_addr_kecamatan')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="mhs_addr_kota">Kota</label>
                                        <input type="text" class="form-control" name="mhs_addr_kota" id="mhs_addr_kota" placeholder="nama kota..." value="{{ Auth::guard('mahasiswa')->user()->domicile_city }}">
                                        @error('mhs_addr_kota')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="mhs_addr_provinsi">Provinsi</label>
                                        <input type="text" class="form-control" name="mhs_addr_provinsi" id="mhs_addr_provinsi" placeholder="nama provinsi..." value="{{ Auth::guard('mahasiswa')->user()->domicile_province }}">
                                        @error('mhs_addr_provinsi')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="d-flex justify-content-end align-items-center">
                                        <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-save"></i> Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <hr>
                            <form action="{{ route('mahasiswa.home-profile-save-password') }}" method="POST" enctype="multipart/form-data">
                                @method('PATCH')
                                @csrf

                                <div class="row">
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="SecurityKey">Security Key</label>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <input type="password" class="form-control" name="mhs_code" id="SecurityKey" value="{{ Auth::guard('mahasiswa')->user()->mhs_code }}" disabled>
                                            <span class="btn btn-sm btn-outline-danger" style="margin-left: 5px" id="showPasswordButton"><i class="fa-solid fa-eye"></i></span>
                                            @error('mhs_code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="oldPassword">Password Lama</label>
                                        <div class="d-flex justify-content-between align-items-center">

                                            <input type="password" class="form-control" name="old_password" id="oldPassword">
                                            <span class="btn btn-sm btn-outline-danger" style="margin-left: 5px" id="showPasswordButton"><i class="fa-solid fa-eye"></i></span>
                                        </div>
                                        @error('old_password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="newPassword">Password Baru</label>
                                        <div class="d-flex justify-content-between align-items-center">

                                            <input type="password" class="form-control" name="new_password" id="newPassword">
                                            <span class="btn btn-sm btn-outline-danger" style="margin-left: 5px" id="showPasswordButton"><i class="fa-solid fa-eye"></i></span>
                                        </div>
                                        @error('new_password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group col-lg-6 col-12">
                                        <label for="newPasswordKonfirm">Konfirmasi Password Baru</label>
                                        <div class="d-flex justify-content-between align-items-center">

                                            <input type="password" class="form-control" name="new_password_confirmed" id="newPasswordKonfirm">
                                            <span class="btn btn-sm btn-outline-danger" style="margin-left: 5px" id="showPasswordButton"><i class="fa-solid fa-eye"></i></span>
                                        </div>
                                        @error('new_password_confirmed')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center">
                                        <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-save"></i> Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
@section('custom-js')
    <script>
        document.getElementById("mhs_image").onchange = function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.querySelector('.card-img-top');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        };
    </script>
    <script>
        const showPasswordButtons = document.querySelectorAll('.btn-outline-danger');
        showPasswordButtons.forEach((btn, index) => {
            const passwordInput = btn.previousElementSibling;
            btn.addEventListener('click', () => {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text'; // Show password
                    btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>'; // Change icon to eye-slash
                } else {
                    passwordInput.type = 'password'; // Hide password
                    btn.innerHTML = '<i class="fa-solid fa-eye"></i>'; // Change icon back to eye
                }
            });
        });
    </script>
@endsection
