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
        'education_level',
        'major',
        'institution',
        'graduation_year',
        'photo_status',
        'cv_status',
        'ijazah_smp_path',
        'ijazah_sma_path',
        'ijazah_d3_path',
        'ijazah_s1_path',
        'ijazah_s2_path',
        'ijazah_s3_path',
        'ijazah_smp_status',
        'ijazah_sma_status',
        'ijazah_d3_status',
        'ijazah_s1_status',
        'ijazah_s2_status',
        'ijazah_s3_status'
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