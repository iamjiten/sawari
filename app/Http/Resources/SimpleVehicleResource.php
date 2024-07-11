<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleVehicleResource extends JsonResource
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
            'user' => $this->user->only(['name', 'email', 'mobile', 'address']),
            'vehicle_type_id' => $this->vehicle_type_id,
            'vehicle' => $this->vehicleType->only('name', 'weight_capacity', 'weight_unit', 'icon'),
            'brand_id' => $this->brand_id,
            'brand' => $this->brand->value,
            'model_id' => $this->model_id,
            'model' => $this->model->value,
            'color_id' => $this->color_id,
            'color' => $this->color->value,
            'number_plate' => $this->number_plate,
            'production_year' => $this->production_year,
            'image' => $this->getMedia('image')->first() ? $this->getMedia('image')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJdmcgDo8IzoNtOk7zTs1NumbvD4AS1qGgqbaUjZC6v7ZU483CYF1P0ROSgcJNrImYd7I&usqp=CAU',
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'extra' => $this->extra
        ];
    }
}
