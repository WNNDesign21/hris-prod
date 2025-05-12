<?php

namespace App\Services;

use App\Repositories\CutiRepository;
use Illuminate\Database\Eloquent\Collection;

class CutiService
{
    private $cutiRepository;

    public function __construct(CutiRepository $cutiRepository)
    {
        $this->cutiRepository = $cutiRepository;
    }

    public function getMustApprovedDatatable(array $dataFilter, array $settings)
    {
        return $this->cutiRepository->getMustApproved($dataFilter, $settings);
    }

    public function countMustApprovedDatatable(array $dataFilter)
    {
        return $this->cutiRepository->countMustApproved($dataFilter);
    }

    public function getAllDataDatatable(array $dataFilter, array $settings)
    {
        return $this->cutiRepository->getAllData($dataFilter, $settings);
    }

    public function countAllDataDatatable(array $dataFilter)
    {
        return $this->cutiRepository->countAllData($dataFilter);
    }

     public function getPengajuanDatatable(array $dataFilter, array $settings)
    {
        return $this->cutiRepository->getPengajuan($dataFilter, $settings);
    }

    public function countPengajuanDatatable(array $dataFilter)
    {
        return $this->cutiRepository->countPengajuan($dataFilter);
    }

    public function getById(int $id, array $fields = ['*'])
    {
        return $this->cutiRepository->getById($id, $fields);
    }

    public function countApprovalCuti()
    {
        return $this->cutiRepository->countApprovalCuti();
    }

    public function rejectCuti(int $id, array $data)
    {
        return $this->cutiRepository->rejectCuti($id, $data);
    }

    public function deleteCuti(int $id)
    {
        return $this->cutiRepository->deleteCuti($id);
    }

    public function cancelCuti(int $id, array $data)
    {
        return $this->cutiRepository->cancelCuti($id, $data);
    }

    public function createCuti(array $data)
    {
        return $this->cutiRepository->createCuti($data);
    }

    public function updateCuti(int $id, array $data)
    {
        return $this->cutiRepository->updateCuti($id, $data);
    }

    public function updateApprovalCuti(int $id, array $data)
    {
        return $this->cutiRepository->updateApprovalCuti($id, $data);
    }

    public function createApprovalCuti(array $data)
    {
        return $this->cutiRepository->createApprovalCuti($data);
    }

    public function getStructureApprovalCuti(Collection $posisi)
    {
        return $this->cutiRepository->getStructureApprovalCuti($posisi);
    }

    public function getKaryawanPengganti(string $id_karyawan)
    {
        return $this->cutiRepository->getKaryawanPengganti($id_karyawan);
    }
}
