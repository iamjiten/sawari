<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\PackageSizeRequest;
use App\Http\Resources\PackageSizeResource;
use App\Models\PackageSize;

class PackageSizeController extends SuperController
{

    public array $scopes = [
        'user',
        'orderByDescSize'
    ];
    public function __construct()
    {
        parent::__construct(
            PackageSize::class,
            PackageSizeResource::class,
            PackageSizeRequest::class,
            PackageSizeRequest::class
        );
    }
}
