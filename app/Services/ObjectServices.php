<?php

namespace App\Services;

use App\Data\Object\GetObjectData;
use App\Data\Object\StoreObjectData;
use App\Helper\UnixTimestampFormatter;
use App\Models\Objects;
use App\Interfaces\ObjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ObjectServices
{
    private ObjectRepositoryInterface $objectRepository;

    public function __construct(ObjectRepositoryInterface $objectRepository)
    {
        $this->objectRepository = $objectRepository;
    }

    public function all(): Collection
    {
        return $this->objectRepository->getAll() ?? collect();
    }
    
    public function store(StoreObjectData $data): array
    {
        $storeData = $this->objectRepository->create($data);
        $timestampFormatter = new UnixTimestampFormatter($storeData->created_at);
        $timestamp = [
            'Time' => $timestampFormatter->convert($storeData->created_at)
        ];
        return $timestamp;
    }

    public function find(GetObjectData $data): ?Objects
    {
        $object =  $this->objectRepository->findByIdWithQuery($data);
        return $object;
    }
}