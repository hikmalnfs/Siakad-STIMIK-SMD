{{-- Sidebar Mahasiswa --}}
<li class="sidebar-item {{ Route::is('mahasiswa.home-index') ? 'active' : '' }}">
    <a href="{{ route('mahasiswa.home-index') }}" class="sidebar-link">
        <i class="fa-solid fa-home"></i>
        <span>Home</span>
    </a>
</li>

<li class="sidebar-item {{ Route::is('mahasiswa.home-profile*') ? 'active' : '' }}">
    <a href="{{ route('mahasiswa.home-profile') }}" class="sidebar-link">
        <i class="fa-solid fa-user-edit"></i>
        <span>Profile User</span>
    </a>
</li>

<li class="sidebar-title">Menu Akademik</li>

@if (Route::has('mahasiswa.home-jadkul-index'))
<li class="sidebar-item {{ Route::is('mahasiswa.home-jadkul-*') ? 'active' : '' }}">
    <a href="{{ route('mahasiswa.home-jadkul-index') }}" class="sidebar-link">
        <i class="fa-solid fa-calendar"></i>
        <span>Jadwal Kuliah</span>
    </a>
</li>
@endif

@if (Route::has('mahasiswa.krs.index'))
<li class="sidebar-item {{ Route::is('mahasiswa.krs.*') ? 'active' : '' }}">
    <a href="{{ route('mahasiswa.krs.index') }}" class="sidebar-link">
        <i class="fa-solid fa-file-alt"></i>
        <span>Kartu Rencana Studi (KRS)</span>
    </a>
</li>
@endif

@if (Route::has('mahasiswa.khs.index'))
<li class="sidebar-item {{ Route::is('mahasiswa.khs.*') ? 'active' : '' }}">
    <a href="{{ route('mahasiswa.khs.index') }}" class="sidebar-link">
        <i class="fa-solid fa-file-circle-check"></i>
        <span>Kartu Hasil Studi (KHS)</span>
    </a>
</li>
{{-- @endif --}}

{{-- @if (Route::has('mahasiswa.akademik.tugas-index'))
<li class="sidebar-item {{ Route::is('mahasiswa.akademik.tugas-*') ? 'active' : '' }}">
    <a href="{{ route('mahasiswa.akademik.tugas-index') }}" class="sidebar-link">
        <i class="fa-solid fa-list-check"></i>
        <span>Tugas Kuliah</span>
    </a>
</li> --}}
{{-- @endif

<li class="sidebar-title">Menu Finansial</li>

@if (Route::has('mahasiswa.home-tagihan-index'))
<li class="sidebar-item {{ Route::is('mahasiswa.home-tagihan-*') ? 'active' : '' }}">
    <a href="{{ route('mahasiswa.home-tagihan-index') }}" class="sidebar-link">
        <i class="fa-solid fa-file-invoice"></i>
        <span>Data Tagihan</span>
    </a>
</li>
@endif

<li class="sidebar-title">Menu Bantuan</li>

@if (Route::has('mahasiswa.support.ticket-index'))
<li class="sidebar-item has-sub {{ Route::is('mahasiswa.support.*') ? 'active' : '' }}">
    <a href="#" class="sidebar-link">
        <i class="fa-solid fa-ticket"></i>
        <span>Ticket Support</span>
    </a>
    <ul class="submenu">
        @if (Route::has('mahasiswa.support.ticket-index'))
        <li class="submenu-item {{ Route::is('mahasiswa.support.ticket-index', 'mahasiswa.support.ticket-view') ? 'active' : '' }}">
            <a href="{{ route('mahasiswa.support.ticket-index') }}" class="submenu-link">Lihat Ticket</a>
        </li>
        @endif

        @if (Route::has('mahasiswa.support.ticket-open'))
        <li class="submenu-item {{ Route::is('mahasiswa.support.ticket-open', 'mahasiswa.support.ticket-create') ? 'active' : '' }}">
            <a href="{{ route('mahasiswa.support.ticket-open') }}" class="submenu-link">Buka Ticket</a>
        </li>
        @endif
    </ul>
</li> --}}
@endif
