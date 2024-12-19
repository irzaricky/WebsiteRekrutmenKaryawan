<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => 'string',
        'status' => 'string',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    public function candidateDetail()
    {
        return $this->hasOne(CandidateDetail::class);
    }

    public function hrdDetail()
    {
        return $this->hasOne(HRDDetail::class);
    }
}