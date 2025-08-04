@extends('base.base-dash-index')

@section('menu', 'Kartu Rencana Studi')
@section('submenu', 'Pengisian KRS')
@section('urlmenu', route('mahasiswa.krs.index'))
@section('subdesc', 'Pilih mata kuliah sesuai semester dan kelas')
@section('title', 'Pengajuan KRS')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold text-primary mb-4">
        <i class="fa fa-book-open me-2"></i> Pengajuan Kartu Rencana Studi (KRS)
    </h4>

    {{-- Pilih Tahun Akademik --}}
    <form method="GET" action="{{ route($prefix . 'krs.create') }}" class="mb-4 d-flex align-items-center gap-3">
        <label for="tahun_akademik_id" class="form-label mb-0 fw-semibold">Pilih Tahun Akademik:</label>
        <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select w-auto" onchange="this.form.submit()">
            @foreach ($allTa as $taItem)
                <option value="{{ $taItem->id }}" {{ $taItem->id == $ta->id ? 'selected' : '' }}>
                    {{ $taItem->name }} {{ $taItem->status == 'Aktif' ? '(Aktif)' : '' }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Form Utama Pengajuan --}}
    <form action="{{ route($prefix . 'krs.store') }}" method="POST" id="form-krs">
        @csrf
        <input type="hidden" name="tahun_akademik_id" value="{{ $ta->id }}">

        <div class="mb-3 p-3 bg-light border-start border-4 border-primary rounded shadow-sm">
            <strong>Tahun Akademik:</strong> {{ $ta->name ?? '-' }}
        </div>

        <div class="alert alert-info d-flex align-items-center">
            <i class="fa fa-info-circle me-2"></i>
            Silakan pilih mata kuliah untuk semester ini. Maksimal <strong>24 SKS</strong>.
        </div>

        {{-- Tabel Pilihan Mata Kuliah --}}
        <div class="table-responsive rounded shadow-sm">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th style="width: 5%">Pilih</th>
                        <th>Kode</th>
                        <th>Mata Kuliah</th>
                        <th>SKS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mataKuliahUnique as $matkul)
                        @php
                            $jadwal = $jadwalKuliah->firstWhere('mataKuliah.id', $matkul->id);
                            $isTaken = in_array($jadwal->id ?? null, $krsTakenJadwalIds);
                        @endphp
                        <tr>
                            <td class="text-center">
                                @if ($jadwal)
                                    <input type="checkbox" class="form-check-input matkul-checkbox"
                                        name="jadwal_kuliah_id[]" value="{{ $jadwal->id }}"
                                        data-sks="{{ optional($jadwal->mataKuliah)->bsks ?? 0 }}"
                                        {{ $isTaken ? 'checked disabled' : '' }}>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center text-primary fw-semibold">{{ $matkul->code ?? '-' }}</td>
                            <td>{{ $matkul->name ?? '-' }}</td>
                            <td class="text-center">{{ $jadwal->mataKuliah->bsks ?? ($matkul->bsks ?? '-') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fa fa-info-circle me-1"></i> Tidak ada mata kuliah tersedia untuk kelas Anda saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Ringkasan SKS --}}
        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light border-start border-4 border-info rounded shadow-sm">
            <div>
                <strong>Total SKS yang dipilih:</strong> <span id="total-sks" class="text-primary">0</span>
            </div>
            <div>
                <strong>Sisa SKS maksimal:</strong> <span id="remaining-sks" class="text-success">24</span>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="d-flex justify-content-between">
            <a href="{{ route($prefix . 'krs.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-1"></i> Kembali
            </a>
            @if (!$mataKuliahUnique->isEmpty())
                <button type="submit" class="btn btn-primary" id="btn-submit">
                    <i class="fa fa-paper-plane me-1"></i> Ajukan KRS
                </button>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-krs');
    const totalSksElem = document.getElementById('total-sks');
    const remainingSksElem = document.getElementById('remaining-sks');
    const maxSks = 24;

    if (!form || !totalSksElem || !remainingSksElem) {
        console.warn('Element form atau elemen SKS tidak ditemukan.');
        return;
    }

    function hitungTotalSks() {
        let total = 0;
        const checkboxes = form.querySelectorAll('input.matkul-checkbox:checked');
        checkboxes.forEach(cb => {
            const sks = parseInt(cb.dataset.sks) || 0;
            total += sks;
        });

        totalSksElem.textContent = total;
        remainingSksElem.textContent = Math.max(0, maxSks - total);
        return total;
    }

    // Hitung saat load
    hitungTotalSks();

    // Event saat checkbox berubah
    form.querySelectorAll('input.matkul-checkbox').forEach(cb => {
        cb.addEventListener('change', hitungTotalSks);
    });

    // Validasi saat submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const total = hitungTotalSks();
        const terpilih = form.querySelectorAll('input.matkul-checkbox:checked').length;

        if (terpilih === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Kamu belum memilih mata kuliah!',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-warning' },
                buttonsStyling: false
            });
            return;
        }

        if (total > maxSks) {
            Swal.fire({
                icon: 'error',
                title: 'Melebihi SKS',
                text: `Total SKS melebihi batas maksimum (${maxSks} SKS).`,
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-danger' },
                buttonsStyling: false
            });
            return;
        }

        Swal.fire({
            title: 'Ajukan KRS?',
            text: 'Pastikan mata kuliah yang dipilih sudah benar.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ajukan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(result => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
