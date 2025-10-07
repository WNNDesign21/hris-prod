<?php

namespace Database\Seeders;

use Faker\Factory as faker;
use Illuminate\Database\Seeder;
use App\Models\StockOpname\StockOpnameHeader;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class StoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = faker::create('id_ID');







        // $getProducts = DB::connection('idempiere')->table('m_product')
        //     ->select('m_product_id', 'value', 'name', 'description', 'classification')
        //     ->where([['isactive', '=', "Y"]])->get();

        //batas looping
        $org = '1000002';
        $getWarehouses = DB::connection('idempiere')->table('m_warehouse')
            ->select('m_warehouse_id', 'name')->where('ad_org_id', $org)->get();

        $wh_id = $getWarehouses->random(1)[0]->m_warehouse_id;
        $wh_name = $getWarehouses->random(1)[0]->name;



        $stoHeader = StockOpnameHeader::create([
            'year' => now()->format('m-Y'),
            'issued_name' => $faker->name(),
            'issued_by' => 2,
            'organization_id' => $org,
            'doc_date' => now(),
            'wh_name' => $wh_name,
            'wh_id' => $wh_id,
        ]);

        $data = [];

        $customers = ['1000207'];
        for ($i = 0; $i < 10; $i++) {

            // $getCustomers = DB::connection('idempiere')->table('c_bpartner')
            //     ->select('c_bpartner_id', 'name')
            //     ->where([['iscustomer', "=", 'Y'], ['isactive', "=", "Y"]])->get();

            // $partnerProducts = DB::connection('idempiere')->table('c_bpartner_product')
            //     ->where('c_bpartner_id', $getCustomers->random(1)[0]->c_bpartner_id)->get();

            $getCustomers = DB::connection('idempiere')->table('c_bpartner')
                ->select('c_bpartner_id', 'name')
                ->where('c_bpartner_id', '1000010')->first();

            $getProducts = DB::connection('idempiere')->table('m_product')
                ->leftJoin('c_bpartner_product', 'm_product.m_product_id', '=', 'c_bpartner_product.m_product_id')
                ->select('m_product.m_product_id', 'm_product.value', 'm_product.name', 'm_product.description', 'classification')
                ->where([
                    ['m_product.isactive', '=', "Y"],
                    ['c_bpartner_product.c_bpartner_id', $getCustomers->c_bpartner_id]
                ])->get();

            $product = $getProducts->random(1);

            $data[] = [
                'customer_id' => $getCustomers->c_bpartner_id,
                'customer_name' => $getCustomers->name,
                'wh_id' => $wh_id,
                'wh_name' => $wh_name,
                'no_label' => $faker->randomNumber(5),
                'spec_size' => $faker->randomNumber(5),
                'product_id' => $product[0]->m_product_id,
                'part_code' => $product[0]->value,
                'part_name' => $product[0]->name,
                'part_desc' => $product[0]->description,
                'model' => $product[0]->classification,
                'identitas_lot' => $faker->randomLetter(5),
                'quantity' => $faker->randomNumber(5),
                // 'status'=>,
                'inputed_by' => 1,
                'inputed_name' => $faker->name(),
                'updated_by' => 1,
                'updated_name' => $faker->name(),
            ];
        } //./end loop stolines

        $stoHeader->stoLines()->createMany($data);
    }
}
