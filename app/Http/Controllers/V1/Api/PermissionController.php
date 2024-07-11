<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\AppRole;

class PermissionController extends SuperController
{
    public array $withAll = [
        'permissions'
    ];

    public function __construct()
    {
        parent::__construct(
            Permission::class,
            PermissionResource::class,
            PermissionRequest::class,
            PermissionRequest::class,
        );
    }
}
