<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\SettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;

class SettingController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            Setting::class,
            SettingResource::class,
            SettingRequest::class,
            SettingRequest::class
        );
    }

    public function typeAll($type)
    {
        $model = $this->model::initializer()
            ->where('key', $type)
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        $resource = $this->resource;
        if (property_exists($this, 'listResource')) {
            $resource = $this->listResource;
        }
        return $resource::collection($model->get());
    }
}
