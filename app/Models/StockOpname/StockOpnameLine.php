<?php

namespace App\Models\StockOpname;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameLine extends Model
{
    use HasFactory;
    protected $table = 'sto_lines';
    protected $primaryKey = 'id_sto_line';
    protected $fillable = [
        'sto_header_id',
        'customer',
        'wh_id',
        'wh_name',
        'no_label',
        'spec_size',
        'product_id',
        'part_code',
        'part_name',
        'part_desc',
        'model',
        'identitas_lot',
        'quantity',
        'status',
        'input_by',
        'input_date',
    ];

    public function stoHeader()
    {
        return $this->belongsTo(StockOpnameHeader::class, 'sto_header_id', 'id_sto_header');
    }

    private static function _query($dataFilter)
    {
        $data = self::select(
            'id_sto_line',
            'customer',
            'wh_id',
            'wh_name',
            'no_label',
            'part_code',
            'part_name',
            'part_desc',
            'model',
            'quantity',
            'identitas_lot',
            
        );

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('customer', 'ILIKE', "%{$search}%")
                    ->orWhere('wh_name', 'ILIKE', "%{$search}%")
                    ->orWhere('no_label', 'ILIKE', "%{$search}%")
                    ->orWhere('part_code', 'ILIKE', "%{$search}%")
                    ->orWhere('part_name', 'ILIKE', "%{$search}%")
                    ->orWhere('part_desc', 'ILIKE', "%{$search}%")
                    ->orWhere('model', 'ILIKE', "%{$search}%")
                    ->orWhere('quantity', 'ILIKE', "%{$search}%")
                    ->orWhere('identitas_lot', 'ILIKE', "%{$search}%");
                    
            });
        }
        $result = $data;
        return $result;
    }
    
    public static function getData($dataFilter, $settings)
    {
        return self::_query($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public static function countData($dataFilter)
    {
        return self::_query($dataFilter)->get()->count();
    }
}
