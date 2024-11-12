<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'test_id',
        'score',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'score' => 'integer',
    ];

    /**
     * Get the user that owns the test result.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the test that owns the result.
     */
    public function test()
    {
        return $this->belongsTo(TestsList::class, 'test_id');
    }
}