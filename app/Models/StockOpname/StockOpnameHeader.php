<?php

namespace App\Models\StockOpname;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameHeader extends Model
{
    use HasFactory;

    protected $table = 'sto_headers';
    protected $primaryKey = 'id_sto_header';
    protected $fillable = [
        'issued_by',
        'issued_name',
        'wh_id',
        'wh_name',
        'organization_id',
        'year',
        'doc_date',
    ];

    public function stoLines()
    {
        return $this->hasMany(StockOpnameLine::class, 'sto_header_id', 'id_sto_header');
    }
}
