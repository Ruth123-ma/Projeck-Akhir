<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialMasuk;
use App\Models\MaterialTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MaterialMasukController
{
    public function index(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|string|max:20',
            'cari' => 'nullable|string|max:100',
        ]);

        return response()->json($this->filteredHistory($request)->get());
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'bulan' => 'nullable|string|max:20',
            'cari' => 'nullable|string|max:100',
        ]);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Material Masuk');
        $headers = ['Nomor Material', 'Nama Material', 'Jumlah', 'Satuan', 'Tanggal', 'Jam', 'Bulan'];

        foreach ($headers as $index => $header) {
            $column = chr(ord('A') + $index);
            $sheet->setCellValueExplicit($column.'1', $header, DataType::TYPE_STRING);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        foreach ($this->filteredHistory($request)->get() as $index => $item) {
            $row = $index + 2;
            $values = [
                'A' => $item->kode_barang,
                'B' => $item->nama_material,
                'D' => $item->satuan,
                'E' => $item->tanggal_transaksi,
                'F' => $item->jam_input,
                'G' => $item->bulan_simpanan,
            ];

            foreach ($values as $column => $value) {
                $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
            }

            $sheet->setCellValueExplicit('C'.$row, (float) $item->jumlah, DataType::TYPE_NUMERIC);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, 'riwayat-material-masuk-'.now()->format('Ymd-His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function filteredHistory(Request $request): Builder
    {
        $query = MaterialMasuk::query()->orderByDesc('id');
        $bulan = $request->query('bulan');

        if ($bulan && $bulan !== 'Semua') {
            $query->where('bulan_simpanan', $bulan);
        }

        $search = trim((string) $request->query('cari', ''));
        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('kode_barang', 'like', '%'.$search.'%')
                    ->orWhere('nama_material', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'sometimes|required|string',
            'material_id' => 'sometimes|nullable|exists:materials,id',
            'nama' => 'sometimes|required|string',
            'type' => 'sometimes|in:IN,OUT',
            'quantity' => 'sometimes|numeric|min:0.01',
            'jumlah' => 'sometimes|numeric|min:0.01',
            'satuan' => 'sometimes|required|string',
            'tanggal' => 'sometimes|required|date',
            'jam' => 'sometimes|required',
            'bulan' => 'sometimes|required|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $type = strtoupper($validated['type'] ?? 'IN');
        $quantity = isset($validated['quantity']) ? (float) $validated['quantity'] : (isset($validated['jumlah']) ? (float) $validated['jumlah'] : 0);
        $kode = trim((string) ($validated['kode'] ?? ''));
        $nama = trim((string) ($validated['nama'] ?? ''));
        $satuan = trim((string) ($validated['satuan'] ?? ''));
        $tanggal = $validated['tanggal'] ?? now()->toDateString();
        $jam = $validated['jam'] ?? now()->format('H:i:s');
        $bulan = $validated['bulan'] ?? now()->translatedFormat('F');
        $notes = $validated['notes'] ?? null;

        try {
            $history = DB::transaction(function () use ($validated, $kode, $nama, $satuan, $tanggal, $jam, $bulan, $type, $quantity, $notes) {
                $material = ! empty($validated['material_id'])
                    ? Material::lockForUpdate()->findOrFail($validated['material_id'])
                    : Material::firstOrCreate(
                        ['kode_barang' => $kode],
                        [
                            'nama_material' => $nama ?: 'Material Baru',
                            'jumlah' => 0,
                            'stok_akhir' => 0,
                            'min_stok' => 10,
                            'satuan' => $satuan ?: 'KG',
                            'status' => Material::statusForStock(0, 10),
                            'tanggal_transaksi' => $tanggal,
                            'jam_input' => $jam,
                            'bulan_simpanan' => $bulan,
                        ]
                    );

                if (! $material || empty($kode) && empty($validated['material_id'])) {
                    abort(422, 'Material tidak ditemukan atau kode barang wajib diisi.');
                }

                $material->nama_material = $nama ?: $material->nama_material;
                $material->satuan = $satuan ?: $material->satuan;
                $currentStok = (float) ($material->stok_akhir ?? $material->jumlah ?? 0);

                if ($type === 'OUT' && $currentStok < $quantity) {
                    abort(422, 'Stok tidak mencukupi untuk material ini.');
                }

                $currentStok += $type === 'OUT' ? -$quantity : $quantity;
                $material->fill([
                    'stok_akhir' => $currentStok,
                    'jumlah' => $currentStok,
                    'status' => Material::statusForStock($currentStok, (float) ($material->min_stok ?? 10)),
                    'tanggal_transaksi' => $tanggal,
                    'jam_input' => $jam,
                    'bulan_simpanan' => $bulan,
                ])->save();

                $transactionDate = Carbon::createFromFormat('Y-m-d H:i:s', "{$tanggal} {$jam}");
                $transaction = MaterialTransaction::create([
                    'material_id' => $material->id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'user_id' => 'WH-OP-092',
                    'notes' => $notes,
                ]);
                $transaction->forceFill([
                    'created_at' => $transactionDate,
                    'updated_at' => $transactionDate,
                ])->save();

                $history = MaterialMasuk::create([
                    'kode_barang' => $material->kode_barang,
                    'nama_material' => $material->nama_material,
                    'jumlah' => $quantity,
                    'satuan' => $material->satuan,
                    'tanggal_transaksi' => $tanggal,
                    'jam_input' => $jam,
                    'bulan_simpanan' => $bulan,
                ]);

                return ['history' => $history, 'stock' => $material->stok_akhir];
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getStatusCode());
        }

        return response()->json(['success' => true, 'data' => $history['history'], 'stock' => $history['stock']]);
    }
}
