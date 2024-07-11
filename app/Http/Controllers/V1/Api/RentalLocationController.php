<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\RentalLocationRequest;
use App\Http\Resources\RentalLocationResource;
use App\Models\RentalLocation;

class RentalLocationController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            RentalLocation::class,
            RentalLocationResource::class,
            RentalLocationRequest::class,
            RentalLocationRequest::class
        );
    }
}
