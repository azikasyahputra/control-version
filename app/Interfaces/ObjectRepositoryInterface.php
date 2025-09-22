<?php

namespace App\Interfaces;

use App\Data\Object\GetObjectData;
use App\Data\Object\StoreObjectData;
use App\Models\Objects;
use Illuminate\Database\Eloquent\Collection;

interface ObjectRepositoryInterface
{
    public function getAll(): Collection;
    public function create(StoreObjectData $data): Object;
    public function findByIdWithQuery(GetObjectData $data): ?Objects;
}