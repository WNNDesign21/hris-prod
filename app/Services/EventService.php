<?php

namespace App\Services;

use App\Repositories\EventRepository;
use Illuminate\Database\Eloquent\Collection;

class EventService
{
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getById(int $id, array $fields = ['*'])
    {
        return $this->eventRepository->getById($id, $fields);
    }

    public function getWithFilters(array $dataFilter, array $fields = ['*'])
    {
        return $this->eventRepository->getWithFilters($dataFilter, $fields);
    }

    public function getEventDatatable(array $dataFilter, array $settings)
    {
        return $this->eventRepository->getEvent($dataFilter, $settings);
    }

    public function countEventDatatable(array $dataFilter)
    {
        return $this->eventRepository->countEvent($dataFilter);
    }

    public function create(array $data)
    {
        return $this->eventRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->eventRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->eventRepository->delete($id);
    }
}
