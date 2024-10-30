<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari konvensi
    protected $table = 'tests';

    // Kolom yang dapat diisi
    protected $fillable = [
        'name',
        'description',
    ];

    // Definisikan enum untuk name
    protected $casts = [
        'name' => 'string', // Laravel tidak mendukung enum secara langsung
    ];
}
