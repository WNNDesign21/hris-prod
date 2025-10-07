<?php

namespace App\Observers;

use App\Models\Karyawan;

class KaryawanObserver
{
    /**
     * Handle the Karyawan "saving" event.
     */
    public function saving(Karyawan $karyawan): void
    {
        // Ensure relationships are loaded if not already
        $karyawan->loadMissing('posisi.departemen.divisi');

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

        if ($part1 && $part2 && $part3) {
            $karyawan->sinas = "$part1 - $part2 - $part3";
        } else {
            $karyawan->sinas = null;
        }
    }

    /**
     * Handle the Karyawan "created" event.
     */
    public function created(Karyawan $karyawan): void
    {
        //
    }

    /**
     * Handle the Karyawan "updated" event.
     */
    public function updated(Karyawan $karyawan): void
    {
        //
    }

    /**
     * Handle the Karyawan "deleted" event.
     */
    public function deleted(Karyawan $karyawan): void
    {
        //
    }

    /**
     * Handle the Karyawan "restored" event.
     */
    public function restored(Karyawan $karyawan): void
    {
        //
    }

    /**
     * Handle the Karyawan "force deleted" event.
     */
    public function forceDeleted(Karyawan $karyawan): void
    {
        //
    }
}
