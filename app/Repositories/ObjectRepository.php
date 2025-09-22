<?php

namespace App\Repositories;

use App\Data\Object\GetObjectData;
use App\Data\Object\StoreObjectData;
use App\Interfaces\ObjectRepositoryInterface;
use App\Models\Objects;
use Illuminate\Database\Eloquent\Collection;

class ObjectRepository implements ObjectRepositoryInterface
{
    public function getAll(): Collection
    {
        return Objects::all();
    }

    public function create(StoreObjectData $data): Objects
    {
        $dataForDatabase = $data->toArray();
        return Objects::create($dataForDatabase);
    }

    public function findByIdWithQuery(GetObjectData $data): ?Objects
    {
        $query = Objects::query()->where('key', $data->key);

        if ($data->timestamp !== null) {
            $query->where('created_at', '=', $data->timestamp);
        }

        $query->orderBy('id','DESC');
        return $query->first();
    }
}
