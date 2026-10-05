<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $material->exists ? 'Edit Material' : 'Tambah Material' }} - Stock Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans text-slate-800">
    <div class="flex min-h-screen flex-col md:flex-row">
        @include('components.sidebar')

        <main class="flex-1 p-4 md:p-8">
            <div class="mx-auto max-w-3xl space-y-6">
                <header class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold">
                            {{ $material->exists ? 'Edit Data Material' : 'Tambah Material Baru' }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">Kelola identitas barang dan batas minimum stok.</p>
                    </div>
                    <a href="{{ route('daftar-stok') }}" class="shrink-0 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold hover:bg-slate-100">
                        Kembali ke stok
                    </a>
                </header>

                @if ($errors->any())
                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                        <p class="font-bold">Periksa kembali data yang diisi:</p>
                        <ul class="mt-2 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ $material->exists ? route('material.update', $material) : route('material.store') }}" method="POST" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:p-8">
                    @csrf
                    @if ($material->exists)
                        @method('PUT')
                    @endif

                    <div>
                        <label for="kode_barang" class="mb-1 block text-sm font-semibold">Kode Barang</label>
                        <input id="kode_barang" name="kode_barang" value="{{ old('kode_barang', $material->kode_barang) }}" required maxlength="50" class="w-full rounded-lg border border-slate-300 px-4 py-3 font-mono focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div>
                        <label for="nama_material" class="mb-1 block text-sm font-semibold">Nama Material</label>
                        <input id="nama_material" name="nama_material" value="{{ old('nama_material', $material->nama_material) }}" required maxlength="255" class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="satuan" class="mb-1 block text-sm font-semibold">Satuan</label>
                            <input id="satuan" name="satuan" value="{{ old('satuan', $material->satuan) }}" placeholder="Contoh: PCS, KG, L" required maxlength="20" class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>

                        <div>
                            <label for="min_stok" class="mb-1 block text-sm font-semibold">Batas Minimum Stok</label>
                            <input id="min_stok" name="min_stok" type="number" value="{{ old('min_stok', $material->exists ? $material->min_stok : 10) }}" min="0" step="0.01" required class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        </div>
                    </div>

                    @if ($material->exists)
                        <div class="rounded-lg bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            Stok saat ini: <strong>{{ number_format((float) $material->stok_akhir, 2) }} {{ $material->satuan }}</strong>.
                            Perubahan stok dilakukan melalui menu Input atau Output agar tercatat di riwayat transaksi.
                        </div>
                    @else
                        <div>
                            <label for="stok_awal" class="mb-1 block text-sm font-semibold">Stok Awal</label>
                            <input id="stok_awal" name="stok_awal" type="number" value="{{ old('stok_awal', 0) }}" min="0" step="0.01" required class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <p class="mt-1 text-xs text-slate-500">Jika lebih dari 0, stok awal otomatis dicatat sebagai transaksi masuk.</p>
                        </div>
                    @endif

                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <a href="{{ route('daftar-stok') }}" class="rounded-lg border border-slate-300 px-5 py-3 text-center text-sm font-semibold hover:bg-slate-50">Batal</a>
                        <button type="submit" class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                            {{ $material->exists ? 'Simpan Perubahan' : 'Simpan Material' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
