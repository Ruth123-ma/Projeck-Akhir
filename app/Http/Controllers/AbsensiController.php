<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class AbsensiController
{
    /**
     * Helper privat untuk mengambil ID Karyawan yang sedang login
     * atau fallback ke ID dummy jika tidak ada sesi login.
     */
    private function getEmployeeId()
    {
        if (Auth::check()) {
            return Auth::user()->employee_id ?? Auth::user()->id;
        }
        
        return 'LOG-OP-29481';
    }

    public function index()
    {
        $employeeId = $this->getEmployeeId();

        // Data profil user (mengambil dari sesi login jika ada)
        $userName = Auth::check() ? Auth::user()->name : 'Budi Santoso';
        $active_user = [
            'nama_karyawan' => $userName,
            'jabatan'       => Auth::check() ? (Auth::user()->position ?? 'Operator') : 'Senior Floor Operator',
            'nopek'         => $employeeId,
            'avatar'        => Auth::check() && Auth::user()->avatar 
                                ? Auth::user()->avatar 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=1E88E5&color=fff'
        ];

        $today = Carbon::today()->toDateString();
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        try {
            $absensi_log = Attendance::where('employee_id', $employeeId)
                ->orderBy('date', 'desc')
                ->take(5)
                ->get();

            $late_count = Attendance::where('employee_id', $employeeId)
                ->where('status', 'LATE')
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->count();

            $today_attendance = Attendance::where('employee_id', $employeeId)
                ->where('date', $today)
                ->first();

            // Hitung total jam kerja minggu ini
            $weeklyRecords = Attendance::where('employee_id', $employeeId)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->whereNotNull('clock_in')
                ->whereNotNull('clock_out')
                ->get();

            $totalMinutes = 0;
            foreach ($weeklyRecords as $record) {
                $clockIn = Carbon::parse($record->clock_in);
                $clockOut = Carbon::parse($record->clock_out);
                $totalMinutes += $clockOut->diffInMinutes($clockIn);
            }
            $totalHours = round($totalMinutes / 60, 1);

        } catch (Exception $e) {
            Log::error('Error AbsensiController@index: ' . $e->getMessage());

            $absensi_log = collect([]);
            $late_count = 0;
            $today_attendance = null;
            $totalHours = 0;
        }

        $weekly_summary = [
            'total_hours' => $totalHours,
            'target'      => '40.0 Hrs',
            'late_count'  => $late_count
        ];

        return view('pages.absensi', compact('active_user', 'absensi_log', 'weekly_summary', 'today_attendance'));
    }

    public function masuk(Request $request)
    {
        $employeeId  = $this->getEmployeeId();
        $now         = Carbon::now();
        $today       = $now->toDateString();
        $currentTime = $now->toTimeString();

        try {
            $attendance = Attendance::where('employee_id', $employeeId)
                ->where('date', $today)
                ->first();

            if ($attendance) {
                return back()->with('error', 'Anda sudah melakukan absen masuk hari ini!');
            }

            // Batas jam masuk (08:00:00)
            $status = $currentTime > '08:00:00' ? 'LATE' : 'ACTIVE';

            Attendance::create([
                'employee_id' => $employeeId,
                'date'        => $today,
                'clock_in'    => $currentTime,
                'status'      => $status,
            ]);

            return back()->with('success', 'Berhasil melakukan absen masuk!');
        } catch (Exception $e) {
            Log::error('Error AbsensiController@masuk: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses absen masuk!');
        }
    }

    public function keluar(Request $request)
    {
        $employeeId  = $this->getEmployeeId();
        $now         = Carbon::now();
        $today       = $now->toDateString();
        $currentTime = $now->toTimeString();

        try {
            $attendance = Attendance::where('employee_id', $employeeId)
                ->where('date', $today)
                ->first();

            if (!$attendance) {
                return back()->with('error', 'Anda belum melakukan absen masuk hari ini!');
            }

            if ($attendance->clock_out) {
                return back()->with('error', 'Anda sudah melakukan absen keluar hari ini!');
            }

            $attendance->update([
                'clock_out' => $currentTime,
                'status'    => $attendance->status === 'ACTIVE' ? 'ON-TIME' : $attendance->status,
            ]);

            return back()->with('success', 'Berhasil melakukan absen keluar!');
        } catch (Exception $e) {
            Log::error('Error AbsensiController@keluar: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses absen keluar!');
        }
    }
}