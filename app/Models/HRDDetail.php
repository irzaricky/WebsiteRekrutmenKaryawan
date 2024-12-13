<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HRDDetail extends Model
{
    use HasFactory;

    protected $table = 'hrd_details';

    protected $fillable = [
        'user_id',
        'nik',
        'address',
        'birth_date',
        'photo_path'
    ];

    protected $casts = [
        'birth_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
