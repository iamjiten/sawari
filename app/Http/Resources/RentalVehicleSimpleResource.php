<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalVehicleSimpleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'image' => $this->getMedia('image')->first() ?
                $this->getMedia('image')->first()->getUrl() :
                'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJdmcgDo8IzoNtOk7zTs1NumbvD4AS1qGgqbaUjZC6v7ZU483CYF1P0ROSgcJNrImYd7I&usqp=CAU',
            'brand_id' => $this->brand_id,
            'brand' => $this->brand->value,
            'model_id' => $this->model_id,
            'model' => $this->model->value,
            'color_id' => $this->color_id,
            'color' => $this->color->value,
            'vehicle_type' => $this->vehicleType,
            'basic_infos' => RentalFeatureResource::collection($this->basicInfos),
            'services' => RentalFeatureResource::collection($this->services),
            'vehicle_information' => $this->vehicleInformation,
            'is_booked' => $this->booked ? RentalBookedResource::collection($this->booked) : null,
            'ready_to_book' => $this->booked_count,
        ];
    }
}
