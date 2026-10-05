<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'employee_no',
        'role',
        'phone',
        'email',
        'website',
        'avatar_url',
    ];
}
