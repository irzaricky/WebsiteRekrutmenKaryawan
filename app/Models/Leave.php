<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'employee_name',
        'leave_type',
        'remaining_leave',
        'date_from',
        'date_to',
        'leave_date',
        'leave_day',
        'number_of_day',
        'reason',
        'status',
        'approved_by',
    ];

}
