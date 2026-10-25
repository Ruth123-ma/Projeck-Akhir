<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Stok Gudang - Stock Gudang</title>
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
                        <h2 class="text-2xl font-bold text-slate-800">Daftar Stok Gudang</h2>
                        <p class="text-xs text-slate-400 mt-1">Tampilan lengkap semua material yang tersimpan di gudang</p>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    @if (session('success'))
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700" role="status">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <!-- Statistik Stok -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase font-bold">Total Material</p>
                                    <h3 class="text-2xl font-black text-slate-800">{{ $totalMaterial }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase font-bold">Stok Kritis</p>
                                    <h3 class="text-2xl font-black text-rose-600">{{ $stokKritis }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-lg flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                            </div>
                        </div>


                        <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-500 uppercase font-bold">Status Normal</p>
                                    <h3 class="text-2xl font-black text-blue-600">{{ $totalMaterial - $stokKritis }}</h3>
                                </div>
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Daftar Stok -->
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-6 pb-4 border-b border-slate-100 space-y-4">
                            <h3 class="text-lg font-extrabold text-slate-800">Daftar Lengkap Material Gudang</h3>
                            <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                                <form method="GET" action="{{ route('daftar-stok') }}" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                                    <label class="space-y-1">
                                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Cari Material</span>
                                        <input type="search" name="cari" value="{{ $search }}" placeholder="Kode atau nama material"
                                            class="w-full sm:w-64 px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </label>
                                    <label class="space-y-1">
                                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Filter Bulan</span>
                                        <select name="bulan" class="w-full sm:w-48 px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">Semua Bulan</option>
                                            @foreach($availableMonths as $month)
                                                <option value="{{ $month }}" @selected($selectedMonth === $month)>
                                                    {{ \Carbon\Carbon::createFromFormat('!Y-m', $month)->locale('id')->translatedFormat('F Y') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-4 py-2.5 rounded-lg transition">
                                        <i class="fa-solid fa-magnifying-glass mr-1"></i> Cari
                                    </button>
                                    @if($search !== '' || $selectedMonth !== '')
                                        <a href="{{ route('daftar-stok') }}" class="text-center text-slate-500 hover:text-slate-800 font-semibold px-2 py-2.5">Reset</a>
                                    @endif
                                </form>
                                <div class="flex gap-2">
                                    <a href="{{ route('daftar-stok.export.pdf', request()->only(['cari', 'bulan'])) }}" target="_blank" rel="noopener"
                                        class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold px-4 py-2.5 rounded-lg transition">
                                        <i class="fa-solid fa-file-pdf mr-1"></i> PDF
                                    </a>
                                    <a href="{{ route('daftar-stok.export.excel', request()->only(['cari', 'bulan'])) }}"
                                        class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold px-4 py-2.5 rounded-lg transition">
                                        <i class="fa-solid fa-file-excel mr-1"></i> Excel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm" id="tableStok">
                                <thead class="bg-slate-50 border-b border-slate-200 sticky top-0">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Kode Barang</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Nama Material</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Lokasi</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold uppercase text-slate-600">Stok Akhir</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold uppercase text-slate-600">Min Stok</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold uppercase text-slate-600">Status</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold uppercase text-slate-600">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materials as $idx => $material)
                                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                            <td class="px-6 py-4 font-bold text-slate-800">{{ ($materials->currentPage() - 1) * $materials->perPage() + $loop->iteration }}</td>
                                            <td class="px-6 py-4 font-mono font-bold text-slate-700">{{ $material->kode_barang }}</td>
                                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $material->nama_material }}</td>
                                            <td class="px-6 py-4 text-slate-600 text-xs">{{ $material->lokasi ?? 'Gudang Utama' }}</td>
                                            <td class="px-6 py-4 text-center font-bold text-slate-800">
                                                {{ number_format($material->stok_akhir, 2) }} {{ $material->satuan }}
                                            </td>
                                            <td class="px-6 py-4 text-center font-semibold text-slate-600">
                                                {{ $material->min_stok ?? '10' }} {{ $material->satuan }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($material->status === 'KRITIS' || $material->stok_akhir <= ($material->min_stok ?? 10))
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                                        <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                        Kritis
                                                    </span>
                                                @elseif($material->status === 'RENDAH' || $material->stok_akhir <= (($material->min_stok ?? 10) * 1.5))
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                                        <i class="fa-solid fa-exclamation-circle text-[10px]"></i>
                                                        Rendah
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                                        <i class="fa-solid fa-check-circle text-[10px]"></i>
                                                        Normal
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <a href="{{ route('material.edit', $material) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-100">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-8 text-center text-slate-500">
                                                <i class="fa-solid fa-inbox text-4xl text-slate-300 mb-2 block"></i>
                                                Belum ada data stok
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($materials->hasPages())
                            <div class="p-4 border-t border-slate-100 flex justify-center">
                                {{ $materials->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
