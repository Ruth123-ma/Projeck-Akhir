<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MaterialKeluar;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MaterialKeluarController
{
    /**
     * Tampilkan halaman utama Blade atau data arsip material keluar (JSON).
     */
    public function index(Request $request): View|JsonResponse
    {
        // Jika permintaan membutuhkan respon JSON / via AJAX (Filter Bulan)
        if ($request->wantsJson() || $request->has('bulan')) {
            $query = MaterialKeluar::query();

            if ($request->filled('bulan') && $request->bulan !== 'Semua') {
                $query->where('bulan_simpanan', $request->bulan);
            }

            $data = $query->orderBy('tanggal_transaksi', 'desc')
                         ->orderBy('jam_input', 'desc')
                         ->get();

            return response()->json($data);
        }

        // Tampilkan view form Blade
        return view('material_keluar');
    }

    /**
     * Simpan data pengeluaran material ke database.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode'    => 'required|string|max:50',
            'nama'    => 'required|string|max:255',
            'jumlah'  => 'required|numeric|min:0.01',
            'satuan'  => 'required|string|max:20',
            'tanggal' => 'required|date',
            'jam'     => 'required',
            'bulan'   => 'required|string|max:20',
        ]);

        $material = MaterialKeluar::create([
            'kode_barang'       => $validated['kode'],
            'nama_material'     => $validated['nama'],
            'jumlah'            => $validated['jumlah'],
            'satuan'            => $validated['satuan'],
            'tanggal_transaksi' => $validated['tanggal'],
            'jam_input'         => $validated['jam'],
            'bulan_simpanan'    => $validated['bulan'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data material keluar berhasil disimpan.',
            'data'    => $material
        ], 201);
    }

}
