<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Stok Gudang - PDF</title>
    <style>
        body { color: #1e293b; font-family: Arial, sans-serif; margin: 32px; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        p { color: #64748b; margin-top: 0; }
        button { background: #2563eb; border: 0; border-radius: 6px; color: white; cursor: pointer; font-weight: bold; padding: 10px 16px; }
        table { border-collapse: collapse; margin-top: 20px; width: 100%; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        th { background: #f1f5f9; }
        .number { text-align: right; }
        @media print {
            body { margin: 0; }
            button { display: none; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <h1>Daftar Stok Gudang</h1>
    <p>Dicetak pada {{ now()->locale('id')->translatedFormat('d F Y H:i') }}</p>
    <button type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button>

    <table>
        <thead>
            <tr>
                <th>Kode Barang</th>
                <th>Nama Material</th>
                <th>Lokasi</th>
                <th class="number">Stok Akhir</th>
                <th class="number">Min Stok</th>
                <th>Satuan</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($materials as $material)
                <tr>
                    <td>{{ $material->kode_barang }}</td>
                    <td>{{ $material->nama_material }}</td>
                    <td>{{ $material->lokasi ?? 'Gudang Utama' }}</td>
                    <td class="number">{{ number_format((float) $material->stok_akhir, 2) }}</td>
                    <td class="number">{{ number_format((float) ($material->min_stok ?? 10), 2) }}</td>
                    <td>{{ $material->satuan }}</td>
                    <td>{{ $material->status ?? 'NORMAL' }}</td>
                    <td>{{ $material->tanggal_transaksi?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data stok yang sesuai dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
