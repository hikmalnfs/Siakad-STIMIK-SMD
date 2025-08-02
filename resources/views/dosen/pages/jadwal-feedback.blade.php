@extends('base.base-dash-index')

@section('menu')
    FeedBack Perkuliahan
@endsection

@section('submenu')
    Lihat Semua FeedBack
@endsection

@section('urlmenu')
    {{ route('dosen.akademik.jadwal-index') }}
@endsection

@section('subdesc')
    Halaman untuk melihat semua feedback
@endsection

@section('title')
    @yield('submenu') - @yield('menu') - Siakad By Internal Developer
@endsection

@section('content')
<section class="content">
    <div class="row">
        {{-- Grafik Chart --}}
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

        {{-- Tabel Feedback --}}
        <div class="col-lg-8 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title">@yield('menu') - @yield('submenu')</h4>
                    <a href="@yield('urlmenu')" class="btn btn-warning">
                        <i class="fa-solid fa-backward"></i>
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>Skor FeedBack</th>
                                <th>Alasan FeedBack</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($feedback as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">{{ $item->fb_score }}</td>
                                    <td>{{ $item->fb_reason }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada feedback tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('custom-js')
<script src="{{ asset('dist/assets/extensions/apexcharts/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ajaxRunning = false;

        function fetchData() {
            if (ajaxRunning) return;

            $.ajax({
                url: '{{ route('dosen.services.ajax.graphic.kepuasan-mengajar', $code ?? '') }}',
                method: 'GET',
                success: function (response) {
                    let tidakPuasCount = response.tidakpuas ?? 0;
                    let cukupPuasCount = response.cukuppuas ?? 0;
                    let sangatPuasCount = response.sangatpuas ?? 0;

                    const chart = new ApexCharts(document.querySelector('#grafikChart'), {
                        chart: { type: 'pie' },
                        series: [tidakPuasCount, cukupPuasCount, sangatPuasCount],
                        labels: ['Tidak Puas', 'Cukup Puas', 'Sangat Puas'],
                        legend: { position: 'bottom' }
                    });

                    chart.render();
                },
                error: function (xhr, status, error) {
                    console.error("Gagal memuat data grafik:", error);
                }
            });
        }

        fetchData();
    });
</script>
@endsection
