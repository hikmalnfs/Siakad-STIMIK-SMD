{{-- resources/views/dosen/nilai/edit.blade.php --}}
@extends('base.base-dash-index')

@section('title', 'Edit Nilai')

@section('menu', 'Nilai')

@section('submenu', 'Edit Nilai')

@section('urlmenu', route('dosen.nilai.list'))

@section('content')
<div class="container mt-4">
    <h3>Edit Nilai - {{ $jadwal->mataKuliah->matkul ?? '' }}</h3>

    <form action="{{ route('dosen.nilai.update', $jadwal->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Nilai Absen</th>
                    <th>Nilai Tugas</th>
                    <th>Nilai UTS</th>
                    <th>Nilai UAS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                <tr>
                    <td>
                        {{ $index + 1 }}
                        <input type="hidden" name="idMhs[]" value="{{ $item->mahasiswa_id }}">
                    </td>
                    <td>{{ $item->mahasiswa->nim ?? '-' }}</td>
                    <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                    <td>
                        <input type="number" name="nilai_absen[]" class="form-control" value="{{ old('nilai_absen.'.$index, $item->nilai_absen) }}" min="0" max="100" step="1" required>
                    </td>
                    <td>
                        <input type="number" name="nilai_tugas[]" class="form-control" value="{{ old('nilai_tugas.'.$index, $item->nilai_tugas) }}" min="0" max="100" step="1" required>
                    </td>
                    <td>
                        <input type="number" name="nilai_uts[]" class="form-control" value="{{ old('nilai_uts.'.$index, $item->nilai_uts) }}" min="0" max="100" step="1" required>
                    </td>
                    <td>
                        <input type="number" name="nilai_uas[]" class="form-control" value="{{ old('nilai_uas.'.$index, $item->nilai_uas) }}" min="0" max="100" step="1" required>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada mahasiswa yang mengambil KRS</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <button type="submit" class="btn btn-success">Simpan Nilai</button>
    </form>
</div>
@endsection
