<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak mengikuti konvensi Laravel
    protected $table = 'job_postings';

    // Tentukan kolom yang dapat diisi massal
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'salary',
        'education',
        'slug',
        'job_type',
        'province_id',
        'status',
        'city_id',
        'district_id',
    ];

    // Jika Anda menggunakan relasi, tambahkan metode untuk relasi di sini
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
