<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Gudang - PDF</title>
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
    <h1>Laporan Stok Gudang</h1>
    <p>Saldo per akhir {{ \Carbon\Carbon::create($year, $month)->locale('id')->translatedFormat('F Y') }}
        @if($search !== '') · Pencarian: {{ $search }} @endif
    </p>
    <button type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button>

    <table>
        <thead>
            <tr>
                <th>Kode Material</th>
                <th>Nama Material</th>
                <th class="number">Saldo Akhir</th>
                <th>Satuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockReport as $material)
                <tr>
                    <td>{{ $material->kode_barang }}</td>
                    <td>{{ $material->nama_material }}</td>
                    <td class="number">{{ number_format($material->stok, 2) }}</td>
                    <td>{{ $material->satuan }}</td>
                    <td>{{ $material->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data material yang sesuai dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
        @if($stockReport->isNotEmpty())
            <tfoot>
                <tr>
                    <th colspan="2" class="number">Total saldo stok</th>
                    <th class="number">{{ number_format($totalStok, 2) }}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        @endif
    </table>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
