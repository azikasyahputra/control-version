<?php

namespace App\Services;

use App\Data\Version\GetVersionData;
use App\Data\Version\StoreVersionData;
use App\Helper\ConvertUnixToTime;
use App\Repositories\VersionRepository;
use Illuminate\Database\Eloquent\Collection;

class VersionServices
{
    protected $versionRepository;

    public function __construct(VersionRepository $versionRepository)
    {
        $this->versionRepository = $versionRepository;
    }

    public function all(): Collection
    {
        return $this->versionRepository->getAll();
    }
    
    public function store(StoreVersionData $data): array
    {
        $storeData = $this->versionRepository->create($data);
        $timestamp = [
            'Time' => (new ConvertUnixToTime($storeData->created_at))->convert()
        ];
        return $timestamp;
    }

    public function find(GetVersionData $data): array
    {
        $code = 200;
        $version = null;

        $dataVersion =  $this->versionRepository->find($data);
        if(empty($dataVersion)){
            $code = 404;
        }else{
            $version = [
                'value' => $dataVersion->value
            ];
        }

        $data = [
            'data' => $version,
            'code'=>$code
        ];
        
        return $data;
    }
}