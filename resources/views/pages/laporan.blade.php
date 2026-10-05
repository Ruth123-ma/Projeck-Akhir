<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Barang Gudang Material Kebun Kedaton</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SheetJS CDN untuk Export Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body class="bg-[#F4F6F9] text-slate-800 flex flex-col min-h-screen font-sans overflow-hidden md:h-screen md:flex-row">
    <!-- Sidebar Component (Laravel Blade) -->
    @include('components.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto">
        <!-- Header Page -->
        <header class="bg-white px-4 py-4 flex flex-col gap-3 border-b border-slate-200/80 sticky top-0 z-10 shadow-sm md:px-8 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Laporan Mutasi & Stok Gudang Kebun Kedaton</h2>
                <p class="text-xs text-slate-400">Data otomatis terhubung langsung ke dashboard statistik digital</p>
            </div>
            
            <!-- Tombol Export PDF & EXCEL -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <button onclick="exportToPDF()" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-file-pdf text-sm"></i>
                    <span>Export PDF</span>
                </button>
                <button onclick="exportToExcel()" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-file-excel text-sm"></i>
                    <span>Export EXCEL</span>
                </button>
            </div>
        </header>

        <!-- Main Body Area -->
        <div class="p-4 max-w-7xl w-full mx-auto space-y-8 print:p-0 md:p-8">

            <!-- 1. DASHBOARD RINGKASAN STOK (3 KOLOM) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print:hidden">
                <!-- Total Jenis Barang -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Barang</p>
                        <h3 id="dashTotalBarang" class="text-2xl font-extrabold text-slate-800">0</h3>
                    </div>
                </div>

                <!-- Total Barang Masuk -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-circle-arrow-down"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Barang Masuk</p>
                        <h3 id="dashBarangMasuk" class="text-2xl font-extrabold text-emerald-600">0</h3>
                    </div>
                </div>

                <!-- Total Barang Keluar -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fa-solid fa-circle-arrow-up"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Barang Keluar</p>
                        <h3 id="dashBarangKeluar" class="text-2xl font-extrabold text-rose-600">0</h3>
                    </div>
                </div>
            </div>

            <!-- 2. TABEL RINCIAN DATA LAPORAN STOK -->
            <div id="printArea" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-base">Rincian Data Laporan Stok</h4>
                        <p class="text-xs text-slate-400">Rekapitulasi stok material gudang berdasarkan kalkulasi fisik</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span id="totalReportItemsBadge" class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-semibold print:hidden">0 Item</span>
                        <button onclick="clearAllData()" class="text-xs bg-rose-50 text-rose-600 hover:bg-rose-100 px-3 py-1 rounded-full font-semibold border border-rose-200 transition print:hidden">
                            <i class="fa-solid fa-trash-can mr-1"></i> Kosongkan Data
                        </button>
                    </div>
                </div>

                <!-- Empty State Placeholder -->
                <div id="emptyStateReport" class="hidden py-14 text-center flex flex-col items-center justify-center space-y-3">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 text-xl">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-bold text-slate-600">Belum Ada Rincian Data Laporan Stok</p>
                        <p class="text-xs text-slate-400">Tabel rincian akan tampil kembali setelah data dimasukkan secara digital.</p>
                    </div>
                </div>

                <!-- Tabel Output Data Laporan Stok -->
                <div id="tableContainerReport" class="overflow-x-auto">
                    <table id="laporanTable" class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-[11px] font-bold text-slate-400 uppercase border-b border-slate-100 bg-slate-50/50">
                                <th class="py-3.5 px-4 w-12 text-center">1. No</th>
                                <th class="py-3.5 px-4">2. No. Material</th>
                                <th class="py-3.5 px-4">3. Nama Barang</th>
                                <th class="py-3.5 px-4 text-center">4. Stock Awal</th>
                                <th class="py-3.5 px-4 text-center">5. Satuan</th>
                                <th class="py-3.5 px-4 text-center text-emerald-600">6. Masuk Barang</th>
                                <th class="py-3.5 px-4 text-center text-rose-600">7. Keluar Barang</th>
                                <th class="py-3.5 px-4 text-center text-blue-900 bg-blue-50/50">8. Stock Akhir</th>
                                <th class="py-3.5 px-4 text-center print:hidden">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody" class="divide-y divide-slate-100">
                            <!-- Data di-render via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- JavaScript Handler Engine -->
    <script>
        // Array memori 52 data material dari Gudang Kebun Kedaton
        let stockReports = [
            {"noMaterial": "40005914", "namaBarang": "FERTILIZER:TSP;45% P2O5 MIN;GRANUL", "stockAwal": 32505, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40005942", "namaBarang": "FERTILIZER:UREA;46% N;GRANUL", "stockAwal": 6704, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40005946", "namaBarang": "FERTILIZER:KIESERITE;25% MGO;21% S", "stockAwal": 6912, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40005947", "namaBarang": "FERTILIZER:KCL (MOP);60% K2O;CRYTL", "stockAwal": 5641, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40006222", "namaBarang": "FERTILIZER:NPK 16-16-16;GRANUL", "stockAwal": 209800, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40000214", "namaBarang": "EMULGATOR", "stockAwal": 125.1, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40002897", "namaBarang": "Tb.192", "stockAwal": 270, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40005846", "namaBarang": "SURFACTANT:ADDITIVE X-EC/100;LIQUID", "stockAwal": 166, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40005850", "namaBarang": "HERBICIDE:FLUROXYPYR MH ESTER 480G/L;EC", "stockAwal": 510, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40005867", "namaBarang": "HERBICIDE:METHYL METSULFURON 20%;WG", "stockAwal": 1447.4, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40005952", "namaBarang": "FUNGICIDE:HEXACONAZOLE 50 G/L;SC", "stockAwal": 1445.8, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40005966", "namaBarang": "PLANT,GROWTH:ETEFON 10%;SL", "stockAwal": 2423.7, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40006339", "namaBarang": "HERBICIDE:TRIKLOPIR 480G/L;SL", "stockAwal": 335.7, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40006776", "namaBarang": "HERBICIDE:GLYPHOSATE IPA 480G/L;SL;1L", "stockAwal": 2625.8, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40007021", "namaBarang": "FUNGICIDE:DITHANE M-45;80 WP;PW;1 KG", "stockAwal": 1680, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40000210", "namaBarang": "CAUSTIC SODA", "stockAwal": 25, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40005992", "namaBarang": "TALCUM POWDER:H2MG3O12SI4;379.27G/MOL", "stockAwal": 5550, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40006381", "namaBarang": "FORMIC ACID,AR:CH2O2;90%", "stockAwal": 1537, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91018606", "namaBarang": "Minyak Tanah", "stockAwal": 670, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91057141", "namaBarang": "GASOLINE:BIODIESEL;CN 48;815-880KG/M3", "stockAwal": 3650, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "82024963", "namaBarang": "RORED 140", "stockAwal": 88, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91050493", "namaBarang": "LUBE OIL,ENG:SAE 10W;113 VISC", "stockAwal": 209, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91050494", "namaBarang": "LUBE OIL,ENG:SAE 40;98 VISC", "stockAwal": 144, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91054889", "namaBarang": "GREASE,EXTRM PRES:NLGI 2;LI-HYD", "stockAwal": 196, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91055448", "namaBarang": "LUBE OIL,ENG:SAE 10W-40;161 VISC", "stockAwal": 139, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91055476", "namaBarang": "LUBE OIL,GEAR:SAE 140;96 VISC;GL-5", "stockAwal": 209, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "82063569", "namaBarang": "MORTAR,REFRACTORY:SK34;1300DEG C;50KG", "stockAwal": 4, "satuan": "ZAK", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "82078087", "namaBarang": "SCREEN:LATEX;AL;MULA-N007-001", "stockAwal": 18, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91008594", "namaBarang": "KARET FULL", "stockAwal": 2.6, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "82039518", "namaBarang": "Over Load 10 A-15 A", "stockAwal": 1, "satuan": "PC", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91062075", "namaBarang": "LAMP,INCAND:HALOGEN;250W;220V;R7S;YLW", "stockAwal": 4, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91033458", "namaBarang": "Ember Kaleng 20 Ltr", "stockAwal": 68, "satuan": "PC", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91033657", "namaBarang": "PISAU SADAP SODECI", "stockAwal": 421, "satuan": "PC", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91050116", "namaBarang": "KNIFE,LATEX:TOP;20X130X165MM", "stockAwal": 1254, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91050119", "namaBarang": "KNIFE,LATEX:BTM;40X230X370MM;WOOD HDL", "stockAwal": 20, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91050469", "namaBarang": "GUTTER,LATEX:V-SHP;0.2x30x70MM;BJLS;7T", "stockAwal": 658000, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91055778", "namaBarang": "STONE,SHARP:TPR;9.5X45X120MM;FINE", "stockAwal": 19, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91067898", "namaBarang": "HANGER,BOWL,LATEX:RD;15CM DIA;WIRE", "stockAwal": 93000, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91068055", "namaBarang": "HANGER,BOWL,LATEX:RD;10CM DIA;WIRE", "stockAwal": 169000, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "40006686", "namaBarang": "AMMONIA,TECH:NH3;MIN 20%;LIQ", "stockAwal": 115, "satuan": "L", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91037872", "namaBarang": "PLASTIK LATEK", "stockAwal": 642, "satuan": "SHT", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91037876", "namaBarang": "TALI PLASTIK/ STRAPING BAND", "stockAwal": 304, "satuan": "ROL", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91066744", "namaBarang": "STRAPPING:15MM;PP;120M/ROL", "stockAwal": 359, "satuan": "ROL", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91037877", "namaBarang": "CAT KAYU WARNA KUNING", "stockAwal": 8, "satuan": "CAN", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91050213", "namaBarang": "ELECTRODE,WELD:3.2MM;AWS A5.1 E6013;MS", "stockAwal": 20, "satuan": "KG", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "91066304", "namaBarang": "HOSE,NONMTL:4IN;PVC;5MPA;BLUE;SUCTN", "stockAwal": 23, "satuan": "M", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "82057481", "namaBarang": "ADAPTER,BEARING:THD;40X65X33MM;H209", "stockAwal": 3, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "82057917", "namaBarang": "BEARING,RLR:SPHER;TPR;40MM;ST;22208;C3", "stockAwal": 24, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "82066379", "namaBarang": "BEARING,BALL:DG;1R;50MM;ST;6310;2SHLD;C3", "stockAwal": 2, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "82066425", "namaBarang": "BEARING,BALL,SA:TPR;45MM;ST;1209;C3", "stockAwal": 18, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "82066449", "namaBarang": "BEARING,BALL:DG;1R;40MM;ST;6308;2SHLD;C3", "stockAwal": 1, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0},
            {"noMaterial": "82066454", "namaBarang": "BEARING,BALL:DG;1R;45MM;ST;6409;2SHLD;C3", "stockAwal": 2, "satuan": "EA", "masukBarang": 0, "keluarBarang": 0}
        ];

        // Memuat data awal saat halaman dibuka
        document.addEventListener("DOMContentLoaded", () => {
            renderReportView();
        });

        // Fungsi Hapus Baris Data Satuan
        function deleteReport(index) {
            if (confirm(`Apakah Anda yakin ingin menghapus data "${stockReports[index].namaBarang}"?`)) {
                stockReports.splice(index, 1);
                renderReportView();
            }
        }

        // Fungsi Kosongkan Seluruh Data Laporan
        function clearAllData() {
            if (confirm("Apakah Anda yakin ingin menghapus seluruh Rincian Data Laporan Stok?")) {
                stockReports = [];
                renderReportView();
            }
        }

        // Fungsi Render UI & Update Otomatis Dashboard Kartu Statistik
        function renderReportView() {
            // 1. Kalkulasi Real-Time Dashboard
            const totalJenisBarang = stockReports.length;
            const totalMasuk = stockReports.reduce((sum, item) => sum + Number(item.masukBarang || 0), 0);
            const totalKeluar = stockReports.reduce((sum, item) => sum + Number(item.keluarBarang || 0), 0);

            // 2. Hubungkan data ke elemen Dashboard UI
            document.getElementById('dashTotalBarang').innerText = totalJenisBarang;
            document.getElementById('dashBarangMasuk').innerText = totalMasuk.toLocaleString('id-ID');
            document.getElementById('dashBarangKeluar').innerText = totalKeluar.toLocaleString('id-ID');

            // 3. Kontrol Visibilitas Tabel & Empty State
            const emptyState = document.getElementById('emptyStateReport');
            const tableContainer = document.getElementById('tableContainerReport');
            const tableBody = document.getElementById('reportTableBody');
            const badgeCount = document.getElementById('totalReportItemsBadge');

            badgeCount.innerText = `${stockReports.length} Item`;

            if (stockReports.length === 0) {
                emptyState.classList.remove('hidden');
                tableContainer.classList.add('hidden');
                tableBody.innerHTML = '';
                return;
            }

            emptyState.classList.add('hidden');
            tableContainer.classList.remove('hidden');

            // 4. Render isi baris tabel 8 kolom
            tableBody.innerHTML = stockReports.map((item, index) => {
                const stockAkhir = Number(item.stockAwal || 0) + Number(item.masukBarang || 0) - Number(item.keluarBarang || 0);
                return `
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-4 font-semibold text-slate-400 text-center">${index + 1}</td>
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600">${item.noMaterial}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-800">${item.namaBarang}</td>
                        <td class="py-3.5 px-4 font-mono text-center font-semibold text-slate-600">${Number(item.stockAwal).toLocaleString('id-ID')}</td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2.5 py-1 rounded-full border border-slate-200">
                                ${item.satuan}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-center font-bold text-emerald-600">+${Number(item.masukBarang).toLocaleString('id-ID')}</td>
                        <td class="py-3.5 px-4 font-mono text-center font-bold text-rose-600">-${Number(item.keluarBarang).toLocaleString('id-ID')}</td>
                        <td class="py-3.5 px-4 font-mono text-center font-extrabold text-blue-900 bg-blue-50/60">${stockAkhir.toLocaleString('id-ID')}</td>
                        <td class="py-3.5 px-4 text-center print:hidden">
                            <button onclick="deleteReport(${index})" class="bg-rose-50 hover:bg-rose-100 text-rose-600 p-2 rounded-xl transition border border-rose-200/60" title="Hapus Baris Data">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Fungsi Export Data ke PDF
        function exportToPDF() {
            if (stockReports.length === 0) {
                alert('Tidak ada data untuk diexport!');
                return;
            }
            window.print();
        }

        // Fungsi Export Data ke Excel (.xlsx)
        function exportToExcel() {
            if (stockReports.length === 0) {
                alert('Tidak ada data untuk diexport!');
                return;
            }

            const exportData = stockReports.map((item, index) => {
                const stockAkhir = Number(item.stockAwal || 0) + Number(item.masukBarang || 0) - Number(item.keluarBarang || 0);
                return {
                    "No": index + 1,
                    "No. Material": item.noMaterial,
                    "Nama Barang": item.namaBarang,
                    "Stock Awal": item.stockAwal,
                    "Satuan": item.satuan,
                    "Masuk Barang": item.masukBarang,
                    "Keluar Barang": item.keluarBarang,
                    "Stock Akhir": stockAkhir
                };
            });

            const worksheet = XLSX.utils.json_to_sheet(exportData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Laporan Stok");
            XLSX.writeFile(workbook, `Laporan_Stok_Gudang_Kedaton_${new Date().toISOString().slice(0,10)}.xlsx`);
        }
    </script>
</body>
</html>