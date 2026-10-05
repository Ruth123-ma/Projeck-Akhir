<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Exception;

class MaterialController
{
    /**
     * Menampilkan halaman utama material.
     */
    public function index(): View
    {
        return view('material');
    }

    /**
     * Mengambil data material untuk tabel (dengan filter bulan).
     */
    public function getData(Request $request): JsonResponse
    {
        try {
            $bulan = $request->query('bulan');
            $query = Material::orderBy('created_at', 'desc');

            if ($bulan && $bulan !== 'Semua') {
                $query->where('bulan_simpanan', $bulan);
            }

            return response()->json([
                'success' => true,
                'data'    => $query->get()
            ], 200);

        } catch (Exception $e) {
            Log::error('Error MaterialController@getData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data material.'
            ], 500);
        }
    }

    /**
     * Menyimpan data material baru dari Form AJAX.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode_barang'       => 'required|string|max:50',
            'nama_material'     => 'required|string|max:255',
            'jumlah'            => 'required|numeric|min:0.01',
            'satuan'            => 'required|string|max:20',
            'tanggal_transaksi' => 'required|date',
            'jam_input'         => 'required|string',
            'bulan_simpanan'    => 'required|string',
        ]);

        try {
            $material = Material::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data material berhasil disimpan ke simpanan digital.',
                'data'    => $material
            ], 201);

        } catch (Exception $e) {
            Log::error('Error MaterialController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data material!'
            ], 500);
        }
    }

    /**
     * Menghapus data material dari database.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $material = Material::find($id);

            if (!$material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data material tidak ditemukan.'
                ], 404);
            }

            $material->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data material berhasil dihapus.'
            ], 200);

        } catch (Exception $e) {
            Log::error('Error MaterialController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data material.'
            ], 500);
        }
    }
}