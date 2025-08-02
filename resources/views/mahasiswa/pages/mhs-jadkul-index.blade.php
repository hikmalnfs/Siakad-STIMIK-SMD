@extends('base.base-dash-index')

@section('title', 'Data Jadwal Kuliah - Siakad By Internal Developer')
@section('menu', 'Data Jadwal Kuliah')
@section('submenu', 'Data Jadwal Kuliah')
@section('urlmenu') {{ route('mahasiswa.home-jadkul-index') }} @endsection
@section('subdesc', 'Halaman untuk melihat Jadwal Kuliah')

@section('custom-css')
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }

    th, td {
        padding: 0.625em;
        text-align: center;
        border: 1px solid var(--bs-border-color, #ccc);
    }

    @media screen and (max-width: 600px) {
        table {
            border: 0;
        }

        thead {
            display: none;
        }

        tr {
            display: block;
            margin-bottom: 0.625em;
            border-bottom: 3px solid #ddd;
        }

        td {
            display: block;
            text-align: right;
            font-size: 0.8em;
            border-bottom: 1px solid #ddd;
        }

        td::before {
            content: attr(data-label);
            float: left;
            font-weight: bold;
            text-transform: uppercase;
        }

        td:last-child {
            border-bottom: 0;
        }
    }
</style>
@endsection

@section('content')
<section class="section">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">@yield('submenu')</h5>
            
            {{-- Tombol Rekap Global --}}
            <a href="{{ route('mahasiswa.home-jadkul-rekap') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-chart-bar"></i> Lihat Rekap Absensi
            </a>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-striped" id="table1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kelas</th>
                        <th>Nama Mata Kuliah</th>
                        <th>Dosen Pengajar</th>
                        <th>Metode</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Button</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadkul as $key => $item)
                    <tr>
                        <td data-label="No">{{ $key + 1 }}</td>
                        <td data-label="Nama Kelas">{{ $item->kelas->pluck('name')->implode(', ') ?: '-' }}</td>
                        <td data-label="Mata Kuliah">
                            {{ optional($item->mataKuliah)->name ?? '-' }}<br>
                            {{ $item->bsks }} SKS
                        </td>
                        <td data-label="Dosen">{{ optional($item->dosen)->name ?? '-' }}</td>
                        <td data-label="Metode">{{ $item->metode }}</td>
                        <td data-label="Lokasi">
                            {{ optional($item->ruang?->gedung)->name ?? '-' }}<br>
                            {{ optional($item->ruang)->name ?? '-' }} - Lantai {{ optional($item->ruang)->floor ?? '-' }}
                        </td>
                        <td data-label="Tanggal">
                            {{ \Carbon\Carbon::parse($item->date)->isoFormat('dddd') }}<br>
                            {{ \Carbon\Carbon::parse($item->date)->isoFormat('D MMMM Y') }}
                        </td>
                        <td data-label="Waktu">
                            @forelse ($item->waktuKuliah as $waktu)
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $waktu->time_start)->format('H.i') }} -
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $waktu->time_ended)->format('H.i') }} WIB<br>
                            @empty
                                <span>-</span>
                            @endforelse
                        </td>
                        <td data-label="Aksi">
                            <a href="{{ route('mahasiswa.home-jadkul-absen', $item->code) }}" class="btn btn-info btn-sm mb-1">
                                <i class="fas fa-calendar-check"></i> Absensi
                            </a>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#giveFeedBack{{ $item->id }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-star"></i> Feedback
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- MODAL FEEDBACK --}}
@foreach ($jadkul as $item)
<form action="{{ route('mahasiswa.jadkul.feedback-store', $item->id) }}" method="POST">
    @csrf
    <div class="modal fade" id="giveFeedBack{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header justify-content-between">
                    <h5 class="modal-title">Feedback - {{ optional($item->mataKuliah)->name ?? '-' }} P-{{ $item->raw_pert_id }}</h5>
                    <div>
                        <button type="submit" class="btn btn-outline-primary me-2" aria-label="Kirim feedback">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal" aria-label="Tutup modal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fb_score" class="form-label">Skor Feedback</label>
                        <select name="fb_score" id="fb_score" class="form-select" required>
                            <option value="">Pilih Salah Satu</option>
                            <option value="Tidak Puas">Tidak Puas</option>
                            <option value="Cukup Puas">Cukup Puas</option>
                            <option value="Sangat Puas">Sangat Puas</option>
                        </select>
                        @error('fb_score')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        <small class="text-muted">Feedback kamu bersifat anonim.</small>
                    </div>
                    <div class="mb-3">
                        <label for="fb_reason" class="form-label">Alasan</label>
                        <textarea name="fb_reason" id="fb_reason" class="form-control" rows="5" placeholder="Tulis alasanmu..."></textarea>
                        @error('fb_reason')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endforeach
@endsection
