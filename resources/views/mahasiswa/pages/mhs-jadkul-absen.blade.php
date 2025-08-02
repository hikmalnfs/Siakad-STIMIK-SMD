@extends('base.base-dash-index')

@section('menu') Absensi @endsection
@section('submenu') Form Absensi Mahasiswa @endsection
@section('urlmenu') {{ route('mahasiswa.home-jadkul-index') }} @endsection
@section('subdesc') Form kehadiran kuliah berdasarkan jadwal aktif dan status absensi yang dibuka oleh dosen. @endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        {{-- Alert Section --}}
        @php $alertTypes = ['success', 'error', 'warning', 'info']; @endphp
        @foreach ($alertTypes as $type)
            @if (session($type))
                <div class="alert alert-{{ $type }} alert-dismissible fade show shadow-sm" role="alert">
                    {!! session($type) !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endforeach

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Form Absensi --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                Form Absensi Kuliah
            </div>
            <div class="card-body">
                <form action="{{ route('mahasiswa.home-jadkul-absen-store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Kode Jadwal --}}
                    <div class="mb-3">
                        <label class="form-label">Kode Jadwal Kuliah</label>
                        <input type="text" name="jadkul_code" value="{{ $jadkul->code }}" class="form-control" readonly>
                        <small class="text-muted">{{ $jadkul->mataKuliah->name ?? '-' }}</small>
                    </div>

                    {{-- Mahasiswa --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Mahasiswa</label>
                        <input type="text" value="{{ Auth::guard('mahasiswa')->user()->name }}" class="form-control" readonly>
                        <input type="hidden" name="mahasiswa_id" value="{{ Auth::guard('mahasiswa')->user()->id }}">
                    </div>

                    {{-- Pertemuan Ke --}}
                    <div class="mb-3">
                        <label class="form-label">Pertemuan Ke</label>
                        <select name="pertemuan" class="form-control @error('pertemuan') is-invalid @enderror" required>
                            <option value="">-- Pilih Pertemuan --</option>
                            @foreach ($absensiStatus as $pertemuanKe)
                                @php
                                    $sudahAbsen = \App\Models\AbsensiMahasiswa::where('jadkul_code', $jadkul->code)
                                        ->where('author_id', Auth::guard('mahasiswa')->id())
                                        ->where('pertemuan', $pertemuanKe->pertemuan)
                                        ->exists();
                                @endphp
                                @if (!$sudahAbsen)
                                    <option value="{{ $pertemuanKe->pertemuan }}">Pertemuan ke-{{ $pertemuanKe->pertemuan }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('pertemuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($absensiStatus->isEmpty())
                            <small class="text-danger">Tidak ada pertemuan yang tersedia untuk absensi.</small>
                        @endif
                    </div>

                    {{-- Jenis Kehadiran --}}
                    <div class="mb-3">
                        <label class="form-label">Jenis Kehadiran</label>
                        <select name="absen_type" class="form-control @error('absen_type') is-invalid @enderror" required>
                            <option value="">-- Pilih Kehadiran --</option>
                            <option value="H">Hadir</option>
                            <option value="I">Izin</option>
                            <option value="S">Sakit</option>
                            <option value="A">Alpa</option>
                        </select>
                        @error('absen_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Bukti Absensi --}}
                    <div class="mb-3">
                        <label class="form-label">Upload Bukti (Foto / Surat)</label>
                        <input type="file" name="absen_proof" class="form-control @error('absen_proof') is-invalid @enderror" accept="image/*,application/pdf">
                        @error('absen_proof')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Catatan</label>
                        <textarea name="absen_desc" rows="3" class="form-control @error('absen_desc') is-invalid @enderror" placeholder="Contoh: Hadir sesuai jadwal atau Izin karena sakit.">{{ old('absen_desc') }}</textarea>
                        @error('absen_desc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn btn-primary w-100">
                        Kirim Absensi
                    </button>
                </form>

                <div class="mt-3">
                    <a href="{{ route('mahasiswa.home-jadkul-index') }}" class="btn btn-secondary w-100">
                        ← Kembali ke Jadwal Kuliah
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
