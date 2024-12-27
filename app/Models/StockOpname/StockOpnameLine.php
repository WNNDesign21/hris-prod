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
        'customer_id',
        'customer_name',
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
        'inputed_by',
        'inputed_name',
        'updated_by',
        'updated_name',
    ];

    public function stoHeader()
    {
        return $this->belongsTo(StockOpnameHeader::class, 'sto_header_id', 'id_sto_header');
    }

    private static function _query($dataFilter)
    {
        $data = self::select(
            'sto_lines.id_sto_line',
            'sto_lines.customer_id',
            'sto_lines.customer_name',
            'sto_lines.wh_id',
            'sto_lines.wh_name',
            'sto_lines.no_label',
            'sto_lines.part_code',
            'sto_lines.part_name',
            'sto_lines.part_desc',
            'sto_lines.product_id',
            'sto_lines.model',
            'sto_lines.quantity',
            'sto_lines.identitas_lot',
            'sto_lines.created_at',
            'sto_lines.updated_at',
            'sto_lines.updated_by',
            'sto_lines.updated_name',
            'sto_lines.inputed_by',
            'sto_lines.inputed_name',
            'sto_headers.year',
            'sto_headers.issued_name',
            'sto_headers.issued_by',
        );

        $data->leftJoin('sto_headers', 'sto_headers.id_sto_header', 'sto_lines.sto_header_id');

        if (isset($dataFilter['hasilSto']) == 'Y'){
            $data->whereNotNull('sto_lines.product_id');
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('sto_lines.customer_id', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_lines.customer_name', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_lines.wh_name', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_lines.no_label', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_lines.part_code', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_lines.part_name', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_lines.part_desc', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_lines.model', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_lines.quantity', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_lines.identitas_lot', 'ILIKE', "%{$search}%");
                    
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
