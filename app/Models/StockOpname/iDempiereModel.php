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
    public function scopeFromLocator($query)
    {
        return $query->from('m_locator');
    }

    public function scopeFromStorageOnHand($query)
    {
        return $query->from('m_storageonhand');
    }

    public function scopeFromWarehouse($query)
    {
        $organization_id = auth()->user()->organisasi_id;
        if ($organization_id == 1) {
           $ad_org_id = 1000001;
        } else {
            $ad_org_id = 1000002;
        }


        return $query->from('m_warehouse')
                    ->where('ad_org_id', $ad_org_id)
                    ->where('isactive', 'Y');
    }

    public static function getLocator($warehouse_id)
    {
        $query = self::fromLocator()->select(
            'm_locator.m_locator_id',
            'm_locator.value');

        $query->where('m_warehouse_id', $warehouse_id)->where('isactive', 'Y');


        return $query->first();
    }

    public function scopeFromCustomer($query)
    {
        return $query
        ->from('c_bpartner');
        // ->from('c_bpartner')->where('isactive', 'Y');

    }
    public function scopeCustomerProduct($query)
    {
        return $query
        ->from('m_product')
        ->leftJoin('c_bpartner_product', 'm_product.id', 'c_bpartner_product.product_id')
        ->leftJoin('c_bpartner', 'c_bpartner_product.bpartner_id', 'c_bpartner.id')
        ->select('m_product.*', 'c_bpartner.name as partner_name');

    }


    public static function getProduct($product_id)
    {
        $query = self::fromProduct()->select(
            'm_product.m_product_id',
            'm_product.name',
            'm_product.value',
            'm_product.description',
            'm_product.classification',
            'm_product.weight',
            'c_uom.name as uom',
            'c_bpartner.name as partner_name',
            'c_bpartner.c_bpartner_id as partner_id',
        );

        $query->leftJoin('c_bpartner_product', 'm_product.m_product_id', 'c_bpartner_product.m_product_id')
            ->leftJoin('c_uom', 'm_product.c_uom_id', 'c_uom.c_uom_id')
            ->leftJoin('c_bpartner', 'c_bpartner_product.c_bpartner_id', 'c_bpartner.c_bpartner_id')
            ->where('m_product.isactive', 'Y')
            ->where('m_product.m_product_id', $product_id);

        return $query->first();

    }

    public static function getQuantityBook($warehouse_id, $product_id)
    {
        $query = self::fromStorageOnHand()->selectRaw('SUM(m_storageonhand.qtyonhand) as QtyOnHand');
        $query->leftJoin('m_locator', 'm_storageonhand.m_locator_id', 'm_locator.m_locator_id');

        $query->where('m_locator.m_warehouse_id', $warehouse_id)
            ->where('m_storageonhand.m_product_id', $product_id);
        
        return $query->first();
    }

}
