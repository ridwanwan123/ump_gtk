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

        // SIMPAN DATA
        AttendancePeriod::create([
            'tahun' => $validated['tahun'],
            'triwulan' => $validated['triwulan'],
            'is_active' => $request->has('is_active')
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
            $period->save();

        } else {
            // kalau dimatikan, cukup nonaktifkan
            $period->is_active = false;
            $period->save();
        }

        return back()->with('success', 'Status periode berhasil diperbarui.');
    }
}
