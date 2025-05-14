<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository
{
    public function getById(int $id, array $fields = ['*'])
    {
        return Event::select($fields)->findOrFail($id);
    }

    public function getWithFilters(array $dataFilter, array $fields = ['*'])
    {
        $query = Event::select($fields);

        if (isset($dataFilter['organisasi_id'])) {
            $query->where('organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['jenis_event'])) {
            $query->whereIn('jenis_event', $dataFilter['jenis_event']);
        }

        // Without Get Method
        return $query;
    }

    private function _queryEvent(array $dataFilter)
    {
        $data = Event::select('*');

        if (isset($dataFilter['organisasi_id'])) {
            $data->where('organisasi_id', $dataFilter['organisasi_id']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('jenis_event', 'ILIKE', "%{$search}%")
                    ->orWhere('keterangan', 'ILIKE', "%{$search}%")
                    ->orWhere('tanggal_mulai', 'ILIKE', "%{$search}%")
                    ->orWhere('tanggal_selesai', 'ILIKE', "%{$search}%")
                    ->orWhere('durasi', 'ILIKE', "%{$search}%");
            });
        }

        $result = $data;
        return $result;
    }

    public function getEvent(array $dataFilter, array $settings)
    {
        return $this->_queryEvent($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public function countEvent(array $dataFilter)
    {
        return $this->_queryEvent($dataFilter)->count();
    }

    public function create(array $data)
    {
        return Event::create($data);
    }

    public function update(int $id, array $data)
    {
        $event = Event::findOrFail($id);
        $event->update($data);
        return $event;
    }

    public function delete(int $id)
    {
        return Event::findOrFail($id)->delete();
    }
}
