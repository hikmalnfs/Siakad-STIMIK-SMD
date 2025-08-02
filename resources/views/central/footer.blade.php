
    <!-- BEGIN FOOTER -->
    <footer class="footer footer-transparent pt-5 mt-auto border-top">
        <div class="container pb-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h3 class="h4 mb-4">{{ $webs->school_name }}</h3>
                    <p class="text-muted">{{ $webs->school_desc }}</p>
                    <div class="social-links mt-4 d-flex gap-2">
                        <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-facebook" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                            </svg>
                        </a>
                        <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-instagram" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M4 4m0 4a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4z" />
                                <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                <path d="M16.5 7.5l0 .01" />
                            </svg>
                        </a>
                        <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-youtube" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M3 5m0 4a4 4 0 0 1 4 -4h10a4 4 0 0 1 4 4v6a4 4 0 0 1 -4 4h-10a4 4 0 0 1 -4 -4z" />
                                <path d="M10 9l5 3l-5 3z" />
                            </svg>
                        </a>
                        <a href="#" class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path d="M4 4l11.733 16h4.267l-11.733 -16z" />
                                <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <h4 class="h5 mb-4">Link Cepat</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="link-secondary">Tentang Kami</a></li>
                        <li class="mb-2"><a href="#" class="link-secondary">Program Studi</a></li>
                        <li class="mb-2"><a href="#" class="link-secondary">Pendaftaran</a></li>
                        <li class="mb-2"><a href="#" class="link-secondary">Beasiswa</a></li>
                        <li class="mb-2"><a href="#" class="link-secondary">Kalender Akademik</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <h4 class="h5 mb-4">Mahasiswa</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="link-secondary">SIAKAD</a></li>
                        <li class="mb-2"><a href="#" class="link-secondary">E-Learning</a></li>
                        <li class="mb-2"><a href="#" class="link-secondary">Perpustakaan</a></li>
                        <li class="mb-2"><a href="#" class="link-secondary">Laboratorium</a></li>
                        <li class="mb-2"><a href="#" class="link-secondary">Kemahasiswaan</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h4 class="h5 mb-4">Kontak</h4>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                            </svg>
                            <span class="text-muted">{{ $webs->school_address }}</span>
                        </li>
                        <li class="mb-3 d-flex gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-phone" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path>
                            </svg>
                            <span class="text-muted">{{ $webs->school_phone }}</span>
                        </li>
                        <li class="mb-3 d-flex gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path>
                                <path d="M3 7l9 6l9 -6"></path>
                            </svg>
                            <span class="text-muted">{{ $webs->school_email }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="border-top">
            <div class="container py-4">
                <div class="row align-items-center">
                    <div class="col-lg-8 text-lg-start text-center">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">Copyright © {{ \Carbon\Carbon::now()->translatedFormat('F Y') }} <a href="." class="link-secondary">{{ $webs->school_apps }} - {{ $webs->school_name }} </a>. All rights reserved.</li>
                            <li class="list-inline-item"><a href="#" class="link-secondary">Kebijakan Privasi</a></li>
                            <li class="list-inline-item"><a href="#" class="link-secondary">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end text-center">
                        <img src="{{ asset('logo.png') }}" style="height: 200px; width:auto;" alt="STIMIK Logo">
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- END FOOTER -->