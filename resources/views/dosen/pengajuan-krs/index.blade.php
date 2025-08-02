@extends('base.base-dash-index')

@section('menu', 'Pengajuan KRS')
@section('submenu', 'Daftar Pengajuan')
@section('urlmenu', route('dosen.pengajuan-krs.index'))
@section('subdesc', 'Daftar pengajuan KRS mahasiswa yang harus diverifikasi dosen')
@section('title', 'Daftar Pengajuan - Pengajuan KRS - Siakad By Internal Developer')

@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">@yield('menu') - @yield('submenu')</h5>
                    <a href="{{ route('dosen.home-index') }}" class="btn btn-warning">
                        <i class="fa-solid fa-arrow-left"></i> Dashboard
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="table1">
                            <thead class="text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Jumlah Mata Kuliah (Menunggu)</th>
                                    <th>Status Pengajuan Terakhir</th>
                                    <th>Pengajuan Terakhir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pengajuans as $key => $item)
                                @php
                                    // Status dari pengajuan terakhir
                                    $status = strtolower($item->status);
                                @endphp
                                <tr class="text-center">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->mahasiswa->name ?? '-' }}</td>
                                    <td>{{ $item->mahasiswa->krs()->where('status', 'Menunggu')->count() }} matakuliah</td>
                                    <td>
                                        @if ($status === 'menunggu')
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        @elseif ($status === 'disetujui')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif ($status === 'ditolak')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span>{{ $item->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->created_at ? $item->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('dosen.pengajuan-krs.show', $item->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada pengajuan KRS.</td>
                                </tr>
                                @endforelse
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
    $(document).ready(function () {
        $('#table1').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                search: "Cari:",
                zeroRecords: "Tidak ditemukan data.",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                lengthMenu: "Tampilkan _MENU_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Selanjutnya"
                }
            }
        });
    });
</script>
@endpush
