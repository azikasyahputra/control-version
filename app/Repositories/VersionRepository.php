<?php

namespace App\Repositories;

use App\Data\Version\GetVersionData;
use App\Data\Version\StoreVersionData;
use App\Models\Version;
use Illuminate\Database\Eloquent\Collection;

class VersionRepository
{
    public function getAll(): Collection
    {
        return Version::all();
    }

    public function create(StoreVersionData $data): Version
    {
        $dataForDatabase = $data->toArray();
        return Version::create($dataForDatabase);
    }

    public function find(GetVersionData $data): ?Version
    {
        $query = Version::query()->where('key', $data->key);

        if ($data->timestamp !== null) {
            $query->where('created_at', '<=', $data->timestamp);
        }

        return $query->first();
    }
}
