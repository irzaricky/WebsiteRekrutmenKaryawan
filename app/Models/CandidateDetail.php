<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nik',
        'address',
        'birth_date',
        'photo_path',
        'cv_path',
        'certificate_path',
        'education_level',
        'major',
        'institution',
        'graduation_year',
        'photo_status',
        'cv_status',
        'certificate_status'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'graduation_year' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}