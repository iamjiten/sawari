<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\AcceptRejectEnum;
use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\VehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Notifications\KycVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class VehicleController extends SuperController
{
    public array $withAll = [
        'vehicleType',
        'user',
        'brand',
        'model',
        'color'
    ];

    public function __construct()
    {
        parent::__construct(
            Vehicle::class,
            VehicleResource::class,
            VehicleRequest::class,
            VehicleRequest::class
        );
    }

    public function index(): JsonResource
    {
        $model = $this->model::initializer()
            ->whereNull('is_available')
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        return $this->resource::collection($model->paginates());
    }


    public function selfVehicle()
    {
        $vehicle = Vehicle::where('user_id', auth()->id())->first();
        if ($vehicle) {
            return VehicleResource::make($vehicle);
        }
        return $this->error('Vehicle not found', 500);
    }

    public function changeActionStatus($id, $status)
    {
        if (!in_array($status, [1, 2, 3, 4])) {
            return $this->error('Status not found', 500);
        }
        try {
            DB::beginTransaction();
            $vehicle = $this->model::findOrFail($id);
            if ($status == 3) {
                $data = [
                    'status' => $status,
                    'remarks' => \request()->remark
                ];
            } else {
                $data = [
                    'status' => $status
                ];
                $vehicle->user->notify(new KycVerificationNotification($vehicle, false));
            }
            $vehicle->update($data);
            DB::commit();

            return $this->success(['status' => true], 'Status change successfully');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->somethingWentWrong($e, 'Failed to change status');
        }
    }
}
