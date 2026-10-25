<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Material;
use App\Models\MaterialTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WarehouseController
{
    public function dashboard(): View
    {
        $totalMaterial = Material::query()->distinct()->count('nama_material');
        $stokKritis = Material::whereIn('status', ['KRITIS', 'RENDAH'])->count();
        $criticalMaterials = Material::whereIn('status', ['KRITIS', 'RENDAH'])->orderBy('stok_akhir', 'asc')->take(5)->get();
        $materials = Material::orderBy('stok_akhir', 'asc')->take(5)->get();
        $totalStok = Material::sum('stok_akhir');
        $attendanceToday = Attendance::whereDate('date', today())->count();
        $activeItems = Material::where('status', 'NORMAL')->count();
        $unconfirmedItems = Material::whereNull('status')->count();
        $activePercentage = $totalMaterial > 0
            ? (int) round(($activeItems / $totalMaterial) * 100)
            : 0;

        // Load team members to show on the dashboard in the requested order
        $memberOrder = ['M.Firdaus Madjid', 'Ermawati', 'Jumartadi', 'Mujiono'];
        $members = \App\Models\Member::orderByRaw('FIELD(name, ?, ?, ?, ?) ASC', $memberOrder)->get();

        return view('pages.dashboard', compact(
            'totalMaterial', 'stokKritis', 'criticalMaterials', 'materials', 'totalStok',
            'attendanceToday', 'members', 'activeItems', 'unconfirmedItems', 'activePercentage'
        ));
    }

    public function absensi(): View
    {
        $attendances = Attendance::latest()->take(5)->get();
        $today = now()->format('l, d F Y');

        return view('pages.absensi', compact('attendances', 'today'));
    }

   public function absensiKaryawan(Request $request): View
    {
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);

        abort_if($month < 1 || $month > 12 || $year < now()->year - 5 || $year > now()->year + 1, 404);

        $attendances = Attendance::with('member')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderByDesc('date')
            ->orderByDesc('clock_in')
            ->paginate(20)
            ->withQueryString();
         $monthAttendance = Attendance::whereYear('date', $year)->whereMonth('date', $month);
        $totalHadir = (clone $monthAttendance)->count();
        $totalTerlambat = (clone $monthAttendance)->whereIn('status', ['LATE', 'TERLAMBAT'])->count();
        $hadirHariIni = Attendance::whereDate('date', today())->count();
        $belumPulang = Attendance::whereDate('date', today())->whereNotNull('clock_in')->whereNull('clock_out')->count();
        $totalKaryawan = Member::count();
        $members = Member::orderBy('name')->get(['employee_no', 'name', 'role']);

        if ($totalKaryawan === 0) {
            $totalKaryawan = Attendance::query()->select('employee_id')->distinct()->count('employee_id');
        }

          return view('pages.absensi-karyawan', compact(
            'attendances', 'totalHadir', 'totalTerlambat', 'hadirHariIni', 'belumPulang',
            'totalKaryawan', 'members', 'year', 'month'
        ));
    }

    public function inputMaterial(): View
    {
        $materials = Material::orderBy('nama_material', 'asc')->get();
        $transactions = MaterialTransaction::with('material')->where('type', 'IN')->latest()->take(10)->get();

        return view('pages.input-material', compact('materials', 'transactions'));
    }

    public function outputMaterial(Request $request): View
    {
        $validated = $request->validate([
            'cari' => 'nullable|string|max:100',
            'bulan' => 'nullable|date_format:Y-m',
        ]);

        $materials = Material::orderBy('nama_material', 'asc')->get();
        $transactions = $this->outputHistoryQuery($validated)
            ->paginate(20)
            ->withQueryString();
        $availableMonths = MaterialTransaction::query()
            ->where('type', 'OUT')
            ->whereNotNull('created_at')
            ->select('created_at')
            ->distinct()
            ->orderByDesc('created_at')
            ->pluck('created_at')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m'))
            ->unique()
            ->values();

        return view('pages.output-material', [
            'materials' => $materials,
            'transactions' => $transactions,
            'availableMonths' => $availableMonths,
            'search' => $validated['cari'] ?? '',
            'selectedMonth' => $validated['bulan'] ?? '',
        ]);
    }

    public function exportOutputExcel(Request $request)
    {
        $validated = $request->validate([
            'cari' => 'nullable|string|max:100',
            'bulan' => 'nullable|date_format:Y-m',
        ]);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Material Keluar');
        $headers = ['Tanggal', 'Kode Material', 'Nama Material', 'Jumlah Keluar', 'Satuan', 'Keterangan'];

        foreach ($headers as $index => $header) {
            $column = chr(ord('A') + $index);
            $sheet->setCellValueExplicit($column.'1', $header, DataType::TYPE_STRING);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        foreach ($this->outputHistoryQuery($validated)->get() as $index => $transaction) {
            $row = $index + 2;
            $values = [
                'A' => $transaction->created_at?->format('d/m/Y H:i') ?? '',
                'B' => $transaction->material?->kode_barang ?? '-',
                'C' => $transaction->material?->nama_material ?? '-',
                'E' => $transaction->material?->satuan ?? '',
                'F' => $transaction->notes ?? '-',
            ];

            foreach ($values as $column => $value) {
                $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
            }

            $sheet->setCellValueExplicit('D'.$row, (float) $transaction->quantity, DataType::TYPE_NUMERIC);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, 'riwayat-material-keluar-'.now()->format('Ymd-His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportOutputPdf(Request $request): View
    {
        $validated = $request->validate([
            'cari' => 'nullable|string|max:100',
            'bulan' => 'nullable|date_format:Y-m',
        ]);
        $transactions = $this->outputHistoryQuery($validated)->get();

        return view('pages.output-material-pdf', compact('transactions'));
    }

    private function outputHistoryQuery(array $filters): Builder
    {
        $query = MaterialTransaction::query()
            ->with('material')
            ->where('type', 'OUT')
            ->latest();

        if (! empty($filters['bulan'])) {
            [$year, $month] = explode('-', $filters['bulan']);
            $query->whereYear('created_at', (int) $year)
                ->whereMonth('created_at', (int) $month);
        }

        $search = trim($filters['cari'] ?? '');
        if ($search !== '') {
            $query->whereHas('material', function (Builder $materialQuery) use ($search): void {
                $materialQuery->where('kode_barang', 'like', '%'.$search.'%')
                    ->orWhere('nama_material', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function daftarStok(Request $request): View
    {
        $validated = $request->validate([
            'cari' => 'nullable|string|max:100',
            'bulan' => 'nullable|date_format:Y-m',
        ]);

        $materials = $this->stockListQuery($validated)->paginate(20)->withQueryString();
        $totalMaterial = Material::count();
        $stokKritis = Material::whereIn('status', ['KRITIS', 'RENDAH'])->count();
        $totalStok = Material::sum('stok_akhir');
        $availableMonths = Material::query()
            ->whereNotNull('tanggal_transaksi')
            ->select('tanggal_transaksi')
            ->distinct()
            ->orderByDesc('tanggal_transaksi')
            ->pluck('tanggal_transaksi')
            ->map(fn ($date) => Carbon::parse($date)->format('Y-m'))
            ->unique()
            ->values();

        return view('pages.daftar-stok', [
            'materials' => $materials,
            'totalMaterial' => $totalMaterial,
            'stokKritis' => $stokKritis,
            'totalStok' => $totalStok,
            'availableMonths' => $availableMonths,
            'search' => $validated['cari'] ?? '',
            'selectedMonth' => $validated['bulan'] ?? '',
        ]);
    }

    public function exportStockExcel(Request $request)
    {
        $validated = $request->validate([
            'cari' => 'nullable|string|max:100',
            'bulan' => 'nullable|date_format:Y-m',
        ]);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Stok');
        $headers = ['Kode Barang', 'Nama Material', 'Lokasi', 'Stok Akhir', 'Min Stok', 'Satuan', 'Status', 'Tanggal'];

        foreach ($headers as $index => $header) {
            $column = chr(ord('A') + $index);
            $sheet->setCellValueExplicit($column.'1', $header, DataType::TYPE_STRING);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        foreach ($this->stockListQuery($validated)->get() as $index => $material) {
            $row = $index + 2;
            $values = [
                'A' => $material->kode_barang,
                'B' => $material->nama_material,
                'C' => $material->lokasi ?? 'Gudang Utama',
                'F' => $material->satuan,
                'G' => $material->status ?? 'NORMAL',
                'H' => $material->tanggal_transaksi?->format('d/m/Y') ?? '',
            ];

            foreach ($values as $column => $value) {
                $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
            }

            $sheet->setCellValueExplicit('D'.$row, (float) $material->stok_akhir, DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicit('E'.$row, (float) ($material->min_stok ?? 10), DataType::TYPE_NUMERIC);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, 'daftar-stok-gudang-'.now()->format('Ymd-His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportStockPdf(Request $request): View
    {
        $validated = $request->validate([
            'cari' => 'nullable|string|max:100',
            'bulan' => 'nullable|date_format:Y-m',
        ]);
        $materials = $this->stockListQuery($validated)->get();

        return view('pages.daftar-stok-pdf', compact('materials'));
    }

    private function stockListQuery(array $filters): Builder
    {
        $query = Material::query()->orderBy('nama_material');

        if (! empty($filters['bulan'])) {
            [$year, $month] = explode('-', $filters['bulan']);
            $query->whereYear('tanggal_transaksi', (int) $year)
                ->whereMonth('tanggal_transaksi', (int) $month);
        }

        $search = trim($filters['cari'] ?? '');
        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('kode_barang', 'like', '%'.$search.'%')
                    ->orWhere('nama_material', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function createMaterial(): View
    {
        return view('pages.material-form', ['material' => new Material()]);
    }

    public function storeMaterial(Request $request): RedirectResponse
    {
        if (is_string($request->input('kode_barang'))) {
            $request->merge(['kode_barang' => trim($request->input('kode_barang'))]);
        }

        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:50', Rule::unique('materials', 'kode_barang')],
            'nama_material' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:20'],
            'min_stok' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'stok_awal' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        DB::transaction(function () use ($validated): void {
            $initialStock = (float) $validated['stok_awal'];
            $minimumStock = (float) $validated['min_stok'];
            $material = Material::create([
                'kode_barang' => trim($validated['kode_barang']),
                'nama_material' => trim($validated['nama_material']),
                'jumlah' => $initialStock,
                'stok_akhir' => $initialStock,
                'min_stok' => $minimumStock,
                'satuan' => trim($validated['satuan']),
                'status' => Material::statusForStock($initialStock, $minimumStock),
                'tanggal_transaksi' => today(),
                'jam_input' => now()->format('H:i:s'),
                'bulan_simpanan' => now()->format('F'),
            ]);

            if ($initialStock > 0) {
                MaterialTransaction::create([
                    'material_id' => $material->id,
                    'type' => 'IN',
                    'quantity' => $initialStock,
                    'user_id' => Auth::check() ? (string) Auth::id() : 'WH-OP-092',
                    'notes' => 'Stok awal',
                ]);
            }
        });

        return redirect()->route('daftar-stok')->with('success', 'Material berhasil ditambahkan.');
    }

    public function editMaterial(Material $material): View
    {
        return view('pages.material-form', compact('material'));
    }

    public function updateMaterial(Request $request, Material $material): RedirectResponse
    {
        if (is_string($request->input('kode_barang'))) {
            $request->merge(['kode_barang' => trim($request->input('kode_barang'))]);
        }

        $validated = $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('materials', 'kode_barang')->ignore($material->id),
            ],
            'nama_material' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:20'],
            'min_stok' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        $material->update([
            'kode_barang' => trim($validated['kode_barang']),
            'nama_material' => trim($validated['nama_material']),
            'satuan' => trim($validated['satuan']),
            'min_stok' => $validated['min_stok'],
            'status' => Material::statusForStock((float) $material->stok_akhir, (float) $validated['min_stok']),
        ]);

        return redirect()->route('daftar-stok')->with('success', 'Data material berhasil diperbarui.');
    }

    public function laporan(): View
    {
        $materials = Material::orderBy('stok_akhir', 'asc')->get();
        $totalStok = Material::sum('stok_akhir');
        $stokKritis = Material::whereIn('status', ['KRITIS', 'RENDAH'])->count();
        $optimal = Material::where('status', 'NORMAL')->count();

        return view('pages.laporan', compact('materials', 'totalStok', 'stokKritis', 'optimal'));
    }

    public function laporanMaterial(Request $request): View
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|min:2000|max:'.(now()->year + 1),
            'month' => 'nullable|integer|min:1|max:12',
            'cari' => 'nullable|string|max:100',
        ]);
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        $search = trim($validated['cari'] ?? '');

        abort_if($month < 1 || $month > 12 || $year < 2000 || $year > now()->year + 1, 422);

        $periodStart = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = $periodStart->copy()->endOfMonth()->endOfDay();
        $periodTransactions = $this->reportTransactionsQuery($periodStart, $periodEnd, $search);
        $outgoingTransactions = (clone $periodTransactions)->where('type', 'OUT');
        $incomingTransactions = (clone $periodTransactions)->where('type', 'IN');

        $transactions = (clone $outgoingTransactions)
            ->latest()
            ->get();
        $incoming = (clone $incomingTransactions)->latest()->get();
        $totalTransaksi = (clone $outgoingTransactions)->count();
        $totalTransaksiMasuk = (clone $incomingTransactions)->count();
        $jumlahBarangMasuk = (clone $incomingTransactions)->distinct()->count('material_id');
        $outgoingSummary = (clone $outgoingTransactions)
            ->select('material_id')
            ->selectRaw('SUM(quantity) AS total_keluar')
            ->groupBy('material_id')
            ->get()
            ->sortBy(fn ($row) => $row->material_id);
        $summaryMaterials = Material::whereIn('id', $outgoingSummary->pluck('material_id'))
            ->orderBy('nama_material')
            ->get()
            ->map(function (Material $material) use ($outgoingSummary): object {
                $movement = $outgoingSummary->firstWhere('material_id', $material->id);

                return (object) [
                    'kode_barang' => $material->kode_barang,
                    'nama_material' => $material->nama_material,
                    'satuan' => $material->satuan,
                    'keluar' => (float) $movement->total_keluar,
                ];
            });
        $jumlahBarangKeluar = $summaryMaterials->count();

        return view('pages.laporan-material', compact(
            'transactions', 'totalTransaksi', 'jumlahBarangKeluar',
            'summaryMaterials', 'incoming', 'totalTransaksiMasuk', 'jumlahBarangMasuk',
            'month', 'year', 'search'
        ));
    }

    public function exportMaterialReportExcel(Request $request)
    {
        [$periodStart, $periodEnd, $year, $month, $search] = $this->materialReportFilters($request);
        $transactions = $this->reportTransactionsQuery($periodStart, $periodEnd, $search)
            ->with('material')
            ->orderByDesc('created_at')
            ->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Masuk Keluar');
        $headers = ['Jenis Transaksi', 'Tanggal', 'Kode Material', 'Nama Material', 'Jumlah', 'Satuan', 'User', 'Keterangan'];

        foreach ($headers as $index => $header) {
            $column = chr(ord('A') + $index);
            $sheet->setCellValueExplicit($column.'1', $header, DataType::TYPE_STRING);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        foreach ($transactions as $index => $transaction) {
            $row = $index + 2;
            $values = [
                'A' => $transaction->type === 'IN' ? 'Masuk' : 'Keluar',
                'B' => $transaction->created_at?->format('d/m/Y H:i') ?? '',
                'C' => $transaction->material?->kode_barang ?? '-',
                'D' => $transaction->material?->nama_material ?? '-',
                'F' => $transaction->material?->satuan ?? '',
                'G' => $transaction->user_id ?? 'System',
                'H' => $transaction->notes ?? '-',
            ];

            foreach ($values as $column => $value) {
                $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
            }

            $sheet->setCellValueExplicit('E'.$row, (float) $transaction->quantity, DataType::TYPE_NUMERIC);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, sprintf('laporan-material-%04d-%02d-%s.xlsx', $year, $month, now()->format('His')), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportMaterialReportPdf(Request $request): View
    {
        [$periodStart, $periodEnd, $year, $month, $search] = $this->materialReportFilters($request);
        $transactions = $this->reportTransactionsQuery($periodStart, $periodEnd, $search)
            ->with('material')
            ->orderByDesc('created_at')
            ->get();

        return view('pages.laporan-material-pdf', compact('transactions', 'year', 'month', 'search'));
    }

    private function materialReportFilters(Request $request): array
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|min:2000|max:'.(now()->year + 1),
            'month' => 'nullable|integer|min:1|max:12',
            'cari' => 'nullable|string|max:100',
        ]);

        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);
        abort_if($month < 1 || $month > 12 || $year < 2000 || $year > now()->year + 1, 422);

        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = $periodStart->copy()->endOfMonth()->endOfDay();

        return [$periodStart, $periodEnd, $year, $month, trim($validated['cari'] ?? '')];
    }

    private function reportTransactionsQuery(Carbon $periodStart, Carbon $periodEnd, string $search): Builder
    {
        $query = MaterialTransaction::query()
            ->with('material')
            ->whereBetween('created_at', [$periodStart, $periodEnd]);

        if ($search !== '') {
            $query->whereHas('material', function (Builder $materialQuery) use ($search): void {
                $materialQuery->where('kode_barang', 'like', '%'.$search.'%')
                    ->orWhere('nama_material', 'like', '%'.$search.'%');
            });
        }

        return $query;
    }

    public function laporanStok(Request $request): View
    {
        $filters = $this->stockReportFilters($request);
        [$year, $month, $search] = $filters;
        $stockReport = $this->stockReportData($year, $month, $search);
        $totalStok = (float) $stockReport->sum('stok');

        return view('pages.laporan-stok', compact('stockReport', 'totalStok', 'month', 'year', 'search'));
    }

    public function exportStockReportExcel(Request $request)
    {
        [$year, $month, $search] = $this->stockReportFilters($request);
        $stockReport = $this->stockReportData($year, $month, $search);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Stok');
        $headers = ['Kode Material', 'Nama Material', 'Saldo Akhir', 'Satuan', 'Status'];

        foreach ($headers as $index => $header) {
            $column = chr(ord('A') + $index);
            $sheet->setCellValueExplicit($column.'1', $header, DataType::TYPE_STRING);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        foreach ($stockReport as $index => $material) {
            $row = $index + 2;
            foreach (['A' => $material->kode_barang, 'B' => $material->nama_material, 'D' => $material->satuan, 'E' => $material->status] as $column => $value) {
                $sheet->setCellValueExplicit($column.$row, (string) $value, DataType::TYPE_STRING);
            }
            $sheet->setCellValueExplicit('C'.$row, (float) $material->stok, DataType::TYPE_NUMERIC);
        }

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, sprintf('laporan-stok-%04d-%02d-%s.xlsx', $year, $month, now()->format('His')), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportStockReportPdf(Request $request): View
    {
        [$year, $month, $search] = $this->stockReportFilters($request);
        $stockReport = $this->stockReportData($year, $month, $search);
        $totalStok = (float) $stockReport->sum('stok');

        return view('pages.laporan-stok-pdf', compact('stockReport', 'totalStok', 'month', 'year', 'search'));
    }

    private function stockReportFilters(Request $request): array
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|min:2000|max:'.(now()->year + 1),
            'month' => 'nullable|integer|min:1|max:12',
            'cari' => 'nullable|string|max:100',
        ]);
        $year = (int) $request->query('year', 2026);
        $month = (int) $request->query('month', 9);
        abort_if($month < 1 || $month > 12 || $year < 2000 || $year > now()->year + 1, 422);

        return [$year, $month, trim($validated['cari'] ?? '')];
    }

    private function stockReportData(int $year, int $month, string $search)
    {
        $periodEnd = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        $stockByMaterial = MaterialTransaction::query()
            ->where('created_at', '<=', $periodEnd)
            ->select('material_id')
            ->selectRaw("SUM(CASE WHEN type = 'IN' THEN quantity ELSE -quantity END) AS stock_balance")
            ->groupBy('material_id')
            ->pluck('stock_balance', 'material_id');

        $materials = Material::query()
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where('kode_barang', 'like', '%'.$search.'%')
                    ->orWhere('nama_material', 'like', '%'.$search.'%');
            })
            ->orderBy('nama_material')
            ->get();

        return $materials->map(function (Material $material) use ($stockByMaterial): object {
            $stock = (float) $stockByMaterial->get($material->id, 0);

            return (object) [
                'kode_barang' => $material->kode_barang,
                'nama_material' => $material->nama_material,
                'satuan' => $material->satuan,
                'stok' => $stock,
                'status' => Material::statusForStock($stock, (float) ($material->min_stok ?? 10)),
            ];
        });
    }

    // Show form to create monthly attendance batch
    public function createAbsensi(): RedirectResponse
    {
        return redirect()->route('absensi-karyawan');
    }

    public function storeAbsensi(Request $request): RedirectResponse
    {
        $hasManualAttendance = $request->filled('name') || $request->filled('employee_no');

        if ($hasManualAttendance) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'employee_no' => 'required|string|max:50',
                'jabatan' => 'required|string|max:255',
            ]);

            $employeeNo = strtoupper(trim($validated['employee_no']));
            $member = Member::updateOrCreate(
                ['employee_no' => $employeeNo],
                ['name' => trim($validated['name']), 'role' => $validated['jabatan']]
            );

            $today = now()->toDateString();
            $attendance = Attendance::firstOrCreate(
                ['employee_id' => $member->employee_no, 'date' => $today],
                [
                    'clock_in' => now()->toTimeString(),
                    'clock_out' => null,
                    'status' => now()->format('H:i:s') > '08:00:00' ? 'TERLAMBAT' : 'HADIR',
                ]
            );

            $message = $attendance->wasRecentlyCreated
                ? 'Absensi masuk berhasil dicatat untuk ' . $member->name . '.'
                : $member->name . ' sudah melakukan absensi masuk hari ini.';

            return redirect()->route('absensi-karyawan', ['year' => now()->year, 'month' => now()->month])->with(
                $attendance->wasRecentlyCreated ? 'success' : 'info',
                $message
            );
        }

        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'employees' => 'nullable|array',
        ]);

        $date = \Carbon\Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()->toDateString();
        $employees = $validated['employees'] ?? ['Mujiono','Jumartadi','Ermawati','M.Firdaus Madjid'];

        foreach ($employees as $emp) {
            Attendance::updateOrCreate(
                ['employee_id' => $emp, 'date' => $date],
                ['clock_in' => null, 'clock_out' => null, 'status' => 'ACTIVE']
            );
        }

        return redirect()->route('absensi-karyawan', ['year' => substr($date,0,4), 'month' => intval(substr($date,5,2))])->with('success', 'Absensi bulan disimpan.');
    }

    public function clockOut(Attendance $attendance): RedirectResponse
    {
        if (!$attendance->date->isToday()) {
            return back()->with('error', 'Absensi pulang hanya dapat dicatat pada tanggal hari ini.');
        }

        if (!$attendance->clock_in) {
            return back()->with('error', 'Karyawan belum melakukan absensi masuk.');
        }

        if ($attendance->clock_out) {
            return back()->with('info', 'Absensi pulang karyawan ini sudah tercatat.');
        }

        $attendance->update(['clock_out' => now()->toTimeString()]);

        return back()->with('success', 'Absensi pulang berhasil dicatat.');
    }

    public function storeTransaction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'type'        => 'required|in:IN,OUT',
            'quantity'    => 'required|numeric|min:0.01',
            'notes'       => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $material = Material::lockForUpdate()->findOrFail($validated['material_id']);

                // Pastikan stok_akhir memiliki nilai awal jika null
                $currentStok = $material->stok_akhir ?? $material->jumlah ?? 0;

                // 1. Transaksi Barang Keluar (OUT)
                if ($validated['type'] === 'OUT') {
                    if ($currentStok < $validated['quantity']) {
                        throw new Exception("Stok tidak mencukupi! Sisa stok saat ini: {$currentStok} {$material->satuan}.");
                    }
                    $currentStok -= $validated['quantity'];
                }

                // 2. Transaksi Barang Masuk (IN)
                if ($validated['type'] === 'IN') {
                    $currentStok += $validated['quantity'];
                }

                // 3. Update data stok dan status
                $minStok = (float) ($material->min_stok ?? 10);
                $material->stok_akhir = $currentStok;
                $material->jumlah = $currentStok;
                $material->status = Material::statusForStock((float) $currentStok, $minStok);
                $material->save();

                // 4. Simpan Riwayat Transaksi
                $userId = Auth::check() ? (Auth::user()->employee_id ?? Auth::id()) : 'WH-OP-092';

                MaterialTransaction::create([
                    'material_id' => $material->id,
                    'type'        => $validated['type'],
                    'quantity'    => $validated['quantity'],
                    'user_id'     => $userId,
                    'notes'       => $validated['notes'] ?? null,
                ]);
            });

            return back()->with('success', 'Transaksi persediaan berhasil dicatat!');

        } catch (Exception $e) {
            Log::error('Error WarehouseController@storeTransaction: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

}
