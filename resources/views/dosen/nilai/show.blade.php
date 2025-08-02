@extends('base.base-dash-index')

@section('title', 'Detail Nilai')
@section('menu', 'Nilai')
@section('submenu', 'Detail Nilai')
@section('urlmenu', route('dosen.nilai.list'))

@section('content')
<div class="container mt-4">
    <h3>Nilai Mahasiswa - {{ $jadwal->mataKuliah->matkul ?? $jadwal->mataKuliah->name ?? 'Nama Mata Kuliah' }}</h3>

    {{-- Informasi Mata Kuliah, Kelas, Dosen, Tahun Akademik, Status --}}
    <table class="table table-bordered mb-4">
        <tbody>
            <tr>
                <th style="width: 150px;">Mata Kuliah</th>
                <td>{{ $jadwal->mataKuliah->matkul ?? $jadwal->mataKuliah->name ?? '-' }}</td>
                <th style="width: 150px;">Kelas</th>
                <td>
                    @if($jadwal->kelas && $jadwal->kelas->count() > 0)
                        {{ $jadwal->kelas->pluck('nama')->join(', ') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dosen</th>
                <td>{{ $jadwal->dosen->name ?? '-' }}</td>
                <th>Tahun Akademik</th>
                <td>{{ $ta->name ?? '-' }} / {{ ucfirst($ta->type ?? '-') }}</td>
            </tr>
            <tr>
                <th>Status Nilai</th>
                <td colspan="3">
                    @if ($jadwal->nilai_locked)
                        <span class="badge bg-danger"><i class="fas fa-lock"></i> Terkunci</span>
                    @elseif ($jadwal->nilai_submitted)
                        <span class="badge bg-warning text-dark"><i class="fas fa-paper-plane"></i> Diajukan ke BAAK</span>
                    @else
                        <span class="badge bg-secondary">Draft</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Form Pengaturan Bobot Nilai --}}
    @if (!$jadwal->nilai_locked)
    <form id="formBobot" action="{{ route('dosen.nilai.updateKomposisi', $jadwal->id) }}" method="POST" class="mb-4">
        @csrf
        <label class="fw-bold mb-2">Pengaturan Komposisi Nilai (%)</label>
        <div class="row g-3 align-items-end">
            @php $total = 0; @endphp
            @foreach (['absen' => 10, 'tugas' => 30, 'uts' => 30, 'uas' => 30] as $key => $default)
            @php $nilai = old('bobot_'.$key, $jadwal->{'bobot_'.$key} ?? $default); $total += $nilai; @endphp
            <div class="col-md-2">
                <div class="input-group">
                    <input type="number"
                        class="form-control bobot-input @error('bobot_'.$key) is-invalid @enderror"
                        name="bobot_{{ $key }}"
                        value="{{ $nilai }}"
                        min="0" max="100" required>
                    <span class="input-group-text">%</span>
                </div>
                @error('bobot_'.$key)
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted text-capitalize">{{ $key }}</small>
            </div>
            @endforeach
            <div class="col-md-2 text-center">
                <div class="fw-bold">Total</div>
                <div><span id="totalBobot">{{ $total }}</span>%</div>
                <div id="warningBobot" class="text-danger fw-bold mt-1" style="display:none;"></div>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" id="btnSimpanBobot" class="btn btn-success w-100" disabled>
                    <i class="fas fa-save"></i> Simpan Bobot
                </button>
            </div>
        </div>
    </form>
    @endif

    {{-- FORM NILAI MAHASISWA --}}
    @if (!$jadwal->nilai_locked)
        {{-- Form Input --}}
        <form action="{{ route('dosen.nilai.update', $jadwal->id) }}" method="POST" class="mb-4" id="formNilai">
            @csrf
            @method('PATCH')

            <table class="table table-striped table-bordered align-middle">
                <thead class="thead-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Nilai Absen</th>
                        <th>Nilai Tugas</th>
                        <th>Nilai UTS</th>
                        <th>Nilai UAS</th>
                        <th>Nilai Akhir</th>
                        <th>Nilai Huruf</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->numb_nim ?? '-' }}</td>
                        <td>{{ $item->name ?? '-' }}</td>
                        <input type="hidden" name="idMhs[]" value="{{ $item->id }}">
                        @foreach (['absen', 'tugas', 'uts', 'uas'] as $type)
                        <td>
                            <input type="number"
                                name="nilai_{{ $type }}[]"
                                class="form-control form-control-sm text-center nilai-input"
                                data-index="{{ $index }}" data-type="{{ $type }}"
                                min="0" max="100" step="any"
                                value="{{ old("nilai_{$type}.$index", $item->{"nilai_{$type}"} ?? '') }}">
                        </td>
                        @endforeach
                        <td class="text-center"><span id="nilai_akhir_{{ $index }}">{{ $item->nilai_akhir ?? '-' }}</span></td>
                        <td class="text-center"><span id="nilai_huruf_{{ $index }}">{{ $item->nilai_huruf ?? '-' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center">Belum ada mahasiswa.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Simpan Nilai Draft</button>
        </form>
    @else
        {{-- TABEL READONLY --}}
        <table class="table table-striped table-bordered align-middle">
            <thead class="thead-dark text-center">
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Nilai Absen</th>
                    <th>Nilai Tugas</th>
                    <th>Nilai UTS</th>
                    <th>Nilai UAS</th>
                    <th>Nilai Akhir</th>
                    <th>Nilai Huruf</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->numb_nim ?? '-' }}</td>
                    <td>{{ $item->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->nilai_absen ?? '-' }}</td>
                    <td class="text-center">{{ $item->nilai_tugas ?? '-' }}</td>
                    <td class="text-center">{{ $item->nilai_uts ?? '-' }}</td>
                    <td class="text-center">{{ $item->nilai_uas ?? '-' }}</td>
                    <td class="text-center">{{ $item->nilai_akhir ?? '-' }}</td>
                    <td class="text-center">{{ $item->nilai_huruf ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center">Belum ada mahasiswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- TOMBOL AKSI --}}
<div class="d-flex flex-wrap gap-2 mt-3">

    {{-- Tombol Cetak --}}
    <a href="{{ route('dosen.nilai.cetakDosen', $jadwal->id) }}" class="btn btn-success mb-2" target="_blank">
        <i class="fas fa-print"></i> Cetak Nilai
    </a>

    {{-- Jika nilai belum dikunci --}}
    @if (!($jadwal->nilai_locked ?? false))
        {{-- Tombol Edit Nilai --}}
        <form action="{{ route('dosen.nilai.edit', $jadwal->id) }}" method="GET" class="form-fallback">
            @csrf
            <button type="submit" class="btn btn-primary mb-2">
                <i class="fas fa-edit"></i> Edit Nilai
            </button>
        </form>

        {{-- Tombol Kunci & Ajukan --}}
        <form action="{{ route('dosen.nilai.ajukan', $jadwal->id) }}" method="POST" class="form-fallback">
            @csrf
            <button type="submit" class="btn btn-danger mb-2">
                <i class="fas fa-lock"></i> Kunci & Ajukan
            </button>
        </form>
    @endif

    {{-- Jika nilai sudah dikunci --}}
    @if ($jadwal->nilai_locked ?? false)
        @if (!($jadwal->nilai_shown ?? false))
            {{-- Tombol Tampilkan Nilai ke Mahasiswa --}}
            <form action="{{ route('dosen.nilai.tampilkan', $jadwal->id) }}" method="POST" class="form-fallback">
                @csrf
                <button type="submit" class="btn btn-info mb-2">
                    <i class="fas fa-eye"></i> Tampilkan ke Mahasiswa
                </button>
            </form>
        @else
            <span class="badge bg-success align-self-center mb-2">
                <i class="fas fa-check"></i> Sudah Ditampilkan
            </span>
        @endif
    @endif

    {{-- Tombol Buka Kunci (Superadmin saja) --}}
    @can('superadmin')
        <form action="{{ route('dosen.nilai.bukaKunci', $jadwal->id) }}" method="POST" class="form-fallback">
            @csrf
            <button type="submit" class="btn btn-warning mb-2">
                <i class="fas fa-unlock"></i> Buka Kunci
            </button>
        </form>
    @endcan

    {{-- Tombol Kembali --}}
    <a href="{{ route('dosen.nilai.list') }}" class="btn btn-secondary mb-2">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

</div>



    
</div>

{{-- SCRIPT HITUNG NILAI & VALIDASI BOBOT --}}
<script>    
document.addEventListener('DOMContentLoaded', function () {
    // Ambil bobot dari server
    const bobot = {
        absen: {{ $jadwal->bobot_absen ?? 10 }},
        tugas: {{ $jadwal->bobot_tugas ?? 30 }},
        uts: {{ $jadwal->bobot_uts ?? 30 }},
        uas: {{ $jadwal->bobot_uas ?? 30 }},
    };

    // Fungsi konversi nilai angka ke huruf
    function nilaiHuruf(nilai) {
        if (nilai >= 93) return 'A+';
        if (nilai >= 90) return 'A';
        if (nilai >= 87) return 'A-';
        if (nilai >= 83) return 'B+';
        if (nilai >= 80) return 'B';
        if (nilai >= 77) return 'B-';
        if (nilai >= 73) return 'C+';
        if (nilai >= 70) return 'C';
        if (nilai >= 67) return 'C-';
        if (nilai >= 63) return 'D+';
        if (nilai >= 60) return 'D';
        return 'E';
    }

    // Hitung nilai akhir dan huruf tiap mahasiswa
    function hitungNilai(index) {
        const absen = Number(document.querySelector(`input[name="nilai_absen[]"][data-index="${index}"]`)?.value) || 0;
        const tugas = Number(document.querySelector(`input[name="nilai_tugas[]"][data-index="${index}"]`)?.value) || 0;
        const uts = Number(document.querySelector(`input[name="nilai_uts[]"][data-index="${index}"]`)?.value) || 0;
        const uas = Number(document.querySelector(`input[name="nilai_uas[]"][data-index="${index}"]`)?.value) || 0;

        const total = (absen * bobot.absen + tugas * bobot.tugas + uts * bobot.uts + uas * bobot.uas) / 100;
        return {
            total: total.toFixed(2),
            huruf: nilaiHuruf(total)
        };
    }

    // Event listener untuk update nilai akhir/huruf saat input nilai diubah
    document.querySelectorAll('.nilai-input').forEach(input => {
        input.addEventListener('input', e => {
            const index = e.target.getAttribute('data-index');
            const hasil = hitungNilai(index);
            document.getElementById(`nilai_akhir_${index}`).textContent = hasil.total;
            document.getElementById(`nilai_huruf_${index}`).textContent = hasil.huruf;
        });
    });

    // Inisialisasi nilai akhir & huruf pada load halaman
    @foreach($items as $index => $item)
    (() => {
        const hasil = hitungNilai({{ $index }});
        document.getElementById('nilai_akhir_{{ $index }}').textContent = hasil.total;
        document.getElementById('nilai_huruf_{{ $index }}').textContent = hasil.huruf;
    })();
    @endforeach

    // -- Bagian validasi dan tampilan total bobot komposisi nilai --
    const formBobot = document.getElementById('formBobot');
    if (formBobot) {
        const inputsBobot = formBobot.querySelectorAll('.bobot-input');
        const totalBobotEl = document.getElementById('totalBobot');
        const warningEl = document.getElementById('warningBobot');
        const btnSimpan = document.getElementById('btnSimpanBobot');

        function updateTotalBobot() {
            let total = 0;
            inputsBobot.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            totalBobotEl.textContent = total;

            if (total < 100) {
                warningEl.style.display = 'block';
                warningEl.textContent = 'Total bobot nilai kurang dari 100%.';
                btnSimpan.disabled = true;
            } else if (total > 100) {
                warningEl.style.display = 'block';
                warningEl.textContent = 'Total bobot nilai lebih dari 100%.';
                btnSimpan.disabled = true;
            } else {
                warningEl.style.display = 'none';
                warningEl.textContent = '';
                btnSimpan.disabled = false;
            }
        }

        inputsBobot.forEach(input => {
            input.addEventListener('input', updateTotalBobot);
        });

        // Validasi saat submit form bobot
        formBobot.addEventListener('submit', function(e) {
            const total = Array.from(inputsBobot).reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
            if (total !== 100) {
                e.preventDefault();
                alert('Total komposisi nilai harus 100%. Silakan sesuaikan nilai bobot.');
            }
        });

        // Jalankan validasi saat load halaman
        updateTotalBobot();
    }
});
</script>

{{-- CDN SweetAlert2 jika belum dimuat --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.querySelectorAll('.form-fallback').forEach(form => {
        form.addEventListener('submit', async function (e) {
            e.preventDefault(); // Hentikan submit asli

            try {
                // Coba akses endpoint dulu (HEAD request untuk cek)
                const res = await fetch(this.action, {
                    method: 'HEAD',
                    headers: {
                        'X-CSRF-TOKEN': this.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (res.ok) {
                    // Kalau aman, submit normal
                    this.submit();
                } else {
                    throw new Error('Route tidak tersedia');
                }

            } catch (err) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fitur Belum Tersedia',
                    text: 'Aksi ini belum tersedia atau sedang dikembangkan.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>

@endsection
