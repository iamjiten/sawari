<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalVehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'merchant_id' => $this->merchant_id,
            'merchant' => $this->merchant,
            'image' => $this->getMedia('image')->first() ?
                $this->getMedia('image')->first()->getUrl() :
                'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJdmcgDo8IzoNtOk7zTs1NumbvD4AS1qGgqbaUjZC6v7ZU483CYF1P0ROSgcJNrImYd7I&usqp=CAU',
            'brand_id' => $this->brand_id,
            'brand' => $this->brand?->value,
            'production_year' => $this->production_year,
            'model_id' => $this->model_id,
            'model' => $this->model?->value,
            'color_id' => $this->color_id,
            'color' => $this->color?->value,
            'is_available' => $this->is_available ?? 0,
            'vehicle_type_id' => $this->vehicle_type_id,
            'vehicle_type' => $this->vehicleType?->name,
            'basic_infos' => count($this->basicInfos) > 0 ? RentalFeatureResource::collection($this->basicInfos) : [],
            'services' => count($this->services) > 0 ? RentalFeatureResource::collection($this->services) : [],
            'detail_info' => $this->vehicleInformation?->detail_info,
            'discount_percent' => $this->vehicleInformation?->discount_percent,
            'per_day_driver_fare' => $this->vehicleInformation?->per_day_driver_fare,
            'per_day_fare' => $this->vehicleInformation?->per_day_fare,
            'withDriver' => $this->vehicleInformation?->withDriver,
            'is_booked' => $this->booked ? RentalBookedResource::collection($this->booked) : null,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
