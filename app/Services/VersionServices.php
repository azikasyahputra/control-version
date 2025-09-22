<?php

namespace App\Services;

use App\Data\Version\GetVersionData;
use App\Data\Version\StoreVersionData;
use App\Helper\UnixTimestampFormatter;
use App\Models\Version;
use App\Interfaces\VersionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class VersionServices
{
    private VersionRepositoryInterface $versionRepository;

    public function __construct(VersionRepositoryInterface $versionRepository)
    {
        $this->versionRepository = $versionRepository;
    }

    public function all(): Collection
    {
        return $this->versionRepository->getAll() ?: collect();
    }
    
    public function store(StoreVersionData $data): array
    {
        $storeData = $this->versionRepository->create($data);
        $timestampFormatter = new UnixTimestampFormatter($storeData->created_at);
        $timestamp = [
            'Time' => $timestampFormatter->convert($storeData->created_at)
        ];
        return $timestamp;
    }

    public function find(GetVersionData $data): ?Version
    {
        $object =  $this->versionRepository->findByIdWithQuery($data);
        return $object;
    }
}