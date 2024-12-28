<?php

namespace App\Models\StockOpname;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameUpload extends Model
{
    use HasFactory;

    protected $table = 'sto_upload';
    protected $primaryKey = 'id_sto_upload';
    protected $fillable = [
        'wh_id',
        'wh_name',
        'locator_id',
        'locator_name',
        'customer_id',
        'customer_name',
        'product_id',
        'product_code',
        'product_name',
        'product_desc',
        'model',
        'qty_book',
        'qty_count',
        'balance',
        'doc_date',
        'processed',
    ];
}
