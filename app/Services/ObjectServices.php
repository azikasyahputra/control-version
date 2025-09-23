<?php

namespace App\Services;

use App\Data\Object\GetObjectData;
use App\Data\Object\StoreObjectData;
use App\Helper\CheckConvertStringJson;
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
    
    public function store(StoreObjectData $data): string
    {
        $storeData = $this->objectRepository->create($data);
        $timestampFormatter = new UnixTimestampFormatter($storeData->created_at);
        $timestamp = 'Time: '.$timestampFormatter->convert($storeData->created_at);
        return $timestamp;
    }

    public function find(GetObjectData $data): string|object|array|null
    {
        $value = null;
        $object =  $this->objectRepository->findByIdWithQuery($data);
        if(!empty($object)){
            $value = (new CheckConvertStringJson($object->value))->reConvert();
        }
        return $value;
    }
}