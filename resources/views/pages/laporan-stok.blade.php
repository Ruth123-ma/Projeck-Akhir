<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Gudang - Stock Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800">
    <div class="flex min-h-screen flex-col md:flex-row">
        @include('components.sidebar')

        <main class="min-w-0 flex-1">
            <header class="sticky top-0 z-10 flex flex-col gap-3 border-b border-slate-200 bg-white px-4 py-4 shadow-sm md:flex-row md:items-center md:justify-between md:px-8">
                <div>
                    <h1 class="text-2xl font-black">Laporan Stok Gudang</h1>
                    <p class="mt-1 text-xs text-slate-500">
                        Saldo stok material per akhir {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                    </p>
                </div>
                <div class="no-print flex flex-wrap items-end gap-2">
                    <form method="GET" action="{{ route('laporan-stok') }}" class="flex flex-wrap items-end gap-2">
                        <div>
                            <label for="month" class="mb-1 block text-[10px] font-bold uppercase text-slate-500">Filter Bulan</label>
                            <select id="month" name="month" class="rounded-lg border-slate-300 text-sm">
                                @foreach(range(1, 12) as $monthOption)
                                    <option value="{{ $monthOption }}" @selected($month === $monthOption)>
                                        {{ \Carbon\Carbon::create()->month($monthOption)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="year" class="mb-1 block text-[10px] font-bold uppercase text-slate-500">Tahun</label>
                            <input id="year" name="year" type="number" min="2000" max="{{ now()->year + 1 }}" value="{{ $year }}" class="w-24 rounded-lg border-slate-300 text-sm">
                        </div>
                        <div>
                            <label for="cari" class="mb-1 block text-[10px] font-bold uppercase text-slate-500">Cari Material</label>
                            <input id="cari" name="cari" type="search" value="{{ $search }}" placeholder="Kode atau nama"
                                class="w-44 rounded-lg border-slate-300 text-sm">
                        </div>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">
                            <i class="fa-solid fa-filter mr-1"></i> Tampilkan
                        </button>
                    </form>
                    <a href="{{ route('laporan-stok.export.pdf', request()->only(['year', 'month', 'cari'])) }}" target="_blank" rel="noopener"
                        class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 hover:bg-rose-100">
                        <i class="fa-solid fa-file-pdf mr-1"></i> PDF
                    </a>
                    <a href="{{ route('laporan-stok.export.excel', request()->only(['year', 'month', 'cari'])) }}"
                        class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 hover:bg-emerald-100">
                        <i class="fa-solid fa-file-excel mr-1"></i> Excel
                    </a>
                </div>
            </header>

            <div class="mx-auto max-w-7xl space-y-5 p-4 md:p-8">
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="font-extrabold">Rincian Saldo Material</h2>
                            <p class="mt-1 text-xs text-slate-500">Saldo dihitung dari transaksi masuk dikurangi transaksi keluar sampai akhir periode.</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">{{ $stockReport->count() }} jenis material</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-slate-200 bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-slate-600">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-600">Kode Material</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold uppercase text-slate-600">Nama Material</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold uppercase text-slate-600">Saldo Akhir</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-slate-600">Satuan</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold uppercase text-slate-600">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockReport as $index => $material)
                                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                                        <td class="px-4 py-3 text-center text-slate-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 font-mono font-bold">{{ $material->kode_barang }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ $material->nama_material }}</td>
                                        <td class="px-4 py-3 text-right font-bold">{{ number_format($material->stok, 2) }}</td>
                                        <td class="px-4 py-3 text-center text-slate-600">{{ $material->satuan }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $material->status === 'NORMAL' ? 'bg-emerald-100 text-emerald-700' : ($material->status === 'RENDAH' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                                {{ $material->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center text-slate-500">Belum ada data material.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($stockReport->isNotEmpty())
                                <tfoot class="bg-slate-50">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-right font-extrabold">Total saldo stok</td>
                                        <td class="px-4 py-3 text-right font-black">{{ number_format($totalStok, 2) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            aside { display: none !important; }
            body { background: white; }
        }
    </style>
</body>
</html>
