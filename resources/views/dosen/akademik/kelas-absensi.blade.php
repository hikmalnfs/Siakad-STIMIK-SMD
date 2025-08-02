@extends('base.base-dash-index')

@section('menu') Kelas @endsection
@section('submenu') Absensi Mahasiswa @endsection
@section('urlmenu') {{ route('dosen.akademik.kelas-index') }} @endsection
@section('subdesc') Halaman untuk mengelola absensi mahasiswa selama 1 semester. @endsection
@section('title') @yield('submenu') - @yield('menu') - Siakad By Internal Developer @endsection

@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">

                {{-- Header --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Absensi - Kelas: {{ $jadwal->code ?? 'Kode tidak tersedia' }} - {{ $jadwal->matkul->nama ?? 'Mata Kuliah tidak ditemukan' }}
                    </h5>
                    <a href="@yield('urlmenu')" class="btn btn-warning">
                        <i class="fa-solid fa-backward"></i> Kembali
                    </a>
                </div>

                <div class="card-body">

                    {{-- Notifikasi --}}
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @elseif (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    {{-- Tabel Kontrol Buka/Tutup Pertemuan --}}
                    <div class="table-responsive absensi-scroll mb-4">
                        <table class="table table-bordered table-sm text-center align-middle" id="controlTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Pertemuan</th>
                                    @for ($i = 1; $i <= 16; $i++)
                                        <th style="min-width: 100px;">{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Status</td>
                                    @for ($i = 1; $i <= 16; $i++)
                                        @php
                                            $status = $absensiStatus->firstWhere('pertemuan', $i);
                                            $isOpen = $status && $status->is_active;
                                        @endphp
                                        <td>
                                            <span class="badge bg-{{ $isOpen ? 'success' : 'secondary' }}">
                                                {{ $isOpen ? 'Terbuka' : 'Tertutup' }}
                                            </span>
                                        </td>
                                    @endfor
                                </tr>
                                <tr>
                                    <td>Aksi</td>
                                    @for ($i = 1; $i <= 16; $i++)
                                        @php
                                            $status = $absensiStatus->firstWhere('pertemuan', $i);
                                            $isOpen = $status && $status->is_active;
                                        @endphp
                                        <td>
                                            <form action="{{ route('dosen.akademik.kelas-absensi-toggle', [$jadwal->id, $i]) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $isOpen ? 0 : 1 }}">
                                                <button type="submit" class="btn btn-sm {{ $isOpen ? 'btn-danger' : 'btn-primary' }}">
                                                    {{ $isOpen ? 'Tutup' : 'Buka' }}
                                                </button>
                                            </form>
                                        </td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Form Absensi Mahasiswa --}}
                    <form action="{{ route('dosen.akademik.kelas-save-absensi', $jadwal->id) }}" method="POST">
                        @csrf
                        <div class="table-responsive absensi-scroll">
                            <table class="table table-bordered table-sm mb-0" id="absensiTable">
                                <thead class="text-center bg-light align-middle">
                                    <tr>
                                        <th>#</th>
                                        <th>NIM</th>
                                        <th>Nama Mahasiswa</th>
                                        @for ($i = 1; $i <= 16; $i++)
                                            <th style="min-width: 90px;">P{{ $i }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mahasiswas ?? [] as $index => $mhs)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $mhs->numb_nim ?? '-' }}</td>
                                            <td class="text-start">{{ $mhs->mhs_name ?? $mhs->name ?? '-' }}</td>
                                            @for ($i = 1; $i <= 16; $i++)
                                                @php
                                                    $value = $absensiData[$mhs->id][$i] ?? null;
                                                    $statusPertemuan = $absensiStatus->firstWhere('pertemuan', $i);
                                                    $isOpen = $statusPertemuan && $statusPertemuan->is_active;
                                                    $mandiri = \App\Models\AbsensiMahasiswa::where('jadkul_code', $jadwal->code)
                                                        ->where('author_id', $mhs->id)
                                                        ->where('pertemuan', $i)
                                                        ->first();
                                                @endphp
                                                <td class="text-center align-middle">
                                                    @if ($isOpen)
                                                        <select name="absensi[{{ $mhs->id }}][{{ $i }}]" class="form-select form-select-sm">
                                                            <option value="" {{ $value === null ? 'selected' : '' }}>Belum Absen</option>
                                                            <option value="H" {{ $value === 'H' ? 'selected' : '' }}>H</option>
                                                            <option value="I" {{ $value === 'I' ? 'selected' : '' }}>I</option>
                                                            <option value="S" {{ $value === 'S' ? 'selected' : '' }}>S</option>
                                                            <option value="A" {{ $value === 'A' ? 'selected' : '' }}>A</option>
                                                        </select>
                                                        @if ($mandiri)
                                                            <div class="mt-1">
                                                                <span class="badge bg-{{ match($mandiri->absen_type) {
                                                                    'H' => 'success',
                                                                    'I' => 'info',
                                                                    'S' => 'warning',
                                                                    'A' => 'danger',
                                                                    default => 'secondary',
                                                                } }}">
                                                                    Mandiri: {{ $mandiri->absen_type }}
                                                                </span>
                                                                @if ($mandiri->absen_desc)
                                                                    <div class="small text-muted">"{{ $mandiri->absen_desc }}"</div>
                                                                @endif
                                                                @if ($mandiri->absen_proof)
                                                                    <div><a href="{{ asset('storage/' . $mandiri->absen_proof) }}" target="_blank" class="small">📎 Bukti</a></div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @else
                                                        @if ($mandiri)
                                                            <span class="badge bg-{{ match($mandiri->absen_type) {
                                                                'H' => 'success',
                                                                'I' => 'info',
                                                                'S' => 'warning',
                                                                'A' => 'danger',
                                                                default => 'secondary',
                                                            } }}">
                                                                {{ $mandiri->absen_type }}
                                                            </span>
                                                            @if ($mandiri->absen_desc)
                                                                <div class="small text-muted">"{{ $mandiri->absen_desc }}"</div>
                                                            @endif
                                                            @if ($mandiri->absen_proof)
                                                                <div><a href="{{ asset('storage/' . $mandiri->absen_proof) }}" target="_blank" class="small">📎 Bukti</a></div>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary">-</span>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="19" class="text-center">Tidak ada mahasiswa terdaftar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">Simpan Absensi</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- CSS Fix Layout --}}
<style>
    .absensi-scroll {
        overflow-x: auto;
        width: 100%;
    }

    #controlTable, #absensiTable {
        min-width: 1600px;
        width: max-content;
        table-layout: fixed;
        border-collapse: collapse;
    }

    th, td {
        white-space: nowrap;
        vertical-align: middle;
        text-align: center;
        padding: 6px 10px;
        font-size: 14px;
    }

    td select {
        width: 100%;
    }

    .btn-sm {
        padding: 2px 8px;
        font-size: 12px;
    }

    .table thead th {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 13px;
        padding: 6px 10px;
        display: inline-block;
        min-width: 60px;
    }
</style>
@endsection
