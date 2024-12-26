<?php

namespace App\Models\StockOpname;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class iDempiereModel extends Model
{
    use HasFactory;
    protected $connection = 'idempiere';
    public function scopeFromProduct($query)
    {
        return $query->from('m_product');
    }

    public function scopeFromWarehouse($query)
    {
        $organization_id = auth()->user()->organisasi_id;
        if ($organization_id == 1) {
           $ad_org_id = 1000001;
        } else {
            $ad_org_id = 1000002;
        }


        return $query->from('m_warehouse')->where('ad_org_id', $ad_org_id);
    }
    public function scopeFromCustomer($query)
    {
        return $query->from('c_bpartner')->where('iscustomer', 'Y');;
    }

}
