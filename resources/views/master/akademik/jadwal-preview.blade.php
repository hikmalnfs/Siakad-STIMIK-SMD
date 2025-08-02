@extends('base.base-dash-index')

@section('title', 'Preview Jadwal Otomatis')
@section('menu', 'Jadwal')
@section('submenu', 'Preview')
@section('subdesc', 'Pratinjau hasil generate jadwal kuliah otomatis.')
@php
    $prefix = $prefix ?? '';
@endphp
@section('content')
<div class="container mt-4">
    <div class="card shadow-sm rounded-3 border border-success">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i> Preview Jadwal Perkuliahan</h4>
            <span class="badge bg-light text-success">Total Jadwal: {{ count($jadwalPreview) }}</span>
        </div>

        <div class="card-body table-responsive">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <table class="table table-striped table-bordered align-middle text-sm table-hover">
                <thead class="table-success text-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>Dosen</th>
                        <th>Hari</th>
                        <th>Waktu</th>
                        <th>Ruang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalPreview as $i => $jadwal)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $jadwal['mata_kuliah_name'] ?? ($jadwal['matkul_name'] ?? 'N/A') }}</td>
                            <td>{{ $jadwal['kelas_name'] ?? 'N/A' }}</td>
                            <td>{{ $jadwal['dosen_name'] ?? 'N/A' }}</td>
                            <td class="text-center">{{ $jadwal['hari'] }}</td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($jadwal['jam_mulai'])->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($jadwal['jam_selesai'])->format('H:i') }}
                            </td>
                            <td class="text-center">{{ $jadwal['ruang_name'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 text-end">
                <form method="POST" action="{{ route('akademik.jadwal-kuliah-store') }}" id="form-simpan-jadwal">
                    @csrf
                    <input type="hidden" name="tahun_akademik_id" value="{{ $tahunAkademikId ?? '' }}">
                    <input type="hidden" name="tanggal" value="{{ $tanggalMulai ?? '' }}">
                    <button type="button" class="btn btn-success btn-lg px-4" id="btn-simpan-jadwal">
                        <i class="fas fa-save me-1"></i> Simpan Jadwal ke Database
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('btn-simpan-jadwal').addEventListener('click', function(e) {
    Swal.fire({
        title: 'Simpan Jadwal?',
        text: 'Jadwal yang sudah digenerate akan disimpan permanen.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-simpan-jadwal').submit();
        }
    });
});
</script>
@endpush
