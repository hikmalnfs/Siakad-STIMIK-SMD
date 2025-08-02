<li class="sidebar-item {{ Route::is('dosen.home-index') ? 'active' : '' }}">
    <a href="{{ route('dosen.home-index') }}" class="sidebar-link">
        <i class="fa-solid fa-home"></i>
        <span>Home</span>
    </a>
</li>

<li class="sidebar-item {{ Route::is('dosen.home-profile') ? 'active' : '' }}">
    <a href="{{ route('dosen.home-profile') }}" class="sidebar-link">
        <i class="fa-solid fa-user-edit"></i>
        <span>Profile User</span>
    </a>
</li>

<li class="sidebar-title">Data Akademik</li>

<li class="sidebar-item {{ Route::is('dosen.akademik.jadwal-*') ? 'active' : '' }}">
    <a href="{{ route('dosen.akademik.jadwal-index') }}" class="sidebar-link">
        <i class="fa-solid fa-calendar"></i>
        <span>Jadwal Perkuliahan</span>
    </a>
</li>

{{-- <li class="sidebar-item {{ Route::is('dosen.akademik.stask-*') ? 'active' : '' }}">
    <a href="{{ route('dosen.akademik.stask-index') }}" class="sidebar-link">
        <i class="fa-solid fa-tasks"></i>
        <span>Kelola Tugas</span>
    </a>
</li> --}}

{{-- <li class="sidebar-item {{ Route::is('dosen.akademik.kelas-view-absensi*') ? 'active' : '' }}">
    <a href="{{ route('dosen.akademik.kelas-index') }}" class="sidebar-link">
        <i class="fa-solid fa-check-double"></i>
        <span>Kelola Absensi</span>
    </a>
</li> --}}

<li class="sidebar-item {{ Route::is('dosen.nilai.*') ? 'active' : '' }}">
    <a href="{{ route('dosen.nilai.list') }}" class="sidebar-link">
        <i class="fa-solid fa-graduation-cap"></i>
        <span>Kelola Nilai</span>
    </a>
</li>

{{-- Pengajuan KRS: Hanya untuk Dosen Wali --}}
@if(auth('dosen')->check() && !empty(auth('dosen')->user()->wali))
<li class="sidebar-item {{ Route::is('dosen.pengajuan-krs.*') ? 'active' : '' }}">
    <a href="{{ route('dosen.pengajuan-krs.index') }}" class="sidebar-link">
        <i class="fa-solid fa-clipboard-check"></i>
        <span>Pengajuan KRS Mahasiswa</span>
    </a>
</li>
@endif

{{-- Mahasiswa Bimbingan: Hanya untuk Dosen Wali --}}
@if(auth('dosen')->check() && !empty(auth('dosen')->user()->wali))
<li class="sidebar-item {{ Route::is('dosen.mahasiswa-bimbingan.*') ? 'active' : '' }}">
    <a href="{{ route('dosen.mahasiswa-bimbingan.index') }}" class="sidebar-link">
        <i class="fa-solid fa-users"></i>
        <span>Mahasiswa Bimbingan</span>
    </a>
</li>
@endif

