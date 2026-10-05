<?php

namespace Database\Seeders;

use App\Models\Material;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            ['kode' => '40005914', 'nama' => 'FERTILIZER:TSP;45% P2O5 MIN;GRANUL', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 32505],
            ['kode' => '40005942', 'nama' => 'FERTILIZER:UREA;46% N;GRANUL', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 6704],
            ['kode' => '40005946', 'nama' => 'FERTILIZER:KIESERITE;25% MGO;21% S', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 6912],
            ['kode' => '40005947', 'nama' => 'FERTILIZER:KCL (MOP);60% K2O;CRYTL', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 5641],
            ['kode' => '40006222', 'nama' => 'FERTILIZER:NPK 16-16-16;GRANUL', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 209800],
            ['kode' => '40000214', 'nama' => 'EMULGATOR', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 125.1],
            ['kode' => '40002897', 'nama' => 'Tb.192', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 270],
            ['kode' => '40005846', 'nama' => 'SURFACTANT:ADDITIVE X-EC/100;LIQUID', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 166],
            ['kode' => '40005850', 'nama' => 'HERBICIDE:FLUROXYPYR MH ESTER 480G/L;EC', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 510],
            ['kode' => '40005867', 'nama' => 'HERBICIDE:METHYL METSULFURON 20%;WG', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 1447.4],
            ['kode' => '40005952', 'nama' => 'FUNGICIDE:HEXACONAZOLE 50 G/L;SC', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 1445.8],
            ['kode' => '40005966', 'nama' => 'PLANT,GROWTH:ETEFON 10%;SL', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 2423.7],
            ['kode' => '40006339', 'nama' => 'HERBICIDE:TRIKLOPIR 480G/L;SL', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 335.7],
            ['kode' => '40006776', 'nama' => 'HERBICIDE:GLYPHOSATE IPA 480G/L;SL;1L', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 2625.8],
            ['kode' => '40007021', 'nama' => 'FUNGICIDE:DITHANE M-45;80 WP;PW;1 KG', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 1680],
            ['kode' => '40000210', 'nama' => 'CAUSTIC SODA', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 25],
            ['kode' => '40005992', 'nama' => 'TALCUM POWDER:H2MG3O12SI4;379.27G/MOL', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 5550],
            ['kode' => '40006381', 'nama' => 'FORMIC ACID,AR:CH2O2;90%', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 1537],
            ['kode' => '91018606', 'nama' => 'Minyak Tanah', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 670],
            ['kode' => '91057141', 'nama' => 'GASOLINE:BIODIESEL;CN 48;815-880KG/M3', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 3650],
            ['kode' => '82024963', 'nama' => 'RORED 140', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 88],
            ['kode' => '91050493', 'nama' => 'LUBE OIL,ENG:SAE 10W;113 VISC', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 209],
            ['kode' => '91050494', 'nama' => 'LUBE OIL,ENG:SAE 40;98 VISC', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 144],
            ['kode' => '91054889', 'nama' => 'GREASE,EXTRM PRES:NLGI 2;LI-HYD', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 196],
            ['kode' => '91055448', 'nama' => 'LUBE OIL,ENG:SAE 10W-40;161 VISC', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 139],
            ['kode' => '91055476', 'nama' => 'LUBE OIL,GEAR:SAE 140;96 VISC;GL-5', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 209],
            ['kode' => '82063569', 'nama' => 'MORTAR,REFRACTORY:SK34;1300DEG C;50KG', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'ZAK', 'stok' => 4],
            ['kode' => '82078087', 'nama' => 'SCREEN:LATEX;AL;MULA-N007-001', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 18],
            ['kode' => '91008594', 'nama' => 'KARET FULL', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 2.6],
            ['kode' => '82039518', 'nama' => 'Over Load 10 A-15 A', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'PC', 'stok' => 1],
            ['kode' => '91062075', 'nama' => 'LAMP,INCAND:HALOGEN;250W;220V;R7S;YLW', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 4],
            ['kode' => '91033458', 'nama' => 'Ember Kaleng 20 Ltr', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'PC', 'stok' => 68],
            ['kode' => '91033657', 'nama' => 'PISAU SADAP SODECI', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'PC', 'stok' => 421],
            ['kode' => '91050116', 'nama' => 'KNIFE,LATEX:TOP;20X130X165MM', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 1254],
            ['kode' => '91050119', 'nama' => 'KNIFE,LATEX:BTM;40X230X370MM;WOOD HDL', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 20],
            ['kode' => '91050469', 'nama' => 'GUTTER,LATEX:V-SHP;0.2x30x70MM;BJLS;7T', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 658000],
            ['kode' => '91055778', 'nama' => 'STONE,SHARP:TPR;9.5X45X120MM;FINE', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 19],
            ['kode' => '91067898', 'nama' => 'HANGER,BOWL,LATEX:RD;15CM DIA;WIRE', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 93000],
            ['kode' => '91068055', 'nama' => 'HANGER,BOWL,LATEX:RD;10CM DIA;WIRE', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 169000],
            ['kode' => '40006686', 'nama' => 'AMMONIA,TECH:NH3;MIN 20%;LIQ', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'L', 'stok' => 115],
            ['kode' => '91037872', 'nama' => 'PLASTIK  LATEK', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'SHT', 'stok' => 642],
            ['kode' => '91037876', 'nama' => 'TALI PLASTIK/ STRAPING BAND', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'ROL', 'stok' => 304],
            ['kode' => '91066744', 'nama' => 'STRAPPING:15MM;PP;120M/ROL', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'ROL', 'stok' => 359],
            ['kode' => '91037877', 'nama' => 'CAT KAYU WARNA KUNING', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'CAN', 'stok' => 8],
            ['kode' => '91050213', 'nama' => 'ELECTRODE,WELD:3.2MM;AWS A5.1 E6013;MS', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'KG', 'stok' => 20],
            ['kode' => '91066304', 'nama' => 'HOSE,NONMTL:4IN;PVC;5MPA;BLUE;SUCTN', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'M', 'stok' => 23],
            ['kode' => '82057481', 'nama' => 'ADAPTER,BEARING:THD;40X65X33MM;H209', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 3],
            ['kode' => '82057917', 'nama' => 'BEARING,RLR:SPHER;TPR;40MM;ST;22208;C3', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 24],
            ['kode' => '82066379', 'nama' => 'BEARING,BALL:DG;1R;50MM;ST;6310;2SHLD;C3', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 2],
            ['kode' => '82066425', 'nama' => 'BEARING,BALL,SA:TPR;45MM;ST;1209;C3', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 18],
            ['kode' => '82066449', 'nama' => 'BEARING,BALL:DG;1R;40MM;ST;6308;2SHLD;C3', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 1],
            ['kode' => '82066454', 'nama' => 'BEARING,BALL:DG;1R;45MM;ST;6409;2SHLD;C3', 'kode_kebun' => '3K05', 'lokasi' => 'Gudang Material Kebun', 'satuan' => 'EA', 'stok' => 2],
        ];

        foreach ($items as $item) {
            Material::firstOrCreate(
                ['kode_barang' => $item['kode']],
                [
                    'nama_material' => $item['nama'],
                    'jumlah' => $item['stok'],
                    'stok_akhir' => $item['stok'],
                    'min_stok' => 10,
                    'satuan' => $item['satuan'],
                    'status' => ($item['stok'] <= 10) ? 'KRITIS' : 'NORMAL',
                    'tanggal_transaksi' => $now->toDateString(),
                    'jam_input' => $now->toTimeString(),
                    'bulan_simpanan' => $now->translatedFormat('F'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
