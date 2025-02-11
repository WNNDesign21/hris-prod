<?php

namespace App\Jobs;

use App\Models\Grup;
use App\Models\Karyawan;
use App\Models\GrupPattern;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RollingShiftGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            activity('process_rolling_shift_grup_karyawan')->log('Start rolling shift group karyawan - ' . now());
            $karyawans = Karyawan::aktif()->get();
            foreach ($karyawans as $karyawan) {
                activity('rolling_shift_grup_karyawan')->log('Start rolling shift group karyawan - ' . $karyawan->nama);
                $currentGrup = $karyawan->grup_id;
                $pattern = GrupPattern::find($karyawan->grup_pattern_id);
                if ($pattern) {
                    $order = json_decode($pattern->urutan);
                    $currentPosition = array_search($currentGrup, $order);
                    if ($currentPosition !== false) {
                        $nextPosition = ($currentPosition + 1) % count($order);
                        $nextGrupId = $order[$nextPosition];
                        $karyawan->grup_id = $nextGrupId;
                        $karyawan->save();
                        
                        $grup = Grup::find($nextGrupId);
                        $karyawan->karyawanGrup()->create([
                            'grup_id' => $nextGrupId,
                            'pin' => $karyawan->pin,
                            'active_date' => now(),
                            'organisasi_id' => $karyawan->organisasi_id,
                            'toleransi_waktu' => $grup->toleransi_waktu,
                            'jam_masuk' => $grup->jam_masuk,
                            'jam_keluar' => $grup->jam_keluar,
                        ]);
                    }
                } else {
                    activity('error_rolling_shift_grup_karyawan')->log('Grup pattern not found - ' . $karyawan->nama);
                    continue;
                }
            }
            activity('success_rolling_shift_grup_karyawan')->log('Success rolling shift group karyawan - ' . now());
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            activity('error_rolling_shift_grup_karyawan')->log($e->getMessage());
        }
    }
}
