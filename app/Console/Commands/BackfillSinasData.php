<?php

namespace App\Console\Commands;

use App\Models\Karyawan;
use Illuminate\Console\Command;

class BackfillSinasData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-sinas-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill the sinas data for all existing employees.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to backfill Sinas data for all employees...');

        $karyawans = Karyawan::with(['posisi.departemen.divisi'])->get();

        $progressBar = $this->output->createProgressBar($karyawans->count());
        $progressBar->start();

        foreach ($karyawans as $karyawan) {
            $posisi = $karyawan->posisi->first();
            $divisiName = $posisi && $posisi->departemen && $posisi->departemen->divisi ? $posisi->departemen->divisi->nama : null;

            if ($karyawan->direct == 1) {
                $part1 = 'PRODUKSI';
            } elseif ($karyawan->indirect == 1 && strtoupper($divisiName) === 'MANUFACTURE') {
                $part1 = 'PRODUKSI';
            } else {
                $part1 = 'LAINNYA';
            }

            $part2 = match (strtoupper($karyawan->jenis_kontrak)) {
                'PKWTT' => 'TETAP',
                'PKWT', 'PENGKARYAAN' => 'KONTRAK',
                default => ''
            };

            $part3 = match ($karyawan->jenis_kelamin) {
                'L' => 'LAKI2',
                'P' => 'PEREMPUAN',
                default => ''
            };

            $sinas = '';
            if ($part1 && $part2 && $part3) {
                $sinas = "$part1 - $part2 - $part3";
            }

            $karyawan->sinas = $sinas;
            $karyawan->save();

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info('\nSinas data backfill completed successfully!');

        return 0;
    }
}
