<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_type',
        'leave_days',
        'year_leave',
    ];
}
