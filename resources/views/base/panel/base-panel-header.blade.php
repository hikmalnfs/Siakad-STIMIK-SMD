@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Notification;
    use App\Models\TicketSupport;

    $notif = Notification::latest()->paginate(6);
    $ticket = [];

    if (Auth::guard('dosen')->check()) {
        $user = Auth::guard('dosen')->user();
        $prefix = 'dosen.';
        $photo = $user->photo ?? 'default.jpg';
        $name = $user->name;
        $status = $user->dsn_stat ?? '-';
        $idcode = $user->numb_nidn ?? '-';
        $ticket = TicketSupport::latest()->paginate(6); // bisa disesuaikan jika ada dept_id dosen
    } elseif (Auth::guard('mahasiswa')->check()) {
        $user = Auth::guard('mahasiswa')->user();
        $prefix = 'mahasiswa.';
        $photo = $user->photo ?? 'default.jpg';
        $name = $user->name;
        $status = $user->mhs_stat ?? '-';
        $idcode = $user->numb_nim ?? '-';
        $ticket = TicketSupport::where('users_id', $user->id)->latest()->paginate(6);
    } elseif (Auth::check()) {
        $user = Auth::user();
        $prefix = '';
        $photo = $user->photo ?? 'default.jpg';
        $name = $user->name ?? $user->username ?? 'User';
        $status = $user->type ?? '-';
        $idcode = $user->username ?? '-';
        $ticket = TicketSupport::when($user->raw_type != '0', fn($q) => $q->where('dept_id', $user->raw_type))->latest()->paginate(6);
    } else {
        $name = 'Guest';
        $photo = 'default.jpg';
        $status = '-';
        $idcode = '-';
        $prefix = '';
    }
@endphp

<nav class="navbar navbar-expand navbar-light navbar-top">
    <div class="container-fluid">
        <a href="#" class="burger-btn d-block">
            <i class="bi bi-justify fs-3"></i>
        </a>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-lg-0">

                {{-- Ticket Support --}}
                @auth
                <li class="nav-item dropdown me-1">
                    <a class="nav-link dropdown-toggle text-gray-600" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-envelope bi-sub fs-4"></i>
                        <span class="badge badge-notification bg-danger">{{ $ticket->count() }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-xl">
                        <li class="dropdown-header"><h6>Ticket Support</h6></li>
                        @forelse ($ticket as $item)
                            <li><a class="dropdown-item" href="#">{{ '#' . $item->code . ' - ' . $item->subject }}</a></li>
                        @empty
                            <li class="dropdown-item text-muted">Tidak ada tiket</li>
                        @endforelse
                    </ul>
                </li>
                @endauth

                {{-- Notification --}}
                <li class="nav-item dropdown me-3">
                    <a class="nav-link dropdown-toggle text-gray-600" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell bi-sub fs-4"></i>
                        <span class="badge badge-notification bg-danger">{{ $notif->count() }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-xl">
                        <li class="dropdown-header"><h6>Notifications</h6></li>
                        @forelse ($notif as $item)
                            <li class="dropdown-item notification-item">
                                <a class="d-flex align-items-center" href="#">
                                    <div class="notification-icon bg-primary"><i class="fa-solid fa-bell"></i></div>
                                    <div class="notification-text ms-4">
                                        <p class="notification-title font-bold">{{ $item->name }}</p>
                                        <p class="notification-subtitle text-sm">{{ Str::limit(strip_tags($item->desc), 25) }}</p>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="dropdown-item text-muted">Tidak ada notifikasi</li>
                        @endforelse
                        <li><p class="text-center py-2 mb-0"><a href="#">Lihat semua notifikasi</a></p></li>
                    </ul>
                </li>
            </ul>

            {{-- User Dropdown --}}
            <div class="dropdown">
                <a href="#" data-bs-toggle="dropdown">
                    <div class="user-menu d-flex">
                        <div class="user-name text-end me-3">
                            <h6 class="mb-0 text-gray-600">{{ $name }}</h6>
                            <p class="mb-0 text-sm text-gray-600">{{ $status }}</p>
                        </div>
                        <div class="user-img d-flex align-items-center">
                            <div class="avatar avatar-md">
                                <img src="{{ asset('storage/images/' . $photo) }}" style="object-fit: cover !important;">
                            </div>
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Hello, {{ $idcode }}</h6></li>
                    <li><a class="dropdown-item" href="{{ route($prefix . 'home-profile') }}"><i class="bi bi-person me-2"></i> My Profile</a></li>
                    <hr class="dropdown-divider">
                    <li><a class="dropdown-item" href="{{ route($prefix . 'auth-signout-post') }}"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
