<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\RentalAreaRequest;
use App\Http\Resources\RentalAreaResource;
use App\Models\RentalArea;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalAreaController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            RentalArea::class,
            RentalAreaResource::class,
            RentalAreaRequest::class,
            RentalAreaRequest::class,
        );
    }

    public function index(): JsonResource
    {
        $areas = RentalArea::query()
            ->select('id', 'area')
            ->get();
        return RentalAreaResource::make($areas);
    }
}
