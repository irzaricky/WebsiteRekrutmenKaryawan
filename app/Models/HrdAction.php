<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrdAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'hrd_id',
        'user_id',
        'action_type',
        'test_result_id',
        'details',
    ];

    public function hrd()
    {
        return $this->belongsTo(User::class, 'hrd_id');
    }

    // Rename 'user' to 'candidate' for better semantics
    public function candidate()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function testResult()
    {
        return $this->belongsTo(TestResult::class, 'test_result_id');
    }
}