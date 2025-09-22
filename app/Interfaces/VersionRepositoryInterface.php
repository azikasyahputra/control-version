<?php

namespace App\Interfaces;

use App\Data\Version\GetVersionData;
use App\Data\Version\StoreVersionData;
use App\Models\Version;
use Illuminate\Database\Eloquent\Collection;

interface VersionRepositoryInterface
{
    public function getAll(): Collection;
    public function create(StoreVersionData $data): Version;
    public function findByIdWithQuery(GetVersionData $data): ?Version;
}