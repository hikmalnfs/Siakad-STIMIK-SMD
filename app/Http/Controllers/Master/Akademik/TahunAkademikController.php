<?php

namespace App\Http\Controllers\Master\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Use System
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
// Use Models
use App\Models\Akademik\TahunAkademik;
use App\Models\Pengaturan\WebSetting;
// Use Plugins


class TahunAkademikController extends Controller
{
    public function renderTaka()
    {
        $user = Auth::user();
        $data['webs'] = WebSetting::first();
        $data['spref'] = $user ? $user->prefix : '';
        $data['menus'] = "Master";
        $data['pages'] = "Tahun Akademik";
        $data['academy'] = $data['webs']->school_apps . ' by ' . $data['webs']->school_name;

        // ✅ Cek realtime semua semester yang sudah berakhir, ubah status ke 'Tidak Aktif'
        TahunAkademik::where('ended_date', '<', now())
            ->where('status', 'Aktif')
            ->update(['status' => 'Tidak Aktif']);

        $data['taka'] = TahunAkademik::all();

        return view('master.akademik.taka-index', $data, compact('user'));
    }

    public function handleTaka(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',  // Contoh: 2025/2026
                'start_date' => 'required|date',
                'ended_date' => 'required|date|after:start_date',
                'desc' => 'nullable|string',
                'aktif_semester' => 'required|in:Ganjil,Genap',
            ]);

            $yearRange = $request->name;
            $startDate = $request->start_date;
            $endDate = $request->ended_date;

            $ganjilStart = $startDate;
            $ganjilEnd = date('Y-m-d', strtotime("$startDate +5 months"));

            $genapStart = date('Y-m-d', strtotime("$ganjilEnd +1 day"));
            $genapEnd = $endDate;

            // Hapus jika ada yang sama (optional, jika perlu cegah duplikat)
            TahunAkademik::where('name', $yearRange)->delete();

            // ❌ Jangan lagi nonaktifkan semua status
            // TahunAkademik::where('status', 'Aktif')->update(['status' => 'Tidak Aktif']);

            // Simpan Ganjil
            TahunAkademik::create([
                'name' => $yearRange,
                'type' => 'Ganjil',
                'code' => 'TAK-' . Str::random(10),
                'start_date' => $ganjilStart,
                'ended_date' => $ganjilEnd,
                'desc' => $request->desc,
                'status' => ($request->aktif_semester === 'Ganjil') ? 'Aktif' : 'Tidak Aktif',
                'created_by' => Auth::id(),
            ]);

            // Simpan Genap
            TahunAkademik::create([
                'name' => $yearRange,
                'type' => 'Genap',
                'code' => 'TAK-' . Str::random(10),
                'start_date' => $genapStart,
                'ended_date' => $genapEnd,
                'desc' => $request->desc,
                'status' => ($request->aktif_semester === 'Genap') ? 'Aktif' : 'Tidak Aktif',
                'created_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tahun Akademik Ganjil & Genap berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan Tahun Akademik: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateTaka(Request $request, $code)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:Ganjil,Genap',
                'start_date' => 'required|date',
                'ended_date' => 'required|date|after:start_date',
                'desc' => 'nullable|string',
                'status' => 'required|in:Aktif,Tidak Aktif',
            ]);

            $taka = TahunAkademik::where('code', $code)->firstOrFail();

            // ❌ Jangan menonaktifkan semua tahun aktif
            // TahunAkademik::where('status', 'Aktif')->update(['status' => 'Tidak Aktif']);

            $taka->update([
                'name' => $request->name,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'ended_date' => $request->ended_date,
                'desc' => $request->desc,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tahun Akademik berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Tahun Akademik: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function deleteTaka($code)
    {
        try {
            DB::beginTransaction();

            $taka = TahunAkademik::where('code', $code)->firstOrFail();
            
            // Check if this is the active academic year
            if ($taka->status === 'Aktif') {
                return redirect()->back()->with('error', 'Tidak dapat menghapus Tahun Akademik yang sedang aktif');
            }

            $taka->update([
                'deleted_by' => Auth::id()
            ]);
            $taka->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Tahun Akademik berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus Tahun Akademik: ' . $e->getMessage());
        }
    }
}
