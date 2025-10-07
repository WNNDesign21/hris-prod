<?php

namespace App\Repositories;

use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

class KaryawanRepository
{
    public function getAll(array $fields = ['*'])
    {
        return Karyawan::select($fields)->get();
    }

    public function getById(string $id, array $fields = ['*'])
    {
        return Karyawan::select($fields)->findOrFail($id);
    }

    public function getActiveKaryawan(array $fields = ['*'])
    {
        return Karyawan::select($fields)->aktif()->get();
    }

    public function getKaryawanWithDistinctPosisi()
    {
        $subQuery = DB::table('karyawan_posisi')
            ->select('karyawan_id', 'posisi_id')
            ->whereNull('deleted_at')
            ->distinct('karyawan_id');

        $data = Karyawan::select(
            'karyawans.id_karyawan',
            'karyawans.nama',
            'posisis.nama as posisi',
            'posisis.id_posisi'
        )
        ->leftJoinSub($subQuery, 'distinct_karyawan_posisi', function ($join) {
            $join->on('karyawans.id_karyawan', 'distinct_karyawan_posisi.karyawan_id');
        })
        ->aktif()
        ->orderBy('karyawans.nama', 'ASC')
        ->groupBy('karyawans.id_karyawan','karyawans.nama', 'posisis.nama', 'posisis.id_posisi')
        ->get();

        return $data;
    }

    public function updateKaryawan(string $id, array $data)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->update($data);
        return $karyawan;
    }
}
