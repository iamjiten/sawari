<?php

namespace App\Http\Controllers\V1\Api;


use App\Http\Controllers\V1\SuperController;
use App\Models\VehicleType;
use App\Http\Requests\VehicleTypeRequest;

// use App\Http\Resources\UserListResource;
use App\Http\Resources\VehicleTypeResource;


class VehicleTypeController extends SuperController
{

    public array $scopes = [
        'user'
    ];

    public function __construct()
    {
        parent::__construct(
            VehicleType::class,
            VehicleTypeResource::class,
            VehicleTypeRequest::class,
            VehicleTypeRequest::class
        );
    }
}
