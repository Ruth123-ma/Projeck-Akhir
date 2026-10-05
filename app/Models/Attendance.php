<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
    ];

    /**
     * Format tipe data atribut (Casting).
     */
     protected $casts = [
        'date' => 'date',
        'clock_in' => 'string',
        'clock_out' => 'string',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'employee_id', 'employee_no');
    }    public function getEmployeeNameAttribute(): string
    {
        if (empty($this->employee_id)) {
            return 'Karyawan';
        }

        $member = $this->relationLoaded('member') ? $this->member : $this->member()->first();

        return $member ? $member->name : $this->employee_id;
    }

    public function getPositionAttribute(): string
    {
        if (empty($this->employee_id)) {
            return 'Staff Gudang';
        }

        $member = $this->relationLoaded('member') ? $this->member : $this->member()->first();

        return $member ? $member->role : 'Staff Gudang';
    }

    public function getCheckInTimeAttribute(): string
    {
        return $this->clock_in ? $this->clock_in : '08:00';
    }
}
