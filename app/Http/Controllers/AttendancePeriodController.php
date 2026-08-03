<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendancePeriod;

class AttendancePeriodController extends Controller
{
    public function index()
    {
        $periods = AttendancePeriod::latest()->paginate(10);

        return view('absensi.absensiPeriode', compact('periods'));
    }

    public function store(Request $request)
    {
        // VALIDASI
        $validated = $request->validate([
            'tahun' => 'required|digits:4',
            'triwulan' => 'required|in:TW 1,TW 2,TW 3,TW 4',
        ]);

        // CEK DUPLIKAT
        $exists = AttendancePeriod::where('tahun', $validated['tahun'])
            ->where('triwulan', $validated['triwulan'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Periode tersebut sudah tersedia.');
        }

        // JIKA ACTIVE DINYALAKAN
        // NONAKTIFKAN SEMUA
        if ($request->has('is_active')) {
            AttendancePeriod::query()->update([
                'is_active' => false
            ]);
        }

        // Bulan aktif otomatis diset ke bulan pertama triwulan yang dipilih.
        // Operator hanya akan bisa input bulan ini sampai superadmin memindahkannya.
        $bulanPertama = AttendancePeriod::BULAN_PER_TW[$validated['triwulan']][0];

        // SIMPAN DATA
        AttendancePeriod::create([
            'tahun' => $validated['tahun'],
            'triwulan' => $validated['triwulan'],
            'is_active' => $request->has('is_active'),
            'bulan_aktif' => $bulanPertama,
        ]);

        return redirect()
            ->route('attendance-period.index')
            ->with('success', 'Periode berhasil ditambahkan.');
    }

    public function toggle($id)
    {
        $period = AttendancePeriod::findOrFail($id);

        // jika mau aktifkan
        if (!$period->is_active) {

            // matikan semua periode lain
            AttendancePeriod::where('is_active', true)
                ->update(['is_active' => false]);

            // aktifkan ini
            $period->is_active = true;

            // pastikan bulan_aktif terisi (mis. untuk periode lama sebelum fitur ini ada)
            if (!$period->bulan_aktif) {
                $period->bulan_aktif = AttendancePeriod::BULAN_PER_TW[$period->triwulan][0];
            }

            $period->save();

        } else {
            // kalau dimatikan, cukup nonaktifkan
            $period->is_active = false;
            $period->save();
        }

        return back()->with('success', 'Status periode berhasil diperbarui.');
    }

    /**
     * Superadmin memindahkan bulan aktif ke bulan lain dalam triwulan yang sama
     * (biasanya "buka bulan berikutnya"). Ini yang mengontrol satu-satunya bulan
     * yang boleh diinput/diedit operator saat ini.
     */
    public function setBulanAktif(Request $request, $id)
    {
        $period = AttendancePeriod::findOrFail($id);

        $request->validate([
            'bulan_aktif' => 'required|integer',
        ]);

        if (!in_array((int) $request->bulan_aktif, $period->bulan_list, true)) {
            return back()->with('error', 'Bulan tidak sesuai triwulan periode ini.');
        }

        $period->update(['bulan_aktif' => (int) $request->bulan_aktif]);

        return back()->with('success', 'Bulan aktif berhasil diperbarui ke bulan ' . $request->bulan_aktif . '.');
    }
}