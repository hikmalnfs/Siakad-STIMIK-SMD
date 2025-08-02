@extends('base.base-dash-index')

@section('menu', 'Pengajuan KRS')
@section('submenu', 'Detail Pengajuan')
@section('urlmenu', route('dosen.pengajuan-krs.index'))
@section('subdesc', 'Detail pengajuan KRS mahasiswa')
@section('title', 'Detail Pengajuan - Pengajuan KRS - Siakad By Internal Developer')


@section('content')
<section class="content py-4">

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-12 col-12">

            {{-- Informasi Mahasiswa --}}
            <div class="card shadow-sm mb-5 border-0 rounded-4">
                <div class="card-header bg-blue text-white rounded-top-4">
                    <h5 class="mb-0 fw-semibold"><i class="fa-solid fa-user-graduate me-2"></i>Informasi Mahasiswa</h5>
                </div>
                <div class="card-body bg-white rounded-bottom-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-3 fw-semibold">Nama Mahasiswa</dt>
                        <dd class="col-sm-9 text-break">{{ $pengajuan->mahasiswa->name ?? '-' }}</dd>

                        <dt class="col-sm-3 fw-semibold">NIM</dt>
                        <dd class="col-sm-9 text-break">{{ $pengajuan->mahasiswa->numb_nim ?? '-' }}</dd>

                        <dt class="col-sm-3 fw-semibold">Program Studi</dt>
                        <dd class="col-sm-9">{{ $pengajuan->mahasiswa->kelas->pstudi->name ?? '-' }}</dd>

                        <dt class="col-sm-3 fw-semibold">Kelas</dt>
                        <dd class="col-sm-9">{{ $pengajuan->mahasiswa->kelas->name ?? '-' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Daftar Semua Pengajuan --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-blue text-white d-flex justify-content-between align-items-center rounded-top-4">
                    <h5 class="mb-0 fw-semibold"><i class="fa-solid fa-list-check me-2"></i>Daftar Pengajuan KRS</h5>
                    <a href="@yield('urlmenu')" class="btn btn-warning btn-sm rounded-3 shadow-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body p-0 rounded-bottom-4">
                    <div class="table-responsive rounded-bottom-4">
                        <table class="table align-middle mb-0 w-100" id="tablePengajuanAll" style="min-width: 900px;">
                            <thead class="text-white text-center small">
                                <tr>
                                    <th style="border-top-left-radius: 12px;">#</th>
                                    <th>Mata Kuliah</th>
                                    <th>Tahun Akademik</th>
                                    <th>Jenis Kelas</th>
                                    <th>Status</th>
                                    <th>Pengajuan</th>
                                    <th>Keterangan</th>
                                    <th style="border-top-right-radius: 12px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($semuaPengajuan as $key => $item)
                                @php $status = strtolower($item->status); @endphp
                                <tr style="transition: background-color 0.3s ease;">
                                    <td class="text-center small">{{ $key + 1 }}</td>
                                    <td class="text-wrap text-break">{{ $item->jadwal->mataKuliah->name ?? '-' }}</td>
                                    <td class="text-center small">{{ $item->jadwal->tahunAkademik->name ?? '-' }}</td>
                                    <td class="text-center small">{{ $item->jadwal->jenisKelas->name ?? '-' }}</td>
                                    <td class="text-center">
                                        @if ($status === 'menunggu')
                                            <span class="badge rounded-pill px-3 py-1" style="background-color: #f39c12; color: #fff; font-weight: 600;">Menunggu</span>
                                        @elseif ($status === 'disetujui')
                                            <span class="badge rounded-pill px-3 py-1" style="background-color: #27ae60; color: #fff; font-weight: 600;">Disetujui</span>
                                        @elseif ($status === 'ditolak')
                                            <span class="badge rounded-pill px-3 py-1" style="background-color: #c0392b; color: #fff; font-weight: 600;">Ditolak</span>
                                        @else
                                            <span class="small">{{ $item->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center small">{{ $item->created_at->format('d M Y H:i') }}</td>
                                    <td class="text-wrap text-break" title="{{ $item->keterangan ?? '-' }}">{{ $item->keterangan ?? '-' }}</td>
                                    <td class="text-center">

                                        @if ($status === 'menunggu')
                                            <form action="{{ route('dosen.pengajuan-krs.approve', $item->id) }}" method="POST" class="d-inline me-1">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success rounded-3 shadow-sm" onclick="return confirm('Setujui pengajuan ini?')">
                                                    <i class="fas fa-check"></i> Setujui
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-sm btn-danger me-1 rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        @endif

                                        {{-- Tombol Edit selalu tampil --}}
                                        <button type="button" class="btn btn-sm btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <!-- Modal Tolak -->
                                        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $item->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="{{ route('dosen.pengajuan-krs.reject', $item->id) }}" method="POST" class="needs-validation" novalidate>
                                                    @csrf
                                                    <div class="modal-content rounded-4 shadow">
                                                        <div class="modal-header bg-danger text-white rounded-top-4">
                                                            <h5 class="modal-title">Tolak Pengajuan</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label for="keterangan{{ $item->id }}" class="form-label fw-semibold">Alasan Penolakan</label>
                                                            <textarea class="form-control" name="keterangan" id="keterangan{{ $item->id }}" rows="3" required></textarea>
                                                            <div class="invalid-feedback">Harap isi alasan penolakan.</div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-danger rounded-3 shadow-sm">Kirim Penolakan</button>
                                                            <button type="button" class="btn btn-secondary rounded-3 shadow-sm" data-bs-dismiss="modal">Batal</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Modal Edit -->
                                        <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="{{ route('dosen.pengajuan-krs.update', $item->id) }}" method="POST" class="needs-validation" novalidate>
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-content rounded-4 shadow">
                                                        <div class="modal-header bg-primary text-white rounded-top-4">
                                                            <h5 class="modal-title">Edit Pengajuan</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Nama Mahasiswa</label>
                                                                <input type="text" class="form-control" value="{{ $item->mahasiswa->name ?? '-' }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Mata Kuliah</label>
                                                                <input type="text" class="form-control" value="{{ $item->jadwal->mataKuliah->name ?? '-' }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="status{{ $item->id }}" class="form-label fw-semibold">Status Pengajuan</label>
                                                                <select name="status" id="status{{ $item->id }}" class="form-select" required>
                                                                    <option value="Menunggu" {{ $item->status === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                                                    <option value="Disetujui" {{ $item->status === 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                                                    <option value="Ditolak" {{ $item->status === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                                                </select>
                                                                <div class="invalid-feedback">Status harus dipilih.</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="keterangan{{ $item->id }}" class="form-label fw-semibold">Keterangan</label>
                                                                <textarea name="keterangan" id="keterangan{{ $item->id }}" rows="3" class="form-control" maxlength="255">{{ old('keterangan', $item->keterangan) }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-success rounded-3 shadow-sm">
                                                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                                                            </button>
                                                            <button type="button" class="btn btn-secondary rounded-3 shadow-sm" data-bs-dismiss="modal">Batal</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</section>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#tablePengajuanAll').DataTable({
            responsive: true,
            autoWidth: false,
            lengthMenu: [5, 10, 25],
            language: {
                search: "Cari:",
                zeroRecords: "Data tidak ditemukan.",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                lengthMenu: "Tampilkan _MENU_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });

        // Tooltip & Form Validation
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el) });

        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endpush
