<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\PackageRequest;
use App\Http\Resources\PackageResource;
use App\Models\Package;

class PackageController extends SuperController
{
    public array $withAll = [
        'sender',
        'receiverAsUser',
        'receiver',
        'category',
        'sensible',
        'size',
    ];

    public array $scopes = [
        'user'
    ];
    public function __construct()
    {
        parent::__construct(
            Package::class,
            PackageResource::class,
            PackageRequest::class,
            PackageRequest::class
        );
    }

    public function selfSender()
    {
        $model = $this->model::initializer()
            ->selfSender()
            ->pending()
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        return $this->resource::collection($model->paginates());
    }

    public function selfReceiver()
    {
        $model = $this->model::initializer()
            ->selfReceiver()
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        return $this->resource::collection($model->paginates());
    }
}
