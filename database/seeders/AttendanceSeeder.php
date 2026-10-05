<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        $records = [
            ['employee_id' => 'EMP-001', 'date' => $today, 'clock_in' => '07:30:00', 'clock_out' => '16:00:00', 'status' => 'ON-TIME'],
            ['employee_id' => 'EMP-002', 'date' => $today, 'clock_in' => '08:00:00', 'clock_out' => '16:05:00', 'status' => 'LATE'],
            ['employee_id' => 'EMP-003', 'date' => $today, 'clock_in' => '07:40:00', 'clock_out' => '16:20:00', 'status' => 'ACTIVE'],
            ['employee_id' => 'EMP-004', 'date' => $today, 'clock_in' => '07:50:00', 'clock_out' => '16:10:00', 'status' => 'ON-TIME'],
        ];

        foreach ($records as $record) {
            Attendance::updateOrCreate(
                [
                    'employee_id' => $record['employee_id'],
                    'date' => $record['date']->toDateString(),
                ],
                [
                    'clock_in' => $record['clock_in'],
                    'clock_out' => $record['clock_out'],
                    'status' => $record['status'],
                ]
            );
        }
    }
}
