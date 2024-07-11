<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\TypeSettingRequest;
use App\Http\Resources\TypeSettingResource;
use App\Models\TypeSetting;

class TypeSettingController extends SuperController
{
    public array $scopes = [
        'user'
    ];

    public function __construct()
    {
        parent::__construct(
            TypeSetting::class,
            TypeSettingResource::class,
            TypeSettingRequest::class,
            TypeSettingRequest::class
        );
    }
}
