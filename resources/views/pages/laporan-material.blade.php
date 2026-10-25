<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Material - Stock Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#f8fafc] text-slate-800 font-sans">

    <div class="flex min-h-screen flex-col md:h-screen md:flex-row">
        @include('components.sidebar')

        <main class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white px-4 py-4 border-b border-slate-200 sticky top-0 z-10 shadow-sm md:px-8 md:py-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Laporan Material Keluar &amp; Masuk</h2>
                        <p class="text-xs text-slate-400 mt-1">Rekap transaksi material yang masuk dan keluar dari gudang</p>
                        <p class="mt-2 inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-700"><i class="fa-solid fa-lock"></i> Arsip laporan permanen</p>
                    </div>
                    <div class="no-print flex flex-wrap gap-2">
                        <a href="{{ route('laporan-material.export.pdf', request()->only(['year', 'month', 'cari'])) }}" target="_blank" rel="noopener"
                            class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-sm font-bold rounded-lg transition flex items-center gap-2">
                            <i class="fa-solid fa-file-pdf"></i> PDF
                        </a>
                        <a href="{{ route('laporan-material.export.excel', request()->only(['year', 'month', 'cari'])) }}"
                            class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-sm font-bold rounded-lg transition flex items-center gap-2">
                            <i class="fa-solid fa-file-excel"></i> Excel
                        </a>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    <form method="GET" action="{{ route('laporan-material') }}" class="no-print bg-white rounded-lg border border-slate-200 shadow-sm p-4 flex flex-col gap-3 md:flex-row md:items-end">
                        <div>
                            <label for="month" class="block text-xs font-bold uppercase text-slate-500 mb-1">Filter Bulan</label>
                            <select id="month" name="month" class="w-full md:w-44 rounded-lg border-slate-300 text-sm">
                                @foreach(range(1, 12) as $monthOption)
                                    <option value="{{ $monthOption }}" @selected($month === $monthOption)>
                                        {{ \Carbon\Carbon::create()->month($monthOption)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="year" class="block text-xs font-bold uppercase text-slate-500 mb-1">Tahun</label>
                            <input id="year" name="year" type="number" min="2000" max="{{ now()->year + 1 }}" value="{{ $year }}" class="w-full md:w-32 rounded-lg border-slate-300 text-sm">
                        </div>
                        <div>
                            <label for="cari" class="block text-xs font-bold uppercase text-slate-500 mb-1">Cari Material</label>
                            <input id="cari" name="cari" type="search" value="{{ $search }}" placeholder="Kode atau nama"
                                class="w-full md:w-56 rounded-lg border-slate-300 text-sm">
                        </div>
                        <button type="submit" class="rounded-lg bg-slate-800 px-5 py-2.5 text-sm font-bold text-white hover:bg-slate-700">
                            <i class="fa-solid fa-filter mr-1"></i> Tampilkan Laporan
                        </button>
                    </form>

                    <div class="hidden print:block">
                        <h1 class="text-xl font-bold">Laporan Material Keluar & Masuk</h1>
                        <p class="text-sm">Periode: {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}</p>
                    </div>
                    
                    <!-- Statistik Transaksi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase font-bold">Transaksi Keluar</p>
                                    <h3 class="text-2xl font-black text-slate-800">{{ $totalTransaksi }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-exchange"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase font-bold">Jenis Barang Keluar</p>
                                    <h3 class="text-2xl font-black text-blue-600">{{ $jumlahBarangKeluar }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase font-bold">Transaksi Masuk</p>
                                    <h3 class="text-2xl font-black text-slate-800">{{ $totalTransaksiMasuk }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-exchange"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase font-bold">Jenis Barang Masuk</p>
                                    <h3 class="text-2xl font-black text-emerald-600">{{ $jumlahBarangMasuk }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Tabel Laporan Transaksi Masuk -->
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 pb-4 border-b border-slate-100">
                            <h3 class="text-lg font-extrabold text-slate-800">Riwayat Transaksi Masuk</h3>
                            <p class="text-xs text-slate-500 mt-1">Material yang diterima pada {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Kode Material</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Nama Material</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold uppercase text-slate-600">Jumlah Masuk</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($incoming as $transaction)
                                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 text-slate-600 text-xs">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                            <td class="px-6 py-4 font-mono font-bold text-slate-800">{{ $transaction->material->kode_barang ?? '-' }}</td>
                                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $transaction->material->nama_material ?? '-' }}</td>
                                            <td class="px-6 py-4 text-center font-bold text-emerald-600">+ {{ number_format($transaction->quantity, 2) }}</td>
                                            <td class="px-6 py-4 text-slate-600 text-xs">{{ $transaction->user_id ?? 'System' }}</td>
                                            <td class="px-6 py-4 text-slate-600 text-xs">{{ $transaction->notes ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada transaksi material masuk pada periode ini</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Ringkasan Per-Material -->
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 pb-4 border-b border-slate-100">
                            <h3 class="text-lg font-extrabold text-slate-800">Ringkasan Barang Keluar</h3>
                            <p class="text-xs text-slate-500 mt-1">Periode {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-slate-600">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-600">No. Material</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-600">Nama Barang</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-slate-600">Satuan</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold uppercase text-slate-600">Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($summaryMaterials as $i => $m)
                                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                            <td class="px-4 py-3 text-center font-bold text-slate-600">{{ $i + 1 }}</td>
                                            <td class="px-4 py-3 font-mono font-bold text-slate-800">{{ $m->kode_barang }}</td>
                                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $m->nama_material }}</td>
                                            <td class="px-4 py-3 text-center text-slate-700">{{ $m->satuan }}</td>
                                            <td class="px-4 py-3 text-center text-rose-600 font-bold">{{ number_format($m->keluar, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                                <i class="fa-solid fa-inbox text-4xl text-slate-300 mb-2 block"></i>
                                                Belum ada transaksi material keluar pada periode ini
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tabel Laporan Transaksi -->
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 pb-4 border-b border-slate-100">
                            <h3 class="text-lg font-extrabold text-slate-800">Riwayat Transaksi Keluar</h3>
                            <p class="text-xs text-slate-500 mt-1">Material yang dikeluarkan pada {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Kode Material</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Nama Material</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold uppercase text-slate-600">Jumlah Keluar</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $idx => $transaction)
                                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 text-slate-600 text-xs">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                            <td class="px-6 py-4 font-mono font-bold text-slate-800">{{ $transaction->material->kode_barang ?? '-' }}</td>
                                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $transaction->material->nama_material ?? '-' }}</td>
                                            <td class="px-6 py-4 text-center font-bold">
                                                <span class="text-rose-600">
                                                    - {{ number_format($transaction->quantity, 2) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600 text-xs">{{ $transaction->user_id ?? 'System' }}</td>
                                            <td class="px-6 py-4 text-slate-600 text-xs">{{ $transaction->notes ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                                <i class="fa-solid fa-inbox text-4xl text-slate-300 mb-2 block"></i>
                                                Belum ada transaksi material keluar pada periode ini
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </main>
    </div>

    <style>
        @media print {
            .no-print { display: none; }
            aside { display: none !important; }
            body { background: white; }
            main { width: 100% !important; }
        }
    </style>

</body>
</html>
