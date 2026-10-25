<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Material Keluar - PDF</title>
    <style>
        body { color: #1e293b; font-family: Arial, sans-serif; margin: 32px; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        p { color: #64748b; margin-top: 0; }
        button { background: #e11d48; border: 0; border-radius: 6px; color: white; cursor: pointer; font-weight: bold; padding: 10px 16px; }
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
    <h1>Riwayat Transaksi Material Keluar</h1>
    <p>Dicetak pada {{ now()->locale('id')->translatedFormat('d F Y H:i') }}</p>
    <button type="button" onclick="window.print()">Cetak / Simpan sebagai PDF</button>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Material</th>
                <th>Nama Material</th>
                <th class="number">Jumlah Keluar</th>
                <th>Satuan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $transaction->material?->kode_barang ?? '-' }}</td>
                    <td>{{ $transaction->material?->nama_material ?? '-' }}</td>
                    <td class="number">{{ number_format((float) $transaction->quantity, 2) }}</td>
                    <td>{{ $transaction->material?->satuan ?? '-' }}</td>
                    <td>{{ $transaction->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada transaksi yang sesuai dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
