<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan setting baru dengan nilai default 0
        $new_settings = [
            ['setting_name' => 'insentif_department_head_3', 'value' => '0', 'organisasi_id' => 1],
            ['setting_name' => 'insentif_section_head_gt_7', 'value' => '0', 'organisasi_id' => 1],
            ['setting_name' => 'insentif_department_head_gt_7', 'value' => '0', 'organisasi_id' => 1],
        ];

        DB::table('setting_lemburs')->insert($new_settings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $setting_names = ['insentif_department_head_3', 'insentif_section_head_gt_7', 'insentif_department_head_gt_7'];
        DB::table('setting_lemburs')->whereIn('setting_name', $setting_names)->delete();
    }
};