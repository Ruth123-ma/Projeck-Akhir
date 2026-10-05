<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Stock Gudang - Output Material Keluar</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#F4F6F9] text-slate-800 flex flex-col min-h-screen font-sans md:h-screen md:flex-row md:overflow-hidden">

    @include('components.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="bg-white px-4 py-4 flex flex-col gap-3 border-b border-slate-200/80 sticky top-0 z-10 shadow-sm md:px-8 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Form Material Keluar Gudang</h2>
                <p class="text-xs text-slate-400">Pencatatan material keluar berdasarkan Kode Material & sinkronisasi database</p>
            </div>
            <div class="text-xs bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200 font-mono font-bold text-slate-600 flex items-center gap-2">
                <i class="fa-solid fa-clock text-blue-600"></i>
                <span id="liveClock">00:00:00</span>
            </div>
        </header>

        <div class="p-4 max-w-7xl w-full mx-auto space-y-8 md:p-8">
            
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md overflow-hidden">
                <div class="p-6 pb-4 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800">Tambah Transaksi Material Keluar</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Pilih Kode Material untuk autofill Nama & Satuan secara otomatis</p>
                    </div>
                    <span class="text-xs bg-rose-50 text-rose-600 border border-rose-200/60 px-3 py-1 rounded-full font-bold flex items-center space-x-1.5">
                        <i class="fa-solid fa-bolt text-[10px]"></i>
                        <span>Auto-Fill Active</span>
                    </span>
                </div>

                <form id="formMaterialKeluar" action="{{ route('output-material.store') }}" method="POST" class="p-8 space-y-5">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">1. Kode Barang</label>
                            <div class="relative">
                                <i class="fa-solid fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <select name="material_id" id="inputMaterialId" onchange="autoFillMaterial()" required 
                                    class="w-full bg-slate-50 border border-slate-200 text-sm pl-11 pr-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 focus:bg-white transition">
                                    <option value="">-- Pilih Material --</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}" data-nama="{{ $material->nama_material }}" data-satuan="{{ $material->satuan }}" data-stok="{{ $material->stok_akhir }}">
                                            {{ $material->kode_barang }} - {{ $material->nama_material }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">2. Nama Material</label>
                            <input type="text" id="namaMaterial" readonly placeholder="Nama otomatis terisi" 
                                class="w-full bg-slate-100 border border-slate-200 text-sm px-4 py-3 rounded-xl font-semibold text-slate-700">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">3. Satuan</label>
                            <input type="text" id="satuanMaterial" readonly placeholder="Satuan otomatis terisi" 
                                class="w-full bg-slate-100 border border-slate-200 text-sm px-4 py-3 rounded-xl font-semibold text-slate-700">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">4. Stok Tersedia</label>
                            <div class="relative">
                                <i class="fa-solid fa-box absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                <input type="text" id="stokTersedia" readonly placeholder="0" 
                                    class="w-full bg-slate-100 border border-slate-200 text-sm pl-11 pr-4 py-3 rounded-xl font-bold text-slate-700">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">5. Jumlah Keluar <span class="text-rose-600">*</span></label>
                            <div class="relative">
                                <i class="fa-solid fa-arrow-up-from-box absolute left-4 top-1/2 -translate-y-1/2 text-rose-400 text-sm"></i>
                                <input type="number" name="quantity" id="jumlahKeluar" step="0.01" min="0.01" placeholder="Masukkan jumlah" required
                                    class="w-full bg-white border-2 border-rose-300 text-sm pl-11 pr-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-semibold">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">6. Keterangan</label>
                            <input type="text" name="notes" placeholder="Contoh: Pengiriman ke divisi produksi" 
                                class="w-full bg-white border border-slate-200 text-sm px-4 py-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                    </div>

                    <input type="hidden" name="type" value="OUT">

                    <div class="flex gap-3 pt-4">
                        <button id="processOutputButton" type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-6 rounded-xl transition shadow-md flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            Proses Material Keluar
                        </button>
                        <button type="reset" class="px-8 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-xl transition">
                            Reset
                        </button>
                    </div>

                    @if ($errors->any())
                        <div class="mt-4 p-4 bg-rose-50 border border-rose-300 rounded-lg text-rose-700 text-sm">
                            <i class="fa-solid fa-exclamation-circle mr-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mt-4 p-4 bg-emerald-50 border border-emerald-300 rounded-lg text-emerald-700 text-sm">
                            <i class="fa-solid fa-check-circle mr-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                </form>
            </div>

            <section class="bg-white rounded-3xl border border-slate-200/80 shadow-md overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-800">Laporan Saldo Stok Gudang</h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Saldo berdasarkan riwayat transaksi sampai akhir {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
                            </p>
                        </div>
                        <form method="GET" action="{{ route('output-material') }}" class="no-print flex flex-wrap items-end gap-2">
                            <div>
                                <label for="reportMonth" class="mb-1 block text-[10px] font-bold uppercase text-slate-500">Bulan</label>
                                <select id="reportMonth" name="month" class="rounded-lg border-slate-300 text-sm">
                                    @foreach(range(1, 12) as $monthOption)
                                        <option value="{{ $monthOption }}" @selected($month === $monthOption)>
                                            {{ \Carbon\Carbon::create()->month($monthOption)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="reportYear" class="mb-1 block text-[10px] font-bold uppercase text-slate-500">Tahun</label>
                                <input id="reportYear" name="year" type="number" min="2000" max="{{ now()->year + 1 }}" value="{{ $year }}" class="w-24 rounded-lg border-slate-300 text-sm">
                            </div>
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">
                                Tampilkan
                            </button>
                            <button type="button" onclick="window.print()" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">
                                Cetak
                            </button>
                        </form>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-slate-200 bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-center text-xs font-bold uppercase text-slate-600">No</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-slate-600">Kode Material</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-slate-600">Nama Material</th>
                                <th class="px-5 py-3 text-right text-xs font-bold uppercase text-slate-600">Saldo per Akhir Bulan</th>
                                <th class="px-5 py-3 text-center text-xs font-bold uppercase text-slate-600">Satuan</th>
                                <th class="px-5 py-3 text-center text-xs font-bold uppercase text-slate-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockReport as $index => $item)
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="px-5 py-3 text-center text-slate-500">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3 font-mono font-bold text-slate-800">{{ $item->kode_barang }}</td>
                                    <td class="px-5 py-3 font-semibold text-slate-700">{{ $item->nama_material }}</td>
                                    <td class="px-5 py-3 text-right font-bold text-slate-800">{{ number_format($item->stok, 2) }}</td>
                                    <td class="px-5 py-3 text-center text-slate-600">{{ $item->satuan }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $item->status === 'NORMAL' ? 'bg-emerald-100 text-emerald-700' : ($item->status === 'RENDAH' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada data material untuk laporan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Riwayat Transaksi Keluar -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md overflow-hidden">
                <div class="p-6 pb-4 border-b border-slate-100">
                    <h3 class="text-lg font-extrabold text-slate-800">Riwayat Transaksi Material Keluar</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Kode Material</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Nama Material</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-slate-600">Jumlah Keluar</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-slate-600">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 text-slate-600">{{ $trx->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ $trx->material->kode_barang ?? '-' }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $trx->material->nama_material ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-rose-600">{{ number_format($trx->quantity, 2) }} {{ $trx->material->satuan ?? '' }}</td>
                                    <td class="px-6 py-4 text-slate-600 text-xs">{{ $trx->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                        <i class="fa-solid fa-inbox text-4xl text-slate-300 mb-2 block"></i>
                                        Belum ada transaksi material keluar
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        setInterval(() => {
            const now = new Date();
            document.getElementById('liveClock').textContent = now.toLocaleTimeString('id-ID');
        }, 1000);

        function autoFillMaterial() {
            const select = document.getElementById('inputMaterialId');
            const option = select.options[select.selectedIndex];
            
            document.getElementById('namaMaterial').value = option.dataset.nama || '';
            document.getElementById('satuanMaterial').value = option.dataset.satuan || '';
            document.getElementById('stokTersedia').value = option.dataset.stok || '0';
        }

        document.getElementById('formMaterialKeluar').addEventListener('submit', function(e) {
            const stok = parseFloat(document.getElementById('stokTersedia').value);
            const jumlah = parseFloat(document.getElementById('jumlahKeluar').value);
            
            if (jumlah > stok) {
                e.preventDefault();
                alert(`Stok tidak mencukupi! Stok tersedia: ${stok}`);
                return;
            }

            const button = document.getElementById('processOutputButton');
            if (button.disabled) {
                e.preventDefault();
                return;
            }

            button.disabled = true;
            button.classList.add('cursor-not-allowed', 'opacity-70');
            button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        });
    </script>

</body>
</html>
