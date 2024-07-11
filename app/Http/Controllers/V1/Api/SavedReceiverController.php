<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\SavedReceiverRequest;
use App\Http\Resources\SavedReceiverResource;
use App\Models\SavedReceiver;

class SavedReceiverController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            SavedReceiver::class,
            SavedReceiverResource::class,
            SavedReceiverRequest::class,
            SavedReceiverRequest::class
        );
    }

    public function self()
    {
        $model = $this->model::initializer()
            ->self()
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        return $this->resource::collection($model->paginates());
    }
}
