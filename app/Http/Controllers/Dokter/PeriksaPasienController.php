<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        // Hanya tampilkan obat dengan stok > 0
        $obats = Obat::where('stok', '>', 0)->get();
        return view('dokter.periksa-pasien.create', compact('obats', 'id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_daftar_poli' => 'required|exists:daftar_poli,id',
            'obat_json'      => 'required',
            'catatan'        => 'nullable|string',
            'biaya_periksa'  => 'required|integer',
        ]);

        $obatIds = json_decode($request->obat_json, true);

        if (empty($obatIds) || !is_array($obatIds)) {
            return back()->withErrors(['obat_json' => 'Pilih minimal satu obat.'])->withInput();
        }

        // Validasi stok sebelum transaksi
        $obatHabis = [];
        foreach ($obatIds as $idObat) {
            $obat = Obat::find($idObat);
            if (!$obat || $obat->stok <= 0) {
                $obatHabis[] = $obat ? $obat->nama_obat : "ID {$idObat}";
            }
        }

        if (!empty($obatHabis)) {
            $namaObat = implode(', ', $obatHabis);
            return back()
                ->with('error', "Stok obat habis: {$namaObat}. Pemeriksaan dibatalkan.")
                ->withInput();
        }

        // Gunakan DB Transaction agar data aman
        DB::beginTransaction();
        try {
            // Simpan data pemeriksaan
            $periksa = Periksa::create([
                'id_daftar_poli' => $request->id_daftar_poli,
                'tgl_periksa'    => now(),
                'catatan'        => $request->catatan,
                'biaya_periksa'  => $request->biaya_periksa + 150000,
            ]);

            // Simpan detail resep dan kurangi stok
            foreach ($obatIds as $idObat) {
                $obat = Obat::lockForUpdate()->find($idObat);

                // Validasi ulang stok di dalam transaksi
                if (!$obat || $obat->stok <= 0) {
                    DB::rollBack();
                    return back()
                        ->with('error', "Stok obat '{$obat->nama_obat}' habis saat proses penyimpanan.")
                        ->withInput();
                }

                // Simpan detail periksa
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat'    => $idObat,
                ]);

                // Kurangi stok sebanyak 1
                $obat->decrement('stok', 1);
            }

            DB::commit();

            return redirect()->route('periksa-pasien.index')
                ->with('success', 'Data pemeriksaan berhasil disimpan. Stok obat telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }
}