<?php

namespace App\Repositories;

use App\Models\Karyawan;

class KaryawanRepository
{
    public function updateKaryawan(string $id, array $data)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->update($data);
        return $karyawan;
    }
}
