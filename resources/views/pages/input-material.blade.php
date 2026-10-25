<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stock Gudang - Input Material Masuk</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#F4F6F9] text-slate-800 flex flex-col min-h-screen font-sans md:h-screen md:flex-row md:overflow-hidden">

    @include('components.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-white px-4 py-4 flex flex-col gap-3 border-b border-slate-200/80 sticky top-0 z-10 shadow-sm md:px-8 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Form Material Masuk Gudang</h2>
                <p class="text-xs text-slate-400">Pencatatan material masuk berdasarkan Kode Material & sinkronisasi database</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <div id="selectedOperatorDisplay" class="text-xs bg-white px-3 py-1 rounded-lg border border-slate-200 font-medium text-slate-700 hidden">
                    Operator: <span id="selectedOperatorName">-</span>
                    <button id="clearOperator" type="button" class="ml-2 text-xs text-rose-600">(Hapus)</button>
                </div>

                <div class="text-xs bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200 font-mono font-bold text-slate-600 flex items-center gap-2">
                    <i class="fa-solid fa-clock text-blue-600"></i>
                    <span id="liveClock">00:00:00</span>
                </div>
            </div>
        </header>

        <div class="p-4 max-w-7xl w-full mx-auto space-y-8 md:p-8">
            
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md overflow-hidden">
                <div class="p-6 pb-4 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800">Tambah Transaksi Material</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Pilih Kode Material untuk autofill Nama & Satuan secara otomatis</p>
                    </div>
                    <span class="text-xs bg-blue-50 text-blue-600 border border-blue-200/60 px-3 py-1 rounded-full font-bold flex items-center space-x-1.5">
                        <i class="fa-solid fa-bolt text-[10px]"></i>
                        <span>Auto-Fill Active</span>
                    </span>
                </div>

                <div id="saveSuccess" class="hidden mx-8 mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700" role="status">
                    <i class="fa-solid fa-circle-check mr-2"></i><span></span>
                </div>

                <form id="formMaterialMasuk" onsubmit="handleMaterialSubmit(event)" class="p-8 space-y-5">
                    <input type="hidden" name="operator_id" id="operator_id" value="">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">1. Kode Barang</label>
                            <div class="relative">
                                <i class="fa-solid fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" id="inputKodeBarang" list="listMaterial" oninput="autoFillMaterial(this.value)" placeholder="Ketik Kode (Contoh: 40005914)" required 
                                    class="w-full bg-slate-50 border border-slate-200 text-sm pl-11 pr-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition font-mono font-bold uppercase">
                                
                                <datalist id="listMaterial">
                                    <option value="40005914">FERTILIZER:TSP;45% P2O5 MIN;GRANUL (KG)</option>
                                    <option value="40005942">FERTILIZER:UREA;46% N;GRANUL (KG)</option>
                                    <option value="40005946">FERTILIZER:KIESERITE;25% MGO;21% S (KG)</option>
                                    <option value="40005947">FERTILIZER:KCL (MOP);60% K2O;CRYTL (KG)</option>
                                    <option value="40006222">FERTILIZER:NPK 16-16-16;GRANUL (KG)</option>
                                    <option value="40000214">EMULGATOR (L)</option>
                                    <option value="40002897">Tb.192 (EA)</option>
                                    <option value="40005846">SURFACTANT:ADDITIVE X-EC/100;LIQUID (L)</option>
                                    <option value="40005850">HERBICIDE:FLUROXYPYR MH ESTER 480G/L;EC (L)</option>
                                    <option value="40005867">HERBICIDE:METHYL METSULFURON 20%;WG (KG)</option>
                                    <option value="40005952">FUNGICIDE:HEXACONAZOLE 50 G/L;SC (L)</option>
                                    <option value="40005966">PLANT,GROWTH:ETEFON 10%;SL (L)</option>
                                    <option value="40006339">HERBICIDE:TRIKLOPIR 480G/L;SL (L)</option>
                                    <option value="40006776">HERBICIDE:GLYPHOSATE IPA 480G/L;SL;1L (L)</option>
                                    <option value="40007021">FUNGICIDE:DITHANE M-45;80 WP;PW;1 KG (KG)</option>
                                    <option value="40000210">CAUSTIC SODA (KG)</option>
                                    <option value="40005992">TALCUM POWDER:H2MG3O12SI4;379.27G/MOL (KG)</option>
                                    <option value="40006381">FORMIC ACID,AR:CH2O2;90% (KG)</option>
                                    <option value="91018606">Minyak Tanah (L)</option>
                                    <option value="91057141">GASOLINE:BIODIESEL;CN 48;815-880KG/M3 (L)</option>
                                    <option value="82024963">RORED 140 (EA)</option>
                                    <option value="91050493">LUBE OIL,ENG:SAE 10W;113 VISC (L)</option>
                                    <option value="91050494">LUBE OIL,ENG:SAE 40;98 VISC (L)</option>
                                    <option value="91054889">GREASE,EXTRM PRES:NLGI 2;LI-HYD (KG)</option>
                                    <option value="91055448">LUBE OIL,ENG:SAE 10W-40;161 VISC (L)</option>
                                    <option value="91055476">LUBE OIL,GEAR:SAE 140;96 VISC;GL-5 (L)</option>
                                    <option value="82063569">MORTAR,REFRACTORY:SK34;1300DEG C;50KG (ZAK)</option>
                                    <option value="82078087">SCREEN:LATEX;AL;MULA-N007-001 (EA)</option>
                                    <option value="91008594">KARET FULL (KG)</option>
                                    <option value="82039518">Over Load 10 A-15 A (PC)</option>
                                    <option value="91062075">LAMP,INCAND:HALOGEN;250W;220V;R7S;YLW (EA)</option>
                                    <option value="91033458">Ember Kaleng 20 Ltr (PC)</option>
                                    <option value="91033657">PISAU SADAP SODECI (PC)</option>
                                    <option value="91050116">KNIFE,LATEX:TOP;20X130X165MM (EA)</option>
                                    <option value="91050119">KNIFE,LATEX:BTM;40X230X370MM;WOOD HDL (EA)</option>
                                    <option value="91050469">GUTTER,LATEX:V-SHP;0.2x30x70MM;BJLS;7T (EA)</option>
                                    <option value="91055778">STONE,SHARP:TPR;9.5X45X120MM;FINE (EA)</option>
                                    <option value="91067898">HANGER,BOWL,LATEX:RD;15CM DIA;WIRE (EA)</option>
                                    <option value="91068055">HANGER,BOWL,LATEX:RD;10CM DIA;WIRE (EA)</option>
                                    <option value="40006686">AMMONIA,TECH:NH3;MIN 20%;LIQ (L)</option>
                                    <option value="91037872">PLASTIK LATEK (SHT)</option>
                                    <option value="91037876">TALI PLASTIK/ STRAPING BAND (ROL)</option>
                                    <option value="91066744">STRAPPING:15MM;PP;120M/ROL (ROL)</option>
                                    <option value="91037877">CAT KAYU WARNA KUNING (CAN)</option>
                                    <option value="91050213">ELECTRODE,WELD:3.2MM;AWS A5.1 E6013;MS (KG)</option>
                                    <option value="91066304">HOSE,NONMTL:4IN;PVC;5MPA;BLUE;SUCTN (M)</option>
                                    <option value="82057481">ADAPTER,BEARING:THD;40X65X33MM;H209 (EA)</option>
                                    <option value="82057917">BEARING,RLR:SPHER;TPR;40MM;ST;22208;C3 (EA)</option>
                                    <option value="82066379">BEARING,BALL:DG;1R;50MM;ST;6310;2SHLD;C3 (EA)</option>
                                    <option value="82066425">BEARING,BALL,SA:TPR;45MM;ST;1209;C3 (EA)</option>
                                    <option value="82066449">BEARING,BALL:DG;1R;40MM;ST;6308;2SHLD;C3 (EA)</option>
                                    <option value="82066454">BEARING,BALL:DG;1R;45MM;ST;6409;2SHLD;C3 (EA)</option>
                                </datalist>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">2. Nama Material</label>
                            <div class="relative">
                                <i class="fa-solid fa-box-open absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" id="inputNamaMaterial" placeholder="Nama material otomatis terisi..." required 
                                    class="w-full bg-slate-100 border border-slate-200 text-sm pl-11 pr-4 py-3 rounded-xl font-semibold text-slate-700">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">3. Jumlah Masuk</label>
                            <div class="relative">
                                <i class="fa-solid fa-arrow-up-1-9 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="number" id="inputJumlah" step="0.01" min="0.01" required placeholder="0.00" 
                                    class="w-full bg-slate-50 border border-slate-200 text-sm pl-11 pr-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition font-mono font-bold text-emerald-600">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">4. Satuan</label>
                            <div class="relative">
                                <i class="fa-solid fa-layer-group absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
                                <select id="inputSatuan" required 
                                    class="w-full bg-slate-50 border border-slate-200 text-sm pl-11 pr-10 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition font-semibold appearance-none text-slate-700">
                                    <option value="" disabled selected>Pilih Satuan</option>
                                    <option value="KG">KG</option>
                                    <option value="Liter">Liter</option>
                                    <option value="EA">EA</option>
                                    <option value="ZAK">ZAK</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">5. TGGL, BLN, THN</label>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="relative">
                                    <input type="number" id="inputTanggalDay" min="1" max="31" value="02" onchange="updateBulanSimpanan()" required class="w-full bg-slate-50 border border-slate-200 text-sm px-3 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-center font-semibold" placeholder="02">
                                </div>
                                <div class="relative">
                                    <input type="number" id="inputTanggalMonth" min="1" max="12" value="09" onchange="updateBulanSimpanan()" required class="w-full bg-slate-50 border border-slate-200 text-sm px-3 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-center font-semibold" placeholder="09">
                                </div>
                                <div class="relative">
                                    <input type="number" id="inputTanggalYear" min="2000" max="2100" value="2026" onchange="updateBulanSimpanan()" required class="w-full bg-slate-50 border border-slate-200 text-sm px-3 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-center font-semibold" placeholder="2026">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">6. Jam Input</label>
                            <div class="relative">
                                <i class="fa-solid fa-clock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" id="inputJam" readonly 
                                    class="w-full bg-slate-100 border border-slate-200 text-sm pl-11 pr-4 py-3 rounded-xl font-mono text-slate-500 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">7. Bulan Simpanan</label>
                            <div class="relative">
                                <i class="fa-solid fa-calendar-check absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 text-sm z-10"></i>
                                <select id="inputBulanSimpanan" required 
                                    class="w-full bg-blue-50/70 border border-blue-200 text-sm pl-11 pr-10 py-3 rounded-xl font-bold text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition appearance-none">
                                    <option value="" disabled>Pilih Bulan</option>
                                    <option value="Januari">Januari</option>
                                    <option value="Februari">Februari</option>
                                    <option value="Maret">Maret</option>
                                    <option value="April">April</option>
                                    <option value="Mei">Mei</option>
                                    <option value="Juni">Juni</option>
                                    <option value="Juli">Juli</option>
                                    <option value="Agustus">Agustus</option>
                                    <option value="September">September</option>
                                    <option value="Oktober">Oktober</option>
                                    <option value="November">November</option>
                                    <option value="Desember">Desember</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-blue-500 text-xs pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="flex items-end">
                            <button id="saveMaterialButton" type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-blue-500/25 transition flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-plus"></i>
                                <span>Simpan ke Database</span>
                            </button>
                        </div>
                    </div>

                </form>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md p-6 space-y-4">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800">Riwayat Material Masuk</h3>
                        <p class="text-xs text-slate-400">Daftar transaksi material masuk yang tersimpan di database</p>
                    </div>
                    
                    <div class="flex flex-col gap-2 w-full md:w-auto md:flex-row md:items-center">
                        <label class="relative w-full md:w-64">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input id="cariMaterial" type="search" oninput="renderTable()" placeholder="Cari material..."
                                aria-label="Cari material berdasarkan kode atau nama"
                                class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 font-medium text-sm focus:ring-2 focus:ring-blue-500 bg-slate-50">
                        </label>
                        <div class="flex items-center gap-2">
                            <label for="filterBulan" class="text-xs font-bold text-slate-500 uppercase whitespace-nowrap">Filter Bulan:</label>
                            <select id="filterBulan" onchange="renderTable()"
                                class="px-3 py-2 rounded-xl border border-slate-200 font-semibold text-sm focus:ring-2 focus:ring-blue-500 bg-slate-50">
                                <option value="Semua">Semua Bulan</option>
                                <option value="Januari">Januari</option>
                                <option value="Februari">Februari</option>
                                <option value="Maret">Maret</option>
                                <option value="April">April</option>
                                <option value="Mei">Mei</option>
                                <option value="Juni">Juni</option>
                                <option value="Juli">Juli</option>
                                <option value="Agustus">Agustus</option>
                                <option value="September">September</option>
                                <option value="Oktober">Oktober</option>
                                <option value="November">November</option>
                                <option value="Desember">Desember</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="exportPdf()"
                                class="px-3 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm transition whitespace-nowrap">
                                <i class="fa-solid fa-file-pdf mr-1"></i>PDF
                            </button>
                            <button type="button" onclick="exportExcel()"
                                class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition whitespace-nowrap">
                                <i class="fa-solid fa-file-excel mr-1"></i>Excel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-4 text-center">No</th>
                                <th class="py-3.5 px-4">Nomor Material</th>
                                <th class="py-3.5 px-4">Nama Material</th>
                                <th class="py-3.5 px-4 text-center">Jumlah</th>
                                <th class="py-3.5 px-4 text-center">Satuan</th>
                                <th class="py-3.5 px-4">Tanggal</th>
                                <th class="py-3.5 px-4 text-center">Jam</th>
                                <th class="py-3.5 px-4 text-center">Bulan</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        // Data Master 52 Nomor Material Kebun Kedaton
        const masterBarang = {
            "40005914": { nama: "FERTILIZER:TSP;45% P2O5 MIN;GRANUL", satuan: "KG" },
            "40005942": { nama: "FERTILIZER:UREA;46% N;GRANUL", satuan: "KG" },
            "40005946": { nama: "FERTILIZER:KIESERITE;25% MGO;21% S", satuan: "KG" },
            "40005947": { nama: "FERTILIZER:KCL (MOP);60% K2O;CRYTL", satuan: "KG" },
            "40006222": { nama: "FERTILIZER:NPK 16-16-16;GRANUL", satuan: "KG" },
            "40000214": { nama: "EMULGATOR", satuan: "Liter" },
            "40002897": { nama: "Tb.192", satuan: "EA" },
            "40005846": { nama: "SURFACTANT:ADDITIVE X-EC/100;LIQUID", satuan: "Liter" },
            "40005850": { nama: "HERBICIDE:FLUROXYPYR MH ESTER 480G/L;EC", satuan: "Liter" },
            "40005867": { nama: "HERBICIDE:METHYL METSULFURON 20%;WG", satuan: "KG" },
            "40005952": { nama: "FUNGICIDE:HEXACONAZOLE 50 G/L;SC", satuan: "Liter" },
            "40005966": { nama: "PLANT,GROWTH:ETEFON 10%;SL", satuan: "Liter" },
            "40006339": { nama: "HERBICIDE:TRIKLOPIR 480G/L;SL", satuan: "Liter" },
            "40006776": { nama: "HERBICIDE:GLYPHOSATE IPA 480G/L;SL;1L", satuan: "Liter" },
            "40007021": { nama: "FUNGICIDE:DITHANE M-45;80 WP;PW;1 KG", satuan: "KG" },
            "40000210": { nama: "CAUSTIC SODA", satuan: "KG" },
            "40005992": { nama: "TALCUM POWDER:H2MG3O12SI4;379.27G/MOL", satuan: "KG" },
            "40006381": { nama: "FORMIC ACID,AR:CH2O2;90%", satuan: "KG" },
            "91018606": { nama: "Minyak Tanah", satuan: "Liter" },
            "91057141": { nama: "GASOLINE:BIODIESEL;CN 48;815-880KG/M3", satuan: "Liter" },
            "82024963": { nama: "RORED 140", satuan: "EA" },
            "91050493": { nama: "LUBE OIL,ENG:SAE 10W;113 VISC", satuan: "Liter" },
            "91050494": { nama: "LUBE OIL,ENG:SAE 40;98 VISC", satuan: "Liter" },
            "91054889": { nama: "GREASE,EXTRM PRES:NLGI 2;LI-HYD", satuan: "KG" },
            "91055448": { nama: "LUBE OIL,ENG:SAE 10W-40;161 VISC", satuan: "Liter" },
            "91055476": { nama: "LUBE OIL,GEAR:SAE 140;96 VISC;GL-5", satuan: "Liter" },
            "82063569": { nama: "MORTAR,REFRACTORY:SK34;1300DEG C;50KG", satuan: "ZAK" },
            "82078087": { nama: "SCREEN:LATEX;AL;MULA-N007-001", satuan: "EA" },
            "91008594": { nama: "KARET FULL", satuan: "KG" },
            "82039518": { nama: "Over Load 10 A-15 A", satuan: "EA" },
            "91062075": { nama: "LAMP,INCAND:HALOGEN;250W;220V;R7S;YLW", satuan: "EA" },
            "91033458": { nama: "Ember Kaleng 20 Ltr", satuan: "EA" },
            "91033657": { nama: "PISAU SADAP SODECI", satuan: "EA" },
            "91050116": { nama: "KNIFE,LATEX:TOP;20X130X165MM", satuan: "EA" },
            "91050119": { nama: "KNIFE,LATEX:BTM;40X230X370MM;WOOD HDL", satuan: "EA" },
            "91050469": { nama: "GUTTER,LATEX:V-SHP;0.2x30x70MM;BJLS;7T", satuan: "EA" },
            "91055778": { nama: "STONE,SHARP:TPR;9.5X45X120MM;FINE", satuan: "EA" },
            "91067898": { nama: "HANGER,BOWL,LATEX:RD;15CM DIA;WIRE", satuan: "EA" },
            "91068055": { nama: "HANGER,BOWL,LATEX:RD;10CM DIA;WIRE", satuan: "EA" },
            "40006686": { nama: "AMMONIA,TECH:NH3;MIN 20%;LIQ", satuan: "Liter" },
            "91037872": { nama: "PLASTIK LATEK", satuan: "EA" },
            "91037876": { nama: "TALI PLASTIK/ STRAPING BAND", satuan: "EA" },
            "91066744": { nama: "STRAPPING:15MM;PP;120M/ROL", satuan: "EA" },
            "91037877": { nama: "CAT KAYU WARNA KUNING", satuan: "EA" },
            "91050213": { nama: "ELECTRODE,WELD:3.2MM;AWS A5.1 E6013;MS", satuan: "KG" },
            "91066304": { nama: "HOSE,NONMTL:4IN;PVC;5MPA;BLUE;SUCTN", satuan: "EA" },
            "82057481": { nama: "ADAPTER,BEARING:THD;40X65X33MM;H209", satuan: "EA" },
            "82057917": { nama: "BEARING,RLR:SPHER;TPR;40MM;ST;22208;C3", satuan: "EA" },
            "82066379": { nama: "BEARING,BALL:DG;1R;50MM;ST;6310;2SHLD;C3", satuan: "EA" },
            "82066425": { nama: "BEARING,BALL,SA:TPR;45MM;ST;1209;C3", satuan: "EA" },
            "82066449": { nama: "BEARING,BALL:DG;1R;40MM;ST;6308;2SHLD;C3", satuan: "EA" },
            "82066454": { nama: "BEARING,BALL:DG;1R;45MM;ST;6409;2SHLD;C3", satuan: "EA" }
        };

        const daftarBulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let historyData = [];
        let historyRequestId = 0;

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById('inputTanggalDay').value = '02';
            document.getElementById('inputTanggalMonth').value = '09';
            document.getElementById('inputTanggalYear').value = '2026';
            updateBulanSimpanan();

            setInterval(updateClock, 1000);
            updateClock();

            const currentMonthIndex = new Date().getMonth();
            document.getElementById('filterBulan').value = daftarBulan[currentMonthIndex];

            renderTable();

            // initialize selected operator if passed via query or localStorage
            initSelectedOperator();
        });

        function autoFillMaterial(kode) {
            const cleanKode = kode.trim();
            if (masterBarang[cleanKode]) {
                document.getElementById('inputNamaMaterial').value = masterBarang[cleanKode].nama;
                document.getElementById('inputSatuan').value = masterBarang[cleanKode].satuan;
            } else {
                document.getElementById('inputNamaMaterial').value = '';
                document.getElementById('inputSatuan').value = '';
            }
        }

        function formatDateFromParts() {
            const day = String(document.getElementById('inputTanggalDay').value || '01').padStart(2, '0');
            const month = String(document.getElementById('inputTanggalMonth').value || '01').padStart(2, '0');
            const year = document.getElementById('inputTanggalYear').value || '2026';
            return `${year}-${month}-${day}`;
        }

        function updateBulanSimpanan() {
            const month = parseInt(document.getElementById('inputTanggalMonth').value || '1', 10);
            if (month >= 1 && month <= 12) {
                document.getElementById('inputBulanSimpanan').value = daftarBulan[month - 1];
            }
        }

        function updateClock() {
            const now = new Date();
            const timeStr = now.toTimeString().split(' ')[0];
            document.getElementById('inputJam').value = timeStr;
            document.getElementById('liveClock').innerText = timeStr;
        }

        // Initialize selected operator from query param or localStorage
        function initSelectedOperator(){
            const params = new URLSearchParams(window.location.search);
            let operatorId = params.get('operator');
            let operatorName = null;

            if(!operatorId){
                operatorId = localStorage.getItem('selectedOperatorId');
                operatorName = localStorage.getItem('selectedOperatorName');
            }

            if(operatorId){
                document.getElementById('operator_id').value = operatorId;
                if(!operatorName){
                    // try to find an element on page with operator data
                    const avatar = document.querySelector('.operator-select[data-name]');
                    if(avatar) operatorName = avatar.dataset.name;
                }
                if(operatorName){
                    const disp = document.getElementById('selectedOperatorDisplay');
                    document.getElementById('selectedOperatorName').textContent = operatorName;
                    disp.classList.remove('hidden');
                }
            }

            const clearBtn = document.getElementById('clearOperator');
            if(clearBtn){
                clearBtn.addEventListener('click', ()=>{
                    localStorage.removeItem('selectedOperatorId');
                    localStorage.removeItem('selectedOperatorName');
                    document.getElementById('operator_id').value = '';
                    const disp = document.getElementById('selectedOperatorDisplay');
                    document.getElementById('selectedOperatorName').textContent = '-';
                    disp.classList.add('hidden');
                });
            }
        }

        async function handleMaterialSubmit(event) {
            event.preventDefault();
            const saveButton = document.getElementById('saveMaterialButton');
            if (saveButton.disabled) return;

            saveButton.disabled = true;
            saveButton.classList.add('cursor-not-allowed', 'opacity-70');
            saveButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Menyimpan...</span>';

            const payload = {
                kode: document.getElementById('inputKodeBarang').value.trim(),
                nama: document.getElementById('inputNamaMaterial').value || "Material Non-Master",
                jumlah: parseFloat(document.getElementById('inputJumlah').value),
                quantity: parseFloat(document.getElementById('inputJumlah').value),
                satuan: document.getElementById('inputSatuan').value || "KG",
                tanggal: formatDateFromParts(),
                jam: document.getElementById('inputJam').value,
                bulan: document.getElementById('inputBulanSimpanan').value,
                operator_id: document.getElementById('operator_id').value || null
            };

            try {
                const response = await fetch('/material-masuk', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                if (response.ok) {
                    const result = await response.json();
                    const success = document.getElementById('saveSuccess');
                    success.querySelector('span').textContent = `Material masuk tersimpan di database. Stok terbaru: ${result.stock}.`;
                    success.classList.remove('hidden');
                    document.getElementById('filterBulan').value = payload.bulan;
                    document.getElementById('cariMaterial').value = '';
                    renderTable();

                    document.getElementById('inputKodeBarang').value = '';
                    document.getElementById('inputNamaMaterial').value = '';
                    document.getElementById('inputJumlah').value = '';
                    document.getElementById('inputSatuan').value = '';
                    document.getElementById('inputTanggalDay').value = '02';
                    document.getElementById('inputTanggalMonth').value = '09';
                    document.getElementById('inputTanggalYear').value = '2026';
                    updateBulanSimpanan();
                } else {
                    const errData = await response.json();
                    alert("Gagal menyimpan data: " + (errData.message || "Terjadi kesalahan server"));
                }
            } catch (error) {
                console.error('Gagal menyimpan ke database:', error);
                alert("Terjadi masalah koneksi ke server.");
            } finally {
                saveButton.disabled = false;
                saveButton.classList.remove('cursor-not-allowed', 'opacity-70');
                saveButton.innerHTML = '<i class="fa-solid fa-plus"></i><span>Simpan ke Database</span>';
            }
        }

        async function renderTable() {
            const requestId = ++historyRequestId;
            const filter = document.getElementById('filterBulan').value;
            const search = document.getElementById('cariMaterial').value.trim();
            const tbody = document.getElementById('tableBody');
            const params = new URLSearchParams({ bulan: filter, cari: search });

            try {
                const response = await fetch(`/material-masuk?${params.toString()}`);
                if (!response.ok) {
                    throw new Error(`Server mengembalikan status ${response.status}`);
                }
                const data = await response.json();
                if (requestId !== historyRequestId) return;
                historyData = Array.isArray(data) ? data : [];

                if (historyData.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="py-6 text-center text-slate-400 font-medium">Tidak ada riwayat material yang cocok dengan pencarian dan filter.</td></tr>`;
                    return;
                }

                tbody.innerHTML = historyData.map((item, index) => `
                    <tr class="hover:bg-slate-50 transition border-b border-slate-100">
                        <td class="py-3 px-4 font-bold text-slate-400 text-center">${index + 1}</td>
                        <td class="py-3 px-4 font-mono font-bold text-blue-600">${escapeHtml(item.kode_barang)}</td>
                        <td class="py-3 px-4 font-bold text-slate-800">${escapeHtml(item.nama_material)}</td>
                        <td class="py-3 px-4 text-center font-mono font-black text-emerald-600">+${escapeHtml(item.jumlah)}</td>
                        <td class="py-3 px-4 text-center font-semibold text-slate-500">${escapeHtml(item.satuan)}</td>
                        <td class="py-3 px-4 text-slate-600 font-medium">${escapeHtml(item.tanggal_transaksi)}</td>
                        <td class="py-3 px-4 text-center font-mono font-bold text-slate-600 bg-slate-50 rounded-lg">${escapeHtml(item.jam_input)}</td>
                        <td class="py-3 px-4 text-center">
                            <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full border border-blue-200">${escapeHtml(item.bulan_simpanan)}</span>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                if (requestId !== historyRequestId) return;
                console.error('Gagal mengambil data database:', error);
                historyData = [];
                tbody.innerHTML = '<tr><td colspan="8" class="py-6 text-center text-rose-600 font-medium">Riwayat gagal dimuat. Silakan coba lagi.</td></tr>';
            }
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, character => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            })[character]);
        }

        function exportExcel() {
            const params = new URLSearchParams({
                bulan: document.getElementById('filterBulan').value,
                cari: document.getElementById('cariMaterial').value.trim()
            });
            window.location.href = `/material-masuk/export/excel?${params.toString()}`;
        }

        function exportPdf() {
            if (historyData.length === 0) {
                alert('Tidak ada riwayat material untuk diekspor.');
                return;
            }

            const printWindow = window.open('', '_blank');
            if (!printWindow) {
                alert('Jendela PDF diblokir browser. Izinkan pop-up untuk situs ini lalu coba lagi.');
                return;
            }

            const rows = historyData.map((item, index) => `
                <tr>
                    <td>${index + 1}</td>
                    <td>${escapeHtml(item.kode_barang)}</td>
                    <td>${escapeHtml(item.nama_material)}</td>
                    <td>${escapeHtml(item.jumlah)}</td>
                    <td>${escapeHtml(item.satuan)}</td>
                    <td>${escapeHtml(item.tanggal_transaksi)}</td>
                    <td>${escapeHtml(item.jam_input)}</td>
                    <td>${escapeHtml(item.bulan_simpanan)}</td>
                </tr>
            `).join('');
            const filter = escapeHtml(document.getElementById('filterBulan').value);
            const search = escapeHtml(document.getElementById('cariMaterial').value.trim());

            printWindow.document.write(`<!DOCTYPE html>
                <html lang="id">
                <head>
                    <meta charset="UTF-8">
                    <title>Riwayat Material Masuk</title>
                    <style>
                        body { font: 12px Arial, sans-serif; color: #1e293b; padding: 24px; }
                        h1 { font-size: 20px; margin-bottom: 6px; }
                        p { color: #475569; margin: 4px 0; }
                        table { border-collapse: collapse; width: 100%; margin-top: 18px; }
                        th, td { border: 1px solid #cbd5e1; padding: 7px; text-align: left; }
                        th { background: #f1f5f9; }
                        @media print { body { padding: 0; } }
                    </style>
                </head>
                <body>
                    <h1>Riwayat Material Masuk</h1>
                    <p>Filter bulan: ${filter}</p>
                    <p>Pencarian: ${search || '-'}</p>
                    <table>
                        <thead><tr><th>No</th><th>Nomor Material</th><th>Nama Material</th><th>Jumlah</th><th>Satuan</th><th>Tanggal</th><th>Jam</th><th>Bulan</th></tr></thead>
                        <tbody>${rows}</tbody>
                    </table>
                </body>
                </html>`);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => printWindow.print(), 250);
        }

    </script>
</body>
</html>
