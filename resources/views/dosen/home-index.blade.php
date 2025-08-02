@extends('base.base-dash-index')

@section('title', 'SIAKAD PT - Internal Developer')
@section('menu', 'Dashboard')
@section('submenu', 'Beranda Dosen')
@section('urlmenu', '#')
@section('subdesc', 'Halaman utama informasi dosen')

@section('custom-css')
<style>
    @media (max-width: 768px) {
        .card-body {
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .icon {
            margin: 10px 0;
        }

        .text-white {
            margin-left: 0px !important;
            margin-top: 10px;
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('content')
@php
    $isWali = session('is_dosen_wali', false);
    $isPengampu = session('is_dosen_pengampu', false);
@endphp

<section class="section">
    <div class="row">
        <div class="col-lg-9 col-12 row">
            <div class="col-lg-3 col-6 mb-2">
                <a href="{{ route('dosen.akademik.jadwal-index') }}">
                    <div class="card btn btn-outline-success">
                        <div class="card-body d-flex justify-content-around align-items-center">
                            <span class="icon" style="margin-right: 25px;">
                                <i class="fa-solid fa-calendar" style="font-size: 42px"></i>
                            </span>
                            <span class="text-blue" style="margin-left: 25px; font-size: 16px;">
                                Jadwal Mengajar <br>{{ $jadwal_count }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-6 mb-2">
                <a href="{{ route('dosen.akademik.jadwal-index') }}">
                    <div class="card btn btn-outline-success">
                        <div class="card-body d-flex justify-content-around align-items-center">
                            <span class="icon" style="margin-right: 25px;">
                                <i class="fa-solid fa-star" style="font-size: 42px"></i>
                            </span>
                            <span class="text-blue" style="margin-left: 25px; font-size: 16px;">
                                FeedBack <br>{{ $feedback->count() }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            @if($isWali || $isPengampu)
            <div class="col-lg-3 col-6 mb-2">
                <a href="{{ route('dosen.pengajuan-krs.index') }}">
                    <div class="card btn btn-outline-success">
                        <div class="card-body d-flex justify-content-around align-items-center">
                            <span class="icon" style="margin-right: 25px;">
                                <i class="fa-solid fa-file-signature" style="font-size: 42px"></i>
                            </span>
                            <span class="text-white" style="margin-left: 25px; font-size: 16px;">
                                Pengajuan KRS
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        </div>

        <div class="col-lg-3 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pengumuman - {{ now()->format('d M Y') }}</h4>
                </div>
                <div class="card-body">
                    @forelse ($pengumuman as $item)
                        <span>
                            {{ $item->created_at->format('d-m-Y - H.i') }} -
                            <a href="#" data-bs-toggle="modal" data-bs-target="#updateFakultas{{ $item->code }}">
                                {{ $item->name }}
                            </a>
                        </span><br>
                    @empty
                        <span class="">Tidak Ada Pengumuman Hari Ini</span>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center">Presentasi Kepuasan Mengajar</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div id="grafikChart"></div>
                    </div>
                    <div class="text-center">
                        <small>Grafik Presentasi Kepuasan Mengajar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Notifikasi -->
    <div class="me-1 mb-1 d-inline-block">
        @foreach ($pengumuman as $item)
        <div class="modal fade text-left w-100" id="updateFakultas{{ $item->code }}" tabindex="-1"
            role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-l" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel16">Notifikasi - {{ $item->name }}</h4>
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-center"><b>{{ $item->name }}</b></p>
                        <p>{!! $item->desc !!}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection

@section('custom-js')
<script src="{{ asset('dist/assets/extensions/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('dist/assets/static/js/pages/dashboard.js') }}"></script>
<script>
    $(document).ready(function () {
        var ajaxRunning = false;

        function fetchData() {
            if (ajaxRunning) return;
            ajaxRunning = true;

            $.ajax({
                url: '{{ route('dosen.services.ajax.graphic.kepuasan-mengajar-dosen') }}',
                method: 'GET',
                success: function (response) {
                    var options = {
                        chart: { type: 'pie' },
                        series: [response.tidakpuas, response.cukuppuas, response.sangatpuas],
                        labels: ['Tidak Puas', 'Cukup Puas', 'Sangat Puas'],
                        legend: { position: 'bottom' }
                    };
                    new ApexCharts(document.querySelector('#grafikChart'), options).render();
                },
                error: function (xhr, status, error) {
                    console.error(error);
                },
                complete: function () {
                    ajaxRunning = false;
                }
            });
        }

        fetchData();
    });
</script>
@endsection
