<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\AppRole;

class RoleController extends SuperController
{
    public array $withAll = [
        'permissions'
    ];

    public function __construct()
    {
        parent::__construct(
            AppRole::class,
            RoleResource::class,
            RoleRequest::class,
            RoleRequest::class
        );
    }
}
