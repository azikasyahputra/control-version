<?php

namespace App\Http\Controllers;

use App\Data\Object\GetObjectData;
use App\Data\Object\StoreObjectData;
use App\Http\Requests\DynamicKeyStoreRequest;
use App\Services\ObjectServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;

class ObjectController extends Controller
{    
    use ResponseTrait;

    protected $objectServices;

    public function __construct(ObjectServices $objectServices)
    {
        $this->objectServices = $objectServices;
    }

    public function index() : JsonResponse
    {
        $objectData = $this->objectServices->all();
        
        if ($objectData->isEmpty()) {
            return $this->error(['message' => 'Data Not Found'], 404);
        }

        return response()->json($objectData,200);
    }

    public function store(DynamicKeyStoreRequest $request) : JsonResponse
    {
        $dynamicData = $request->getDynamicKeyAndValue();
        $storeObjectDto = StoreObjectData::fromArray($dynamicData);
        $storeObject = $this->objectServices->store($storeObjectDto);
        return $this->success($storeObject,201);
    }

    public function show(string $id, Request $request): JsonResponse
    {
        $getObjectDto = GetObjectData::fromRequest($id,$request);
        $objectData = $this->objectServices->find($getObjectDto);

        if ($objectData === null) {
            return $this->error(['message' => 'Data Not Found'], 404);
        }
        return $this->success($objectData);
    }
}
