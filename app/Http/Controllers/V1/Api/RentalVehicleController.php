<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\ModuleEnum;
use App\Exceptions\ApiResponder;
use App\Http\Controllers\Controller;
use App\Http\Requests\RentalVehicleRequest;
use App\Http\Requests\RentalVehicleSearchRequest;
use App\Http\Requests\RentalVehicleUpdateRequest;
use App\Http\Resources\RentalFeatureResource;
use App\Http\Resources\RentalVehicleResource;
use App\Http\Resources\RentalVehicleResourceCollection;
use App\Http\Resources\RentalVehicleSimpleResource;
use App\Http\Resources\VehicleResource;
use App\Models\Module;
use App\Models\RentalFeature;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Plank\Mediable\Facades\MediaUploader;

class RentalVehicleController extends Controller
{
    use ApiResponder;

    public function search(RentalVehicleSearchRequest $request)
    {
        $filter = request()->input('filters');
        if ($filter) {
            $filterValue = json_decode($filter, true);

            if (array_key_exists('price', $filterValue)) {
                $priceRange = $filterValue['price'];
                unset($filterValue['price']);
            }
        }

        $from = Carbon::parse($request->pickup_date)->format('Y-m-d H:i:s');
        $to = Carbon::parse($request->return_date)->format('Y-m-d H:i:s');

//        $rentalModule = Module::where('name', ModuleEnum::Rental->value)->first();
        $model = Vehicle::query()
            ->where('is_available', 1)
            ->whereHas('vehicleInformation')
            ->whereHas('basicInfos')
            ->whereHas('services')
            ->select('id', 'image', 'brand_id', 'model_id', 'color_id', 'vehicle_type_id')
            ->withCount(['booked' => function ($q) use ($from, $to) {
                $q->where(function ($q) use ($from, $to) {
                    $q->where(function ($q) use ($from, $to) {
                        $q->where('from', '>=', $from)
                            ->where('from', '<=', $to);
                    })->orWhere(function ($r) use ($from, $to) {
                        $r->where('to', '>=', $from)
                            ->where('to', '<=', $to);
                    });
                });
            }])
//            ->isBooked(false)
            ->with(['brand:id,value', 'model:id,value', 'color:id,value', 'vehicleType:id,name', 'vehicleInformation'])
            ->where('vehicle_type_id', $request->vehicle_type_id)
            ->isDriverOnlineIn($request->with_driver)
//            ->filterLocations($request->pickup_location_id, $request->return_location_id)
            ->whereHas('vehicleInformation', fn($query) => $query
                ->when(isset($priceRange), fn($q) => $q->whereBetween('per_day_fare', $priceRange))
            )
            ->when(isset($filterValue), fn($q) => $q->applyFilter($filterValue))
            ->paginate();

        $res = RentalVehicleSimpleResource::collection($model);
        $maxPerDayFare = null;
        $minPerDayFare = null;

        foreach ($model as $vehicle) {
            $perDayFare = $vehicle->vehicleInformation?->per_day_fare ?? 0;

            if ($maxPerDayFare === null || $perDayFare > $maxPerDayFare) {
                $maxPerDayFare = $perDayFare;
            }

            if ($minPerDayFare === null || $perDayFare < $minPerDayFare) {
                $minPerDayFare = $perDayFare;
            }
        }

        return response()->json([
            'min_range' => count($model) <= 1 ? 0 : $minPerDayFare,
            'max_range' => $maxPerDayFare,
            'data' => $res,
        ]);
    }

    public function index()
    {
        $model = Vehicle::initializer()
            ->whereHas('vehicleInformation')
            ->whereHas('basicInfos')
            ->whereHas('services')
            ->with('vehicleInformation', 'basicInfos', 'services')
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        if (auth()->user()->merchant_id) {
            $model = $model->where('merchant_id', auth()->user()->merchant_id);
        }
        return RentalVehicleResource::collection($model->paginates());
    }

    public function store(RentalVehicleRequest $request)
    {
        DB::beginTransaction();
        try {
            $rentalVehicle = Vehicle::create([
                'vehicle_type_id' => $request->vehicle_type_id,
                'brand_id' => $request->brand_id,
                'merchant_id' => $request->merchant_id ?? auth()->user()->merchant_id,
                'model_id' => $request->model_id,
                'color_id' => $request->color_id,
                'production_year' => $request->production_year,
                'is_available' => 1,
                'user_id' => auth()->id(),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);
            if (is_array(request()->input('image'))) $rentalVehicle->syncMedia(request()->input('image'), 'image');
            $rentalVehicle->vehicleInformation()->create([
                'detail_info' => $request->detail_info,
                'per_day_fare' => $request->per_day_fare,
                'per_day_driver_fare' => $request?->per_day_driver_fare ?? 0,
                'withDriver' => $request?->withDriver,
            ]);
            $tem_array_1 = [];
            $tem_array_2 = [];
            foreach ($request->basic_infos as $basic) {
                $basic_info_create = RentalFeature::create($basic);
                array_push($tem_array_1, $basic_info_create->id);
            }
            $rentalVehicle->basicInfos()->sync($tem_array_1);
            foreach ($request->services as $service) {
                $service_create = RentalFeature::create($service);
                array_push($tem_array_2, $service_create->id);
            }
            $rentalVehicle->services()->sync($tem_array_2);
            DB::commit();
            return $this->success($rentalVehicle, "Created successfully");
        } catch (\Exception $e) {
            dd($e);
            return $this->somethingWentWrong($e);
        }
    }

    public function update(RentalVehicleRequest $request, $id): Jsonresponse
    {
        DB::beginTransaction();
        try {
            $rentalVehicle = Vehicle::find($id);
            $rentalVehicle->update([
                'vehicle_type_id' => $request->vehicle_type_id,
                'brand_id' => $request->brand_id,
                'model_id' => $request->model_id,
                'merchant_id' => $request->merchant_id ?? auth()->user()->merchant_id,
                'color_id' => $request->color_id,
                'production_year' => $request->production_year,
                'updated_by' => auth()->id()
            ]);
            if (is_array(request()->input('image'))) $rentalVehicle->syncMedia(request()->input('image'), 'image');
            $rentalVehicle->vehicleInformation->update([
                'detail_info' => $request->detail_info,
                'per_day_fare' => $request->per_day_fare,
                'per_day_driver_fare' => $request?->per_day_driver_fare ?? 0,
                'withDriver' => $request?->withDriver,
            ]);
            foreach ($rentalVehicle->basicInfos as $deleteBasic) {
                DB::table('vehicle_basic_info_feature')->where('basic_info_id', $deleteBasic->id)?->delete();
                RentalFeature::where('id', $deleteBasic->id)->delete();
            }
            $tem_array_1 = [];
            $tem_array_2 = [];
            foreach ($request->basic_infos as $basic) {
                $basic_info_create = RentalFeature::create($basic);
                array_push($tem_array_1, $basic_info_create->id);
            }
            $rentalVehicle->basicInfos()->sync($tem_array_1);
            foreach ($rentalVehicle->services as $deleteService) {
                DB::table('vehicle_service_feature')->where('service_id', $deleteService->id)?->delete();
                RentalFeature::where('id', $deleteService->id)->delete();
            }
            foreach ($request->services as $service) {
                $service_create = RentalFeature::create($service);
                array_push($tem_array_2, $service_create->id);
            }
            $rentalVehicle->services()->sync($tem_array_2);
            DB::commit();
            return $this->success($rentalVehicle, "Updated successfully");
        } catch (\Error $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        $this->validate($request, [
            'delete_rows' => ['required', 'array'],
            'delete_rows.*' => ['required', 'exists:' . (new  Vehicle())->getTable() . ',id'],
        ]);

        try {
            DB::beginTransaction();
            foreach ((array)$request->input('delete_rows') as $item) {
                $model = Vehicle::findOrFail($item);
                if ($model) {
                    $model->delete();
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->somethingWentWrong($e);
        }

        return $this->success(null, 'Data deleted successfully');
    }

    public function show($id)
    {
        $vehicle = Vehicle::find($id);
        if ($vehicle) {
            return VehicleResource::make($vehicle);
        } else {
            return $this->error('vehicle does not exist', 500);
        }
    }

    public function simpleShow($id)
    {
        $filter = request()->input('filters');
        if ($filter) {
            $filterValue = json_decode($filter, true);
        }
        $day = 2;
        $withDriver = 0;

        $models = Vehicle::query()
            ->select('id', 'image')
            ->withWhereHas('vehicleInformation', fn($q) => $q->select('vehicle_id', 'service', 'basic_info', 'per_day_fare', 'per_day_driver_fare')
                ->selectRaw("(per_day_fare * $day) + (per_day_driver_fare * $day * $withDriver) as total")
                ->when(isset($filterValue), fn($q) => $q->applyFilter($filterValue))
            )->paginate();

        return RentalVehicleResource::collection($models);

    }

    public function changeDriverStatus($id)
    {
        $vehicle = Vehicle::find($id);
        // check if vehicle belongs to the user
        if ($vehicle) {
            $vehicleInfo = $vehicle->vehicleInformation;
            if ($vehicleInfo) {
                $vehicleInfo->update(['withDriver' => $vehicleInfo->withDriver == 1 ? 0 : 1]);
                return $this->success(null, 'status changed successfully');
            } else {
                return $this->error('do not have rental vehicle', 500);
            }
        } else {
            return $this->error('vehicle not found', 500);
        }
    }

    public function getFilters()
    {
        $features = RentalFeature::query()
            ->select('id', 'category', 'key', 'value')
            ->active()
            ->get()
            ->groupBy('category');

        return $this->success($features, 'features');

    }

    public function changeActionAvailable($id, Request $request)
    {
        $vehicle = Vehicle::find($id);
        $vehicle->update([
            'is_available' => $request?->is_available ?? 0
        ]);

        return $this->success(null, 'Mode changed successfully');
    }
}
