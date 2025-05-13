<?php

namespace App\Services;

use App\Repositories\KaryawanRepository;

class KaryawanService
{
    private $karyawanRepository;

    public function __construct(KaryawanRepository $karyawanRepository)
    {
        $this->karyawanRepository = $karyawanRepository;
    }

    public function updateKaryawan(string $id, array $data)
    {
        return $this->karyawanRepository->updateKaryawan($id, $data);
    }
}
