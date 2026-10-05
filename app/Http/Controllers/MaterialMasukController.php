<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialMasuk;
use App\Models\MaterialTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialMasukController
{
    public function index(Request $request) {
        $bulan = $request->query('bulan');
        $query = MaterialMasuk::orderBy('id', 'desc');
        
        if ($bulan && $bulan !== 'Semua') {
            $query->where('bulan_simpanan', $bulan);
        }
        
        return response()->json($query->get());
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'kode' => 'sometimes|required|string',
            'material_id' => 'sometimes|nullable|exists:materials,id',
            'nama' => 'sometimes|required|string',
            'type' => 'sometimes|in:IN,OUT',
            'quantity' => 'sometimes|numeric|min:0.01',
            'jumlah' => 'sometimes|numeric|min:0.01',
            'satuan' => 'sometimes|required|string',
            'tanggal' => 'sometimes|required|date',
            'jam' => 'sometimes|required',
            'bulan' => 'sometimes|required|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $type = strtoupper($validated['type'] ?? 'IN');
        $quantity = isset($validated['quantity']) ? (float) $validated['quantity'] : (isset($validated['jumlah']) ? (float) $validated['jumlah'] : 0);
        $kode = trim((string) ($validated['kode'] ?? ''));
        $nama = trim((string) ($validated['nama'] ?? ''));
        $satuan = trim((string) ($validated['satuan'] ?? ''));
        $tanggal = $validated['tanggal'] ?? now()->toDateString();
        $jam = $validated['jam'] ?? now()->format('H:i:s');
        $bulan = $validated['bulan'] ?? now()->translatedFormat('F');
        $notes = $validated['notes'] ?? null;

        try {
            $history = DB::transaction(function () use ($validated, $kode, $nama, $satuan, $tanggal, $jam, $bulan, $type, $quantity, $notes) {
                $material = !empty($validated['material_id'])
                    ? Material::lockForUpdate()->findOrFail($validated['material_id'])
                    : Material::firstOrCreate(
                        ['kode_barang' => $kode],
                        [
                            'nama_material' => $nama ?: 'Material Baru',
                            'jumlah' => 0,
                            'stok_akhir' => 0,
                            'min_stok' => 10,
                            'satuan' => $satuan ?: 'KG',
                            'status' => Material::statusForStock(0, 10),
                            'tanggal_transaksi' => $tanggal,
                            'jam_input' => $jam,
                            'bulan_simpanan' => $bulan,
                        ]
                    );

                if (!$material || empty($kode) && empty($validated['material_id'])) {
                    abort(422, 'Material tidak ditemukan atau kode barang wajib diisi.');
                }

                $material->nama_material = $nama ?: $material->nama_material;
                $material->satuan = $satuan ?: $material->satuan;
                $currentStok = (float) ($material->stok_akhir ?? $material->jumlah ?? 0);

                if ($type === 'OUT' && $currentStok < $quantity) {
                    abort(422, 'Stok tidak mencukupi untuk material ini.');
                }

                $currentStok += $type === 'OUT' ? -$quantity : $quantity;
                $material->fill([
                    'stok_akhir' => $currentStok,
                    'jumlah' => $currentStok,
                    'status' => Material::statusForStock($currentStok, (float) ($material->min_stok ?? 10)),
                    'tanggal_transaksi' => $tanggal,
                    'jam_input' => $jam,
                    'bulan_simpanan' => $bulan,
                ])->save();

                $transactionDate = Carbon::createFromFormat('Y-m-d H:i:s', "{$tanggal} {$jam}");
                $transaction = MaterialTransaction::create([
                    'material_id' => $material->id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'user_id' => 'WH-OP-092',
                    'notes' => $notes,
                ]);
                $transaction->forceFill([
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ])->save();

                $history = MaterialMasuk::create([
                    'kode_barang' => $material->kode_barang,
                    'nama_material' => $material->nama_material,
                    'jumlah' => $quantity,
                    'satuan' => $material->satuan,
                    'tanggal_transaksi' => $tanggal,
                    'jam_input' => $jam,
                    'bulan_simpanan' => $bulan,
                ]);

                return ['history' => $history, 'stock' => $material->stok_akhir];
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getStatusCode());
        }

        return response()->json(['success' => true, 'data' => $history['history'], 'stock' => $history['stock']]);
    }

}
