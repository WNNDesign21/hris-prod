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
        'organization_id',
    ];

    private static function _query($dataFilter)
    {
        $data = self::select(
            'sto_upload.id_sto_upload',
            'sto_upload.customer_id',
            'sto_upload.customer_name',
            'sto_upload.wh_id',
            'sto_upload.wh_name',
            'sto_upload.locator_id',
            'sto_upload.locator_name',
            'sto_upload.product_code',
            'sto_upload.product_name',
            'sto_upload.product_desc',
            'sto_upload.product_id',
            'sto_upload.model',
            // 'sto_upload.identitas_lot',
            'sto_upload.qty_book',
            'sto_upload.qty_count',
            'sto_upload.balance',
            'sto_upload.doc_date',
            'sto_upload.processed',
        );

        // $data->leftJoin('sto_headers', 'sto_headers.id_sto_header', 'sto_lines.sto_header_id');

        if (isset($dataFilter['hasilSto']) == 'Y'){
            $data->whereNotNull('sto_upload.product_id');
        }

        if (!empty($dataFilter['wh_id'])) {
            $data->whereIn('sto_upload.wh_id', $dataFilter['wh_id']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('sto_upload.customer_id', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_upload.customer_name', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_upload.wh_name', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_upload.locator_name', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_upload.no_label', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_upload.product_code', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_upload.product_name', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_upload.product_desc', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_upload.model', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_upload.qty_book', 'ILIKE', "%{$search}%")
                    ->orWhere('sto_upload.qty_count', 'ILIKE', "%{$search}%");
                    // ->orWhere('sto_upload.identitas_lot', 'ILIKE', "%{$search}%");
                    
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

        // if (!empty($dataFilter['wh_name'])) {
        //     $query->where('wh_name', $dataFilter['wh_name']);
        // }
    }
    public static function countData($dataFilter)
    {
        return self::_query($dataFilter)->get()->count();
        // if (!empty($dataFilter['wh_name'])) {
        //     $query->where('wh_name', $dataFilter['wh_name']);
        // }
    }


}
