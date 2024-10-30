<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari konvensi
    protected $table = 'test_results';

    // Kolom yang dapat diisi
    protected $fillable = [
        'user_id',
        'test_id',
        'score',
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan model Test
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
