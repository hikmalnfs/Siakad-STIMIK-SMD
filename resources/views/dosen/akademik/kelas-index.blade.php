@extends('base.base-dash-index')

@section('menu')
    Akademik
@endsection

@section('submenu')
    Kelas Mengajar
@endsection

@section('urlmenu')
    {{ route('dosen.akademik.kelas-index') }}
@endsection

@section('subdesc')
    Halaman daftar kelas yang diampu dosen
@endsection

@section('title')
    @yield('submenu') - @yield('menu') - Siakad By Internal Developer
@endsection

@section('content')
    <section class="content">
        <div class="row mb-3">
            <div class="col-md-6">
                <form method="GET" action="{{ route('dosen.akademik.kelas-index') }}">
                    <div class="form-inline d-flex">
                        <select name="tahun_akademik_id" class="form-control me-2">
                            <option value="">Semua Tahun Akademik</option>
                            @foreach ($tahunAkademikList as $ta)
                                <option value="{{ $ta->id }}"
                                    {{ request('tahun_akademik_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="semester" class="form-control me-2">
                            <option value="">Semua Semester</option>
                            <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">@yield('menu') - @yield('submenu')</h5>
            </div>
            @php
                $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

                // Ambil slot jam unik berdasarkan kombinasi jam_mulai & jam_selesai
                $jamSlotList = $jadwals
                    ->map(
                        fn($j) => [
                            'mulai' => $j->jam_mulai,
                            'selesai' => $j->jam_selesai,
                            'label' =>
                                \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') .
                                ' - ' .
                                \Carbon\Carbon::parse($j->jam_selesai)->format('H:i'),
                        ],
                    )
                    ->unique('label')
                    ->sortBy('mulai')
                    ->values();
            @endphp

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <strong>Jadwal Mengajar Mingguan (Semua Semester)</strong>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 160px;">Jam</th>
                                @foreach ($hariList as $hari)
                                    <th>{{ $hari }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jamSlotList as $slot)
                                <tr>
                                    <td style="vertical-align: middle;"><strong>{{ $slot['label'] }}</strong></td>
                                    @foreach ($hariList as $hari)
                                        @php
                                            $jadwalItems = $jadwals->filter(
                                                fn($item) => $item->hari === $hari &&
                                                    $item->jam_mulai === $slot['mulai'] &&
                                                    $item->jam_selesai === $slot['selesai'],
                                            );
                                        @endphp
                                        <td class="text-start" style="vertical-align: top !important;">
                                            @if ($jadwalItems->count())
                                                @foreach ($jadwalItems as $jadwal)
                                                    <div
                                                        style="margin-bottom: 0.5rem; padding: 0.5rem; border-radius: 6px; background:#f8f9fa; border:1px solid #ddd;">
                                                        <div><strong>{{ $jadwal->mataKuliah->name ?? '-' }}</strong></div>
                                                        <div>Kelas: {{ $jadwal->kelas->pluck('name')->join(', ') }}</div>
                                                        <div>Semester: {{ $jadwal->mataKuliah->semester ?? '-' }}</div>
                                                        <div>Ruang: {{ $jadwal->ruang->name ?? '-' }}</div>
                                                        <div>Jumlah Mhs:
                                                            {{ $jadwal->kelas->sum(fn($k) => $k->mahasiswas->count()) }}
                                                        </div>
                                                        <a href="{{ route('dosen.akademik.kelas-view-absensi', $jadwal->id) }}"
                                                            class="btn btn-sm btn-warning mt-1">Kelola Absensi</a>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-muted">-</div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($hariList) + 1 }}" class="text-center text-muted">Tidak ada
                                        jadwal tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
