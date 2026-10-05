<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaterialMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $tanggal = $now->toDateString();
        $jam = $now->toTimeString();
        $bulan = 'Agustus'; // Sesuaikan dengan bulan berjalan

        $materials = [
            ['kode_barang' => '40005914', 'nama_material' => 'FERTILIZER:TSP;45% P2O5 MIN;GRANUL', 'jumlah' => 32505.00, 'satuan' => 'KG'],
            ['kode_barang' => '40005942', 'nama_material' => 'FERTILIZER:UREA;46% N;GRANUL', 'jumlah' => 6704.00, 'satuan' => 'KG'],
            ['kode_barang' => '40005946', 'nama_material' => 'FERTILIZER:KIESERITE;25% MGO;21% S', 'jumlah' => 6912.00, 'satuan' => 'KG'],
            ['kode_barang' => '40005947', 'nama_material' => 'FERTILIZER:KCL (MOP);60% K2O;CRYTL', 'jumlah' => 5641.00, 'satuan' => 'KG'],
            ['kode_barang' => '40006222', 'nama_material' => 'FERTILIZER:NPK 16-16-16;GRANUL', 'jumlah' => 209800.00, 'satuan' => 'KG'],
            ['kode_barang' => '40000214', 'nama_material' => 'EMULGATOR', 'jumlah' => 125.10, 'satuan' => 'L'],
            ['kode_barang' => '40002897', 'nama_material' => 'Tb.192', 'jumlah' => 270.00, 'satuan' => 'EA'],
            ['kode_barang' => '40005846', 'nama_material' => 'SURFACTANT:ADDITIVE X-EC/100;LIQUID', 'jumlah' => 166.00, 'satuan' => 'L'],
            ['kode_barang' => '40005850', 'nama_material' => 'HERBICIDE:FLUROXYPYR MH ESTER 480G/L;EC', 'jumlah' => 510.00, 'satuan' => 'L'],
            ['kode_barang' => '40005867', 'nama_material' => 'HERBICIDE:METHYL METSULFURON 20%;WG', 'jumlah' => 1447.40, 'satuan' => 'KG'],
            ['kode_barang' => '40005952', 'nama_material' => 'FUNGICIDE:HEXACONAZOLE 50 G/L;SC', 'jumlah' => 1445.80, 'satuan' => 'L'],
            ['kode_barang' => '40005966', 'nama_material' => 'PLANT,GROWTH:ETEFON 10%;SL', 'jumlah' => 2423.70, 'satuan' => 'L'],
            ['kode_barang' => '40006339', 'nama_material' => 'HERBICIDE:TRIKLOPIR 480G/L;SL', 'jumlah' => 335.70, 'satuan' => 'L'],
            ['kode_barang' => '40006776', 'nama_material' => 'HERBICIDE:GLYPHOSATE IPA 480G/L;SL;1L', 'jumlah' => 2625.80, 'satuan' => 'L'],
            ['kode_barang' => '40007021', 'nama_material' => 'FUNGICIDE:DITHANE M-45;80 WP;PW;1 KG', 'jumlah' => 1680.00, 'satuan' => 'KG'],
            ['kode_barang' => '40000210', 'nama_material' => 'CAUSTIC SODA', 'jumlah' => 25.00, 'satuan' => 'KG'],
            ['kode_barang' => '40005992', 'nama_material' => 'TALCUM POWDER:H2MG3O12SI4;379.27G/MOL', 'jumlah' => 5550.00, 'satuan' => 'KG'],
            ['kode_barang' => '40006381', 'nama_material' => 'FORMIC ACID,AR:CH2O2;90%', 'jumlah' => 1537.00, 'satuan' => 'KG'],
            ['kode_barang' => '91018606', 'nama_material' => 'Minyak Tanah', 'jumlah' => 670.00, 'satuan' => 'L'],
            ['kode_barang' => '91057141', 'nama_material' => 'GASOLINE:BIODIESEL;CN 48;815-880KG/M3', 'jumlah' => 3650.00, 'satuan' => 'L'],
            ['kode_barang' => '82024963', 'nama_material' => 'RORED 140', 'jumlah' => 88.00, 'satuan' => 'EA'],
            ['kode_barang' => '91050493', 'nama_material' => 'LUBE OIL,ENG:SAE 10W;113 VISC', 'jumlah' => 209.00, 'satuan' => 'L'],
            ['kode_barang' => '91050494', 'nama_material' => 'LUBE OIL,ENG:SAE 40;98 VISC', 'jumlah' => 144.00, 'satuan' => 'L'],
            ['kode_barang' => '91054889', 'nama_material' => 'GREASE,EXTRM PRES:NLGI 2;LI-HYD', 'jumlah' => 196.00, 'satuan' => 'KG'],
            ['kode_barang' => '91055448', 'nama_material' => 'LUBE OIL,ENG:SAE 10W-40;161 VISC', 'jumlah' => 139.00, 'satuan' => 'L'],
            ['kode_barang' => '91055476', 'nama_material' => 'LUBE OIL,GEAR:SAE 140;96 VISC;GL-5', 'jumlah' => 209.00, 'satuan' => 'L'],
            ['kode_barang' => '82063569', 'nama_material' => 'MORTAR,REFRACTORY:SK34;1300DEG C;50KG', 'jumlah' => 4.00, 'satuan' => 'ZAK'],
            ['kode_barang' => '82078087', 'nama_material' => 'SCREEN:LATEX;AL;MULA-N007-001', 'jumlah' => 18.00, 'satuan' => 'EA'],
            ['kode_barang' => '91008594', 'nama_material' => 'KARET FULL', 'jumlah' => 2.60, 'satuan' => 'KG'],
            ['kode_barang' => '82039518', 'nama_material' => 'Over Load 10 A-15 A', 'jumlah' => 1.00, 'satuan' => 'PC'],
            ['kode_barang' => '91062075', 'nama_material' => 'LAMP,INCAND:HALOGEN;250W;220V;R7S;YLW', 'jumlah' => 4.00, 'satuan' => 'EA'],
            ['kode_barang' => '91033458', 'nama_material' => 'Ember Kaleng 20 Ltr', 'jumlah' => 68.00, 'satuan' => 'PC'],
            ['kode_barang' => '91033657', 'nama_material' => 'PISAU SADAP SODECI', 'jumlah' => 421.00, 'satuan' => 'PC'],
            ['kode_barang' => '91050116', 'nama_material' => 'KNIFE,LATEX:TOP;20X130X165MM', 'jumlah' => 1254.00, 'satuan' => 'EA'],
            ['kode_barang' => '91050119', 'nama_material' => 'KNIFE,LATEX:BTM;40X230X370MM;WOOD HDL', 'jumlah' => 20.00, 'satuan' => 'EA'],
            ['kode_barang' => '91050469', 'nama_material' => 'GUTTER,LATEX:V-SHP;0.2x30x70MM;BJLS;7T', 'jumlah' => 658000.00, 'satuan' => 'EA'],
            ['kode_barang' => '91055778', 'nama_material' => 'STONE,SHARP:TPR;9.5X45X120MM;FINE', 'jumlah' => 19.00, 'satuan' => 'EA'],
            ['kode_barang' => '91067898', 'nama_material' => 'HANGER,BOWL,LATEX:RD;15CM DIA;WIRE', 'jumlah' => 93000.00, 'satuan' => 'EA'],
            ['kode_barang' => '91068055', 'nama_material' => 'HANGER,BOWL,LATEX:RD;10CM DIA;WIRE', 'jumlah' => 169000.00, 'satuan' => 'EA'],
            ['kode_barang' => '40006686', 'nama_material' => 'AMMONIA,TECH:NH3;MIN 20%;LIQ', 'jumlah' => 115.00, 'satuan' => 'L'],
            ['kode_barang' => '91037872', 'nama_material' => 'PLASTIK LATEK', 'jumlah' => 642.00, 'satuan' => 'SHT'],
            ['kode_barang' => '91037876', 'nama_material' => 'TALI PLASTIK/ STRAPING BAND', 'jumlah' => 304.00, 'satuan' => 'ROL'],
            ['kode_barang' => '91066744', 'nama_material' => 'STRAPPING:15MM;PP;120M/ROL', 'jumlah' => 359.00, 'satuan' => 'ROL'],
            ['kode_barang' => '91037877', 'nama_material' => 'CAT KAYU WARNA KUNING', 'jumlah' => 8.00, 'satuan' => 'CAN'],
            ['kode_barang' => '91050213', 'nama_material' => 'ELECTRODE,WELD:3.2MM;AWS A5.1 E6013;MS', 'jumlah' => 20.00, 'satuan' => 'KG'],
            ['kode_barang' => '91066304', 'nama_material' => 'HOSE,NONMTL:4IN;PVC;5MPA;BLUE;SUCTN', 'jumlah' => 23.00, 'satuan' => 'M'],
            ['kode_barang' => '82057481', 'nama_material' => 'ADAPTER,BEARING:THD;40X65X33MM;H209', 'jumlah' => 3.00, 'satuan' => 'EA'],
            ['kode_barang' => '82057917', 'nama_material' => 'BEARING,RLR:SPHER;TPR;40MM;ST;22208;C3', 'jumlah' => 24.00, 'satuan' => 'EA'],
            ['kode_barang' => '82066379', 'nama_material' => 'BEARING,BALL:DG;1R;50MM;ST;6310;2SHLD;C3', 'jumlah' => 2.00, 'satuan' => 'EA'],
            ['kode_barang' => '82066425', 'nama_material' => 'BEARING,BALL,SA:TPR;45MM;ST;1209;C3', 'jumlah' => 18.00, 'satuan' => 'EA'],
            ['kode_barang' => '82066449', 'nama_material' => 'BEARING,BALL:DG;1R;40MM;ST;6308;2SHLD;C3', 'jumlah' => 1.00, 'satuan' => 'EA'],
            ['kode_barang' => '82066454', 'nama_material' => 'BEARING,BALL:DG;1R;45MM;ST;6409;2SHLD;C3', 'jumlah' => 2.00, 'satuan' => 'EA'],
        ];

        $dataInsert = array_map(function ($item) use ($tanggal, $jam, $bulan, $now) {
            return array_merge($item, [
                'tanggal_transaksi' => $tanggal,
                'jam_input'         => $jam,
                'bulan_simpanan'    => $bulan,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }, $materials);

        DB::table('material_masuk')->insert($dataInsert);
    }
}