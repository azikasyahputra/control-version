<?php

namespace App\Http\Controllers;

use App\Data\Version\GetVersionData;
use App\Data\Version\StoreVersionData;
use App\Http\Requests\DynamicKeyStoreRequest;
use App\Services\VersionServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VersionController extends Controller
{    
    protected $versionServices;

    public function __construct(VersionServices $versionServices)
    {
        $this->versionServices = $versionServices;
    }

    public function index() : JsonResponse
    {
        $versionData = $this->versionServices->all();
        return response()->json($versionData,200);
    }

    public function store(DynamicKeyStoreRequest $request) : JsonResponse
    {
        $dynamicData = $request->getDynamicKeyAndValue();
        $storeVersionDto = StoreVersionData::fromArray($dynamicData);
        $storeVersion = $this->versionServices->store($storeVersionDto);
        return response()->json($storeVersion,201);
    }

    public function show(string $id, Request $request): JsonResponse
    {
        $getVersionDto = GetVersionData::fromRequest($id,$request);
        $versionData = $this->versionServices->find($getVersionDto);
        return response()->json($versionData['data'],$versionData['code']);
    }
}
