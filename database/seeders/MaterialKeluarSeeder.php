<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaterialKeluarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $tanggal = $now->toDateString();
        $jam = $now->toTimeString();
        $bulan = 'Agustus';

        $materialsKeluar = [
            [
                'kode_barang'       => '40005914',
                'nama_material'     => 'FERTILIZER:TSP;45% P2O5 MIN;GRANUL',
                'jumlah'            => 250.00,
                'satuan'            => 'KG',
                'tanggal_transaksi' => $tanggal,
                'jam_input'         => $jam,
                'bulan_simpanan'    => $bulan,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'kode_barang'       => '40006222',
                'nama_material'     => 'FERTILIZER:NPK 16-16-16;GRANUL',
                'jumlah'            => 500.00,
                'satuan'            => 'KG',
                'tanggal_transaksi' => $tanggal,
                'jam_input'         => $jam,
                'bulan_simpanan'    => $bulan,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'kode_barang'       => '91057141',
                'nama_material'     => 'GASOLINE:BIODIESEL;CN 48;815-880KG/M3',
                'jumlah'            => 100.00,
                'satuan'            => 'L',
                'tanggal_transaksi' => $tanggal,
                'jam_input'         => $jam,
                'bulan_simpanan'    => $bulan,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
            [
                'kode_barang'       => '91033657',
                'nama_material'     => 'PISAU SADAP SODECI',
                'jumlah'            => 15.00,
                'satuan'            => 'PC',
                'tanggal_transaksi' => $tanggal,
                'jam_input'         => $jam,
                'bulan_simpanan'    => $bulan,
                'created_at'        => $now,
                'updated_at'        => $now,
            ],
        ];

        DB::table('material_keluar')->insert($materialsKeluar);
    }
}