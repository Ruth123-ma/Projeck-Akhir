<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Gudang - Dashboard Utama</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#F8FAFC] text-slate-800 flex h-screen font-sans overflow-hidden">
    <!-- Sidebar Component -->
    @include('components.sidebar')

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto">
        
        <!-- Top Navigation / Header -->
        <header class="bg-white px-8 py-4 flex justify-between items-center border-b border-slate-200/80 sticky top-0 z-20 shadow-sm">
            <div class="flex items-center space-x-4">
                <h2 class="text-xl font-bold text-slate-800">Overview Gudang</h2>
                <span class="bg-blue-50 text-blue-600 text-xs px-3 py-1 rounded-full font-semibold border border-blue-200/60">Shift Pagi</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <!-- Quick Search -->
                <div class="relative hidden sm:block">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Cari SKU / barang..." class="bg-slate-100/80 text-xs pl-9 pr-4 py-2 rounded-xl w-64 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>
                
                <!-- Notification & User -->
                <button class="relative text-slate-500 hover:text-slate-800 transition">
                    <i class="fa-solid fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center font-bold">3</span>
                </button>
                
                <div class="flex items-center space-x-3 border-l pl-6 border-slate-200">
                    <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=1E88E5&color=fff" class="w-9 h-9 rounded-xl object-cover shadow-sm">
                    <div>
                        <p class="text-xs font-bold text-slate-800 leading-tight">Budi Santoso</p>
                        <p class="text-[10px] text-slate-400 font-medium">Head Warehouse</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Body Content -->
        <div class="p-8 space-y-8">
            
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-[#091428] to-[#1E3A8A] rounded-3xl p-6 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center">
                <div class="z-10 space-y-2">
                    <h1 class="text-2xl font-extrabold tracking-tight">Selamat Datang Kembali, Budi! 👋</h1>
                    <p class="text-slate-300 text-xs max-w-xl">Operasional gudang berjalan optimal hari ini. Terdapat <span class="text-amber-300 font-bold">4 item</span> dengan kondisi stok di bawah batas minimum.</p>
                </div>
                <div class="mt-4 md:mt-0 z-10 flex space-x-3">
                    <a href="{{ url('/material/input') }}" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-md flex items-center space-x-2">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Input Barang</span>
                    </a>
                    <a href="{{ url('/material/output') }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold px-4 py-2.5 rounded-xl transition flex items-center space-x-2">
                        <i class="fa-solid fa-dolly text-xs"></i>
                        <span>Keluar Barang</span>
                    </a>
                </div>
                <!-- Decorative background elements -->
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl"></div>
            </div>

            <!-- KPI Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Material -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Item Barang</span>
                        <div class="bg-blue-50 text-blue-600 p-2.5 rounded-xl">
                            <i class="fa-solid fa-boxes-stacked text-base"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-slate-900 mb-1">2,845 <span class="text-xs font-normal text-slate-400">SKU</span></h3>
                    <p class="text-xs text-emerald-600 font-semibold flex items-center space-x-1">
                        <i class="fa-solid fa-arrow-up text-[10px]"></i>
                        <span>+12% dibanding bulan lalu</span>
                    </p>
                </div>

                <!-- Barang Masuk -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Barang Masuk (Hari Ini)</span>
                        <div class="bg-emerald-50 text-emerald-600 p-2.5 rounded-xl">
                            <i class="fa-solid fa-box-archive text-base"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-slate-900 mb-1">142 <span class="text-xs font-normal text-slate-400">Pcs</span></h3>
                    <p class="text-xs text-emerald-600 font-semibold flex items-center space-x-1">
                        <i class="fa-solid fa-arrow-up text-[10px]"></i>
                        <span>+5 Transaksi Berhasil</span>
                    </p>
                </div>

                <!-- Barang Keluar -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Barang Keluar (Hari Ini)</span>
                        <div class="bg-amber-50 text-amber-600 p-2.5 rounded-xl">
                            <i class="fa-solid fa-dolly text-base"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-slate-900 mb-1">89 <span class="text-xs font-normal text-slate-400">Pcs</span></h3>
                    <p class="text-xs text-slate-500 font-semibold flex items-center space-x-1">
                        <i class="fa-solid fa-check text-[10px]"></i>
                        <span>3 Proyek Aktif</span>
                    </p>
                </div>

                <!-- Alert Stok Menipis -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Perlu Reorder</span>
                        <div class="bg-rose-50 text-rose-600 p-2.5 rounded-xl">
                            <i class="fa-solid fa-triangle-exclamation text-base"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-extrabold text-rose-600 mb-1">4 <span class="text-xs font-normal text-slate-400">Item</span></h3>
                    <p class="text-xs text-rose-500 font-semibold flex items-center space-x-1">
                        <span>Stok dibawah batas kritis</span>
                    </p>
                </div>
            </div>

            <!-- Visual Charts & Low Stock Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Line Chart Movement -->
                <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="font-bold text-slate-800 text-base">Arus Barang (Masuk vs Keluar)</h3>
                            <p class="text-xs text-slate-400">Grafik pergerakan stok selama 7 hari terakhir</p>
                        </div>
                        <select class="text-xs border border-slate-200 rounded-lg px-3 py-1.5 bg-slate-50 text-slate-600 focus:outline-none">
                            <option>7 Hari Terakhir</option>
                            <option>Bulan Ini</option>
                        </select>
                    </div>
                    <div class="h-64">
                        <canvas id="stockChart"></canvas>
                    </div>
                </div>

                <!-- Low Stock Alert Box -->
                <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-slate-800 text-base">Peringatan Stok Low</h3>
                            <a href="{{ url('/laporan') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</a>
                        </div>
                        <p class="text-xs text-slate-400 mb-4">Item berikut harus segera diisi kembali (reorder).</p>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <p class="text-xs font-bold text-slate-800">Besi Beton 10mm</p>
                                    <p class="text-[10px] text-slate-400 font-mono">SKU-10293</p>
                                </div>
                                <span class="bg-rose-100 text-rose-700 text-[11px] font-bold px-2.5 py-1 rounded-lg">Sisa 12 Btg</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <p class="text-xs font-bold text-slate-800">Cat Tembok Putih 20L</p>
                                    <p class="text-[10px] text-slate-400 font-mono">SKU-88219</p>
                                </div>
                                <span class="bg-rose-100 text-rose-700 text-[11px] font-bold px-2.5 py-1 rounded-lg">Sisa 3 Pail</span>
                            </div>

                            <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <p class="text-xs font-bold text-slate-800">Pipa PVC 2 Inc</p>
                                    <p class="text-[10px] text-slate-400 font-mono">SKU-44102</p>
                                </div>
                                <span class="bg-amber-100 text-amber-700 text-[11px] font-bold px-2.5 py-1 rounded-lg">Sisa 8 Btg</span>
                            </div>
                        </div>
                    </div>

                    <button class="w-full mt-4 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3 rounded-xl transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>Buat Permintaan Reorder</span>
                    </button>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Aktivitas Transaksi Terkini</h3>
                        <p class="text-xs text-slate-400">Catatan mutasi barang masuk dan keluar terbaru</p>
                    </div>
                    <a href="{{ url('/laporan') }}" class="text-xs font-bold text-blue-600 hover:underline">Lihat Laporan Lengkap →</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="text-[11px] font-bold text-slate-400 uppercase border-b border-slate-100 bg-slate-50/50">
                                <th class="py-3 px-4">Tipe Mutasi</th>
                                <th class="py-3 px-4">Kode SKU</th>
                                <th class="py-3 px-4">Nama Barang</th>
                                <th class="py-3 px-4">Jumlah</th>
                                <th class="py-3 px-4">Waktu</th>
                                <th class="py-3 px-4">Operator</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4">
                                    <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full font-bold text-[10px] flex items-center w-max space-x-1">
                                        <i class="fa-solid fa-arrow-down text-[9px]"></i>
                                        <span>Masuk</span>
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-500">SKU-89210</td>
                                <td class="py-3 px-4 font-bold text-slate-800">Semen Gresik 50kg</td>
                                <td class="py-3 px-4 font-bold text-emerald-600">+100 Sak</td>
                                <td class="py-3 px-4 text-slate-400">10:45 WIB</td>
                                <td class="py-3 px-4 text-slate-600 font-medium">Budi Santoso</td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4">
                                    <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full font-bold text-[10px] flex items-center w-max space-x-1">
                                        <i class="fa-solid fa-arrow-up text-[9px]"></i>
                                        <span>Keluar</span>
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-500">SKU-10293</td>
                                <td class="py-3 px-4 font-bold text-slate-800">Besi Beton 10mm</td>
                                <td class="py-3 px-4 font-bold text-amber-600">-25 Batang</td>
                                <td class="py-3 px-4 text-slate-400">09:15 WIB</td>
                                <td class="py-3 px-4 text-slate-600 font-medium">Ahmad Subagyo</td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4">
                                    <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full font-bold text-[10px] flex items-center w-max space-x-1">
                                        <i class="fa-solid fa-arrow-down text-[9px]"></i>
                                        <span>Masuk</span>
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-500">SKU-30112</td>
                                <td class="py-3 px-4 font-bold text-slate-800">Triplek 9mm 4x8</td>
                                <td class="py-3 px-4 font-bold text-emerald-600">+50 Lembar</td>
                                <td class="py-3 px-4 text-slate-400">Yesterday</td>
                                <td class="py-3 px-4 text-slate-600 font-medium">Budi Santoso</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- Chart Configuration Script -->
    <script>
        const ctx = document.getElementById('stockChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: [65, 80, 45, 110, 95, 142, 60],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Barang Keluar',
                        data: [40, 55, 30, 85, 60, 89, 40],
                        borderColor: '#F59E0B',
                        backgroundColor: 'rgba(245, 158, 11, 0.05)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 11, family: 'sans-serif' }, usePointStyle: true }
                    }
                },
                scales: {
                    y: {
                        grid: { color: '#F1F5F9' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    </script>
</body>
</html>