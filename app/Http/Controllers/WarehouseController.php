<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Material;
use App\Models\MaterialTransaction;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
        $year = (int) $request->query('year', 2026);
        $month = (int) $request->query('month', 9);

        abort_if($month < 1 || $month > 12 || $year < 2000 || $year > now()->year + 1, 422);

        $materials = Material::orderBy('nama_material', 'asc')->get();
        $transactions = MaterialTransaction::with('material')->where('type', 'OUT')->latest()->take(10)->get();
        $reportEnd = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        $stockByMaterial = MaterialTransaction::query()
            ->where('created_at', '<=', $reportEnd)
            ->select('material_id')
            ->selectRaw("SUM(CASE WHEN type = 'IN' THEN quantity ELSE -quantity END) AS stock_balance")
            ->groupBy('material_id')
            ->pluck('stock_balance', 'material_id');
        $stockReport = $materials->map(function (Material $material) use ($stockByMaterial): object {
            $stock = (float) $stockByMaterial->get($material->id, 0);

            return (object) [
                'kode_barang' => $material->kode_barang,
                'nama_material' => $material->nama_material,
                'satuan' => $material->satuan,
                'stok' => $stock,
                'status' => Material::statusForStock($stock, (float) ($material->min_stok ?? 10)),
            ];
        });
        $totalStokLaporan = (float) $stockReport->sum('stok');

        return view('pages.output-material', compact(
            'materials', 'transactions', 'stockReport', 'totalStokLaporan', 'year', 'month'
        ));
    }

    public function daftarStok(): View
    {
        $materials = Material::orderBy('nama_material', 'asc')->paginate(20);
        $totalMaterial = Material::count();
        $stokKritis = Material::whereIn('status', ['KRITIS', 'RENDAH'])->count();
        $totalStok = Material::sum('stok_akhir');

        return view('pages.daftar-stok', compact('materials', 'totalMaterial', 'stokKritis', 'totalStok'));
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
        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', now()->month);

        abort_if($month < 1 || $month > 12 || $year < 2000 || $year > now()->year + 1, 422);

        $periodStart = \Carbon\Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = $periodStart->copy()->endOfMonth()->endOfDay();
        $periodTransactions = MaterialTransaction::query()
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->where('type', 'OUT');

        $transactions = (clone $periodTransactions)
            ->with('material')
            ->latest()
            ->get();
        $totalTransaksi = (clone $periodTransactions)->count();
        $totalJumlahKeluar = (float) (clone $periodTransactions)->sum('quantity');
        $outgoingSummary = (clone $periodTransactions)
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
            'transactions', 'totalTransaksi', 'totalJumlahKeluar', 'jumlahBarangKeluar',
            'summaryMaterials', 'month', 'year'
        ));
    }

    public function laporanStok(Request $request): View
    {
        $year = (int) $request->query('year', 2026);
        $month = (int) $request->query('month', 9);

        abort_if($month < 1 || $month > 12 || $year < 2000 || $year > now()->year + 1, 422);

        $periodEnd = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        $stockByMaterial = MaterialTransaction::query()
            ->where('created_at', '<=', $periodEnd)
            ->select('material_id')
            ->selectRaw("SUM(CASE WHEN type = 'IN' THEN quantity ELSE -quantity END) AS stock_balance")
            ->groupBy('material_id')
            ->pluck('stock_balance', 'material_id');

        $materials = Material::orderBy('nama_material')->get();
        $stockReport = $materials->map(function (Material $material) use ($stockByMaterial): object {
            $stock = (float) $stockByMaterial->get($material->id, 0);

            return (object) [
                'kode_barang' => $material->kode_barang,
                'nama_material' => $material->nama_material,
                'satuan' => $material->satuan,
                'stok' => $stock,
                'status' => Material::statusForStock($stock, (float) ($material->min_stok ?? 10)),
            ];
        });
        $totalStok = (float) $stockReport->sum('stok');

        return view('pages.laporan-stok', compact('stockReport', 'totalStok', 'month', 'year'));
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
