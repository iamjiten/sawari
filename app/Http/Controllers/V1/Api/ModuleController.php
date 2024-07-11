<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\ModuleRequest;
use App\Http\Resources\MoudleResource;
use App\Models\Module;

class ModuleController extends SuperController
{

    public array $scopes = [
        'user'
    ];
    public function __construct()
    {
        parent::__construct(
            Module::class,
            MoudleResource::class,
            ModuleRequest::class,
            ModuleRequest::class
        );
    }
}
