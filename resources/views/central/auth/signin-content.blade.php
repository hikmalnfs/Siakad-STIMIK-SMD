<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SISTEM INFORMASI AKADEMIK || STIMIK SAMARINDA</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('sipenmaru/images/logo.png') }}">
    <link href="{{ asset('sipenmaru/vendor/login/style.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
</head>
<body>
@include('sweetalert::alert')

<!-- Tambahkan class sign-in-mode agar langsung ke panel kanan -->
<div class="container sign-up-mode">
    <div class="forms-container">
        <div class="signin-signup">
            {{-- FORM LOGIN --}}
            <form method="POST" action="{{ route('auth.handle-signin') }}" class="sign-in-form">
                @csrf
                <h2 class="title">Masuk</h2>

                @if(session('loginError'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Peringatan!</strong> {{ session('loginError') }}
                    </div>
                @endif

                {{-- Username / Email --}}
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input 
                        type="text" 
                        name="login" 
                        placeholder="Email / Username" 
                        value="{{ old('login') }}" 
                        autocomplete="on"
                    />
                </div>
                @error('login')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                {{-- Password --}}
                <div class="input-field" style="position: relative;">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        name="password" 
                        id="passwordInput" 
                        placeholder="Password" 
                        autocomplete="off" 
                        style="padding-right: 40px;" 
                    />
                    <span class="toggle-password" 
                        style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor:pointer;">
                        <i id="toggleIcon" class="fa fa-eye"></i>
                    </span>
                </div>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                {{-- CAPTCHA --}}
                @if($webs->enable_captcha)
                    <div class="my-2">
                        <x-turnstile-widget theme="auto" language="id"/>
                        @error('cf-turnstile-response')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                @endif

                {{-- Tombol Submit --}}
                <input type="submit" value="MASUK" class="btn solid" />
                <br>
            </form>

            {{-- FORM REGISTER (dummy info) --}}
            <form class="sign-up-form">
                <h2 class="title">SISTEM INFORMASI AKADEMIK STIMIK SAMARINDA</h2>
                <br>
            </form>
        </div>
    </div>

    {{-- PANEL ANIMASI --}}
    <div class="panels-container">
        <div class="panel left-panel">
            <div class="content">
                <h3>Selamat Datang di</h3>
                <p>SISTEM INFORMASI AKADEMIK STIMIK SAMARINDA</p>
                <button class="btn transparent" id="sign-up-btn">Kembali</button>
            </div>
            <img src="{{ asset('sipenmaru/images/LOGO-STIMIK.PNG') }}" class="image" alt="Logo" />
        </div>

        <div class="panel right-panel">
            <div class="content">
                <h3>Salah satu diantara kami?</h3>
                <p>Silahkan masuk jika anda telah memiliki akun.</p>
                <button class="btn transparent" id="sign-in-btn">Masuk</button>
            </div>
            <img src="{{ asset('sipenmaru/images/register.svg') }}" class="image" alt="Register" />
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="{{ asset('sipenmaru/vendor/login/app.js') }}"></script>
<script src="{{ asset('sipenmaru/js/styleSwitcher.js') }}"></script>
<script>
    document.querySelector('.toggle-password').addEventListener('click', function () {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('toggleIcon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
</body>
</html>
