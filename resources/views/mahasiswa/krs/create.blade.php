@extends('base.base-dash-index')

@section('title', 'Pengajuan KRS')

@section('content')
<div class="container py-4">
    <h4 class="fw-bold text-primary mb-4">
        <i class="fa fa-book-open me-2"></i> Pengajuan Kartu Rencana Studi (KRS)
    </h4>

    {{-- Dropdown filter Tahun Akademik --}}
    <form method="GET" action="{{ route($prefix . 'krs.create') }}" class="mb-4 d-flex align-items-center gap-3">
        <label for="tahun_akademik_id" class="form-label mb-0 fw-semibold">Pilih Tahun Akademik:</label>
        <select name="tahun_akademik_id" id="tahun_akademik_id" class="form-select w-auto" onchange="this.form.submit()">
            @foreach($allTa as $taItem)
                <option value="{{ $taItem->id }}" {{ ($taItem->id == $ta->id) ? 'selected' : '' }}>
                    {{ $taItem->name }} {{ $taItem->status == 'Aktif' ? '(Aktif)' : '' }}
                </option>
            @endforeach
        </select>
    </form>

    <form action="{{ route($prefix . 'krs.store') }}" method="POST" id="form-krs">
        @csrf
        <input type="hidden" name="tahun_akademik_id" value="{{ $ta->id }}">

        <div class="mb-3 p-3 bg-light border-start border-4 border-primary rounded shadow-sm">
            <strong>Tahun Akademik:</strong> {{ $ta->name ?? '-' }}
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li><i class="fa fa-times-circle me-1"></i> {!! $error !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="alert alert-info d-flex align-items-center">
            <i class="fa fa-info-circle me-2"></i>
            Silakan pilih mata kuliah untuk semester ini. Maksimal <strong>24 SKS</strong>.
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered table-hover align-middle" id="krs-table">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width: 6%;">Pilih</th>
                        <th>Kode</th>
                        <th class="text-start">Nama Mata Kuliah</th>
                        <th style="width: 6%;">SKS</th>
                        <th>Dosen</th>
                        <th style="width: 12%;">Hari</th>
                        <th style="width: 12%;">Metode</th>
                        <th style="width: 12%;">Ruang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jadwalKuliah as $jadwal)
                        <tr>
                            <td class="text-center">
                                @if (!in_array($jadwal->id, $krsTaken))
                                    <input type="checkbox" name="jadwal_kuliah_id[]" value="{{ $jadwal->id }}" id="jadwal-{{ $jadwal->id }}" data-sks="{{ $jadwal->bsks ?? 0 }}">
                                @else
                                    <span class="badge bg-success">Diambil</span>
                                @endif
                            </td>
                            <td class="text-center text-primary fw-semibold">{{ $jadwal->mataKuliah->code ?? '-' }}</td>
                            <td>{{ $jadwal->mataKuliah->name ?? '-' }}</td>
                            <td class="text-center">{{ $jadwal->bsks ?? '-' }}</td>
                            <td>{{ $jadwal->dosen->name ?? '-' }}</td>
                            <td class="text-center">{{ $jadwal->hari ?? '-' }}</td>
                            <td class="text-center">{{ $jadwal->metode ?? '-' }}</td>
                            <td class="text-center">{{ $jadwal->ruang->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fa fa-info-circle me-1"></i>
                                Tidak ada jadwal kuliah tersedia untuk kelas Anda saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light border-start border-4 border-info rounded shadow-sm">
            <div>
                <strong>Total SKS yang dipilih:</strong> <span id="total-sks" class="text-primary">0</span>
            </div>
            <div>
                <strong>Sisa SKS maksimal:</strong> <span id="remaining-sks" class="text-success">24</span>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route($prefix . 'krs.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-1"></i> Kembali
            </a>

            @if (!$jadwalKuliah->isEmpty())
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
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-krs');
    const checkboxes = form.querySelectorAll('input[name="jadwal_kuliah_id[]"]');
    const totalSksElem = document.getElementById('total-sks');
    const remainingSksElem = document.getElementById('remaining-sks');
    const maxSks = 24;

    function updateSksSummary() {
        let totalSks = 0;
        checkboxes.forEach(chk => {
            if (chk.checked) {
                totalSks += parseInt(chk.dataset.sks) || 0;
            }
        });
        totalSksElem.textContent = totalSks;
        remainingSksElem.textContent = Math.max(0, maxSks - totalSks);
    }

    checkboxes.forEach(chk => chk.addEventListener('change', updateSksSummary));
    updateSksSummary();

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const checkedBoxes = form.querySelectorAll('input[name="jadwal_kuliah_id[]"]:checked');
        if (checkedBoxes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Kamu belum memilih mata kuliah (KRS)!',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
            return;
        }

        const totalSks = Array.from(checkedBoxes).reduce((acc, chk) => acc + (parseInt(chk.dataset.sks) || 0), 0);
        if (totalSks > maxSks) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `Total SKS melebihi batas maksimum (${maxSks} SKS).`,
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-danger' },
                buttonsStyling: false
            });
            return;
        }

        Swal.fire({
            title: 'Ajukan KRS?',
            text: 'Pastikan Anda telah memilih mata kuliah dengan benar.',
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
