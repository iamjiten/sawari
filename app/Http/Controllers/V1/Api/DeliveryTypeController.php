<?php

namespace App\Http\Controllers\V1\Api;


use App\Http\Controllers\V1\SuperController;
use App\Models\DeliveryType;
use App\Http\Requests\DeliveryTypeRequest;
// use App\Http\Resources\UserListResource;
use App\Http\Resources\DeliveryTypeResource;


class DeliveryTypeController extends SuperController
{
    public array $scopes = [
        'user'
    ];
    public function __construct()
    {
        parent::__construct(
            DeliveryType::class,
            DeliveryTypeResource::class,
            DeliveryTypeRequest::class,
            DeliveryTypeRequest::class
        );
    }
}
