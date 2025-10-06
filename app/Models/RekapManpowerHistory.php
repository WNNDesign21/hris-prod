<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapManpowerHistory extends Model
{
    use HasFactory;

    protected $table = 'rekap_manpower_histories';

    protected $fillable = [
        'period',
        'data'
    ];

    protected $casts = [
        'period' => 'date',
        'data' => 'array'
    ];

    /**
     * Scope untuk mencari berdasarkan periode
     */
    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    /**
     * Scope untuk mencari berdasarkan tahun
     */
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('period', $year);
    }

    /**
     * Scope untuk mencari berdasarkan bulan dan tahun
     */
    public function scopeByMonthYear($query, $month, $year)
    {
        $period = sprintf('%d-%02d-01', $year, $month);
        return $query->where('period', $period);
    }

    /**
     * Get periode dalam format bulan-tahun
     */
    public function getFormattedPeriodAttribute()
    {
        return $this->period->format('m-Y');
    }

    /**
     * Get periode dalam format bulan tahun lengkap
     */
    public function getFullPeriodAttribute()
    {
        return $this->period->format('F Y');
    }
}
