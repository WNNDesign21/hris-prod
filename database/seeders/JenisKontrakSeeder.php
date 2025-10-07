<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\JenisKontrak;

class JenisKontrakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisKontraks = DB::table('kontraks')->distinct()->pluck('jenis');

        foreach ($jenisKontraks as $jenis) {
            if ($jenis) {
                JenisKontrak::firstOrCreate(['nama' => $jenis]);
            }
        }

        // Also add the other values that might not be in the table yet
        $additionalJenis = ['PENGKARYAAN', 'PK'];
        foreach ($additionalJenis as $jenis) {
            JenisKontrak::firstOrCreate(['nama' => $jenis]);
        }
    }
}