<?php

namespace App\Export;

use App\Models\User;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek jika email sudah terdaftar
        $user = User::firstOrCreate(
            ['email' => $row['email']],
            [
                'name' => $row['nama_lengkap'],
                'password' => Hash::make($row['password'] ?? $row['nim']),
                'role' => 'mahasiswa',
                'email_verified_at' => now()
            ]
        );

        // Cek jika NIM sudah terdaftar
        $mahasiswa = Mahasiswa::firstOrCreate(
            ['nim' => $row['nim']],
            [
                'user_id' => $user->id,
                'nama' => $row['nama_lengkap'],
                'email' => $row['email'],
                'no_hp' => $row['no_hp'],
                'program_studi_id' => $row['program_studi_id'],
                'kelas_id' => $row['kelas_id'],
                'status' => $row['status'],
                'semester' => $row['semester'] ?? 0,
            ]
        );

        return $mahasiswa;
    }
}
