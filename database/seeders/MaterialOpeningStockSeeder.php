<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\MaterialMasuk;
use App\Models\MaterialTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaterialOpeningStockSeeder extends Seeder
{
    private const OPENING_NOTE = 'Stok awal hasil impor saldo gudang (03-09-2026)';

    public function run(): void
    {
        $transactionDate = Carbon::create(2026, 9, 3, 7, 4, 39);

        DB::transaction(function () use ($transactionDate): void {
            Material::query()
                ->where('stok_akhir', '>', 0)
                ->orderBy('id')
                ->each(function (Material $material) use ($transactionDate): void {
                    if (MaterialTransaction::where('material_id', $material->id)->exists()) {
                        return;
                    }

                    $quantity = (float) $material->stok_akhir;

                    MaterialTransaction::create([
                        'material_id' => $material->id,
                        'type' => 'IN',
                        'quantity' => $quantity,
                        'user_id' => 'IMPORT-STOK-AWAL',
                        'notes' => self::OPENING_NOTE,
                    ])->forceFill([
                        'created_at' => $transactionDate,
                        'updated_at' => $transactionDate,
                    ])->save();

                    MaterialMasuk::firstOrCreate(
                        [
                            'kode_barang' => $material->kode_barang,
                            'tanggal_transaksi' => $transactionDate->toDateString(),
                            'jam_input' => $transactionDate->format('H:i:s'),
                            'jumlah' => $quantity,
                        ],
                        [
                            'nama_material' => $material->nama_material,
                            'satuan' => $material->satuan,
                            'bulan_simpanan' => 'September',
                        ]
                    );
                });
        });
    }
}
