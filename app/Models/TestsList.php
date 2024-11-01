<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestsList extends Model
{
    use HasFactory;

    protected $table = 'tests_list';

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'name' => 'string',
    ];

    public function testResults()
    {
        return $this->hasMany(TestResult::class, 'test_id');
    }
}