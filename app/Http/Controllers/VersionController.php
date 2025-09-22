<?php

namespace App\Http\Controllers;

use App\Data\Version\GetVersionData;
use App\Data\Version\StoreVersionData;
use App\Http\Requests\DynamicKeyStoreRequest;
use App\Services\VersionServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class VersionController extends Controller
{    
    use ResponseTrait;

    protected $versionServices;

    public function __construct(VersionServices $versionServices)
    {
        $this->versionServices = $versionServices;
    }

    public function index() : JsonResponse
    {
        $versionData = $this->versionServices->all();
        
        if ($versionData->isEmpty()) {
            return $this->error(['message' => 'Data Not Found'], 404);
        }

        return response()->json($versionData,200);
    }

    public function store(DynamicKeyStoreRequest $request) : JsonResponse
    {
        $dynamicData = $request->getDynamicKeyAndValue();
        $storeVersionDto = StoreVersionData::fromArray($dynamicData);
        $storeVersion = $this->versionServices->store($storeVersionDto);
        return $this->success($storeVersion,201);
    }

    public function show(string $id, Request $request): JsonResponse
    {
        $getVersionDto = GetVersionData::fromRequest($id,$request);
        $versionData = $this->versionServices->find($getVersionDto);

        if ($versionData === null) {
            return $this->error(['message' => 'Data Not Found'], 404);
        }
        return $this->success($versionData);
    }
}
