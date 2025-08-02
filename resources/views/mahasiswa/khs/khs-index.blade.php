@extends('base.base-dash-index')

@section('title', 'Kartu Hasil Studi')

@section('content')
<div class="container">
    <h4 class="mb-3">Kartu Hasil Studi (KHS)</h4>
        <form method="GET" action="">
            <div class="d-flex flex-wrap align-items-end gap-2 mb-4">
                {{-- Filter Semester --}}
                <div>
                    <label for="semester" class="form-label mb-1">Filter Semester</label>
                    <select name="semester" id="semester" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Semester</option>
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>
                                Semester {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Tombol PDF --}}
                <div class="mt-auto">
                    <label class="form-label d-block invisible">Download</label>
                    <a href="{{ route('mahasiswa.khs.pdf', ['semester' => request('semester')]) }}" target="_blank"
                        class="btn btn-danger">
                        <i class="fas fa-file-pdf me-1"></i> Download PDF
                    </a>
                </div>
            </div>
        </form>


    @php
        $ipk_total_bobot = 0;
        $ipk_total_sks = 0;
    @endphp

    @forelse ($nilai->groupBy('jadwalKuliah.tahunAkademik.name') as $tahun => $itemGroup)
        @php
            $totalSks = 0;
            $totalBobot = 0;

            foreach ($itemGroup as $item) {
                $matkul = optional($item->jadwalKuliah->mataKuliah);
                $sks = $matkul->bsks ?? 0;
                $bobot = $item->bobot ?? 0;

                $totalSks += $sks;
                $totalBobot += ($sks * $bobot);

                $ipk_total_sks += $sks;
                $ipk_total_bobot += ($sks * $bobot);
            }

            $ips = $totalSks > 0 ? number_format($totalBobot / $totalSks, 2) : '-';
        @endphp

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <strong>Semester :
                    {{ request('semester') ? request('semester') : 'Semua' }}
                </strong>
                <strong>Tahun Akademik : {{ $tahun }}</strong>
                <strong>IPS: {{ $ips }}</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped mb-0 align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 15%">Kode</th>
                                <th>Nama Mata Kuliah</th>
                                <th style="width: 15%">Nilai Angka</th>
                                <th style="width: 15%">Nilai Huruf</th>
                                <th style="width: 10%">SKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($itemGroup as $item)
                                @php
                                    $jadwal = $item->jadwalKuliah;
                                    $matkul = $jadwal->mataKuliah ?? null;
                                @endphp
                                @if ($matkul)
                                <tr>
                                    <td class="text-center">{{ $matkul->code }}</td>
                                    <td>{{ $matkul->name }}</td>
                                    <td class="text-center">{{ $item->nilai_akhir ?? '-' }}</td>
                                    <td class="text-center">{{ $item->nilai_huruf ?? '-' }}</td>
                                    <td class="text-center">{{ $matkul->bsks ?? '-' }}</td>
                                </tr>
                                @else
                                <tr>
                                    <td colspan="5" class="text-center text-danger">Data Mata Kuliah tidak ditemukan</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-center">Total SKS</th>
                                <th class="text-center">{{ $totalSks }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            <i class="fa fa-info-circle me-1"></i> Belum ada data nilai yang tersedia.
        </div>
    @endforelse

    @php
        $ipk = $ipk_total_sks > 0 ? number_format($ipk_total_bobot / $ipk_total_sks, 2) : '-';
    @endphp

    @if ($nilai->count())
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-0">Indeks Prestasi Kumulatif (IPK): <strong>{{ $ipk }}</strong></h5>
        </div>
    </div>
    @endif

    <hr class="mt-5 mb-3">
        <h5>Keterangan Nilai</h5>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Nilai Huruf</th>
                            <th>Rentang Angka</th>
                            <th>Bobot</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>A+</td><td>93 - 100</td><td>4.00</td><td>Sangat Baik Sekali</td></tr>
                        <tr><td>A</td><td>90 - 92</td><td>4.00</td><td>Sangat Baik</td></tr>
                        <tr><td>A-</td><td>87 - 89</td><td>3.70</td><td>Baik Sekali</td></tr>
                        <tr><td>B+</td><td>83 - 86</td><td>3.30</td><td>Baik</td></tr>
                        <tr><td>B</td><td>80 - 82</td><td>3.00</td><td>Cukup Baik</td></tr>
                        <tr><td>B-</td><td>77 - 79</td><td>2.70</td><td>Cukup</td></tr>
                        <tr><td>C+</td><td>73 - 76</td><td>2.30</td><td>Sedang</td></tr>
                        <tr><td>C</td><td>70 - 72</td><td>2.00</td><td>Kurang</td></tr>
                        <tr><td>C-</td><td>67 - 69</td><td>1.70</td><td>Kurang Sekali</td></tr>
                        <tr><td>D+</td><td>63 - 66</td><td>1.30</td><td>Hampir Gagal</td></tr>
                        <tr><td>D</td><td>60 - 62</td><td>1.00</td><td>Tidak Lulus</td></tr>
                        <tr><td>E</td><td>&lt; 60</td><td>0.00</td><td>Gagal</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-6">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Istilah</th>
                            <th>Penjelasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>IPS</strong></td>
                            <td>Indeks Prestasi Semester – rata-rata nilai pada satu semester</td>
                        </tr>
                        <tr>
                            <td><strong>IPK</strong></td>
                            <td>Indeks Prestasi Kumulatif – rata-rata dari seluruh semester</td>
                        </tr>
                        <tr>
                            <td><strong>Rumus IPS</strong></td>
                            <td><code>IPS = Total (Bobot × SKS) ÷ Total SKS</code></td>
                        </tr>
                        <tr>
                            <td><strong>Rumus IPK</strong></td>
                            <td><code>IPK = Total Bobot Seluruh Semester ÷ Total SKS</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </hr>
</div>
@endsection
