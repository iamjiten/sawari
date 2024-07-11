<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => $this->user?->only(['name', 'email', 'mobile', 'address']),
            'vehicle_type_id' => $this->vehicle_type_id,
            'vehicle' => $this->vehicleType?->only('name', 'weight_capacity', 'weight_unit', 'icon'),
            'brand_id' => $this->brand_id,
            'brand' => $this->brand->value,
            'model_id' => $this->model_id,
            'model' => $this->model->value,
            'color_id' => $this->color_id,
            'color' => $this->color->value,
            'number_plate' => $this->number_plate,
            'production_year' => $this->production_year,
            'image' => $this->getMedia('image')->first() ? $this->getMedia('image')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJdmcgDo8IzoNtOk7zTs1NumbvD4AS1qGgqbaUjZC6v7ZU483CYF1P0ROSgcJNrImYd7I&usqp=CAU',
            'blue_book_first_image' => $this->getMedia('blue_book_first_image')->first() ? $this->getMedia('blue_book_first_image')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSJdmcgDo8IzoNtOk7zTs1NumbvD4AS1qGgqbaUjZC6v7ZU483CYF1P0ROSgcJNrImYd7I&usqp=CAU',
            'insurance_image' => $this->getMedia('insurance_image')->first() ? $this->getMedia('insurance_image')->first()->getUrl() : 'https://e7.pngegg.com/pngimages/452/190/png-clipart-health-insurance-health-care-star-health-and-allied-insurance-health-insurance-service-insurance.png',
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'extra' => $this->extra,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'remarks' => $this->remarks,
            'vehicle_information' => $this->whenLoaded('vehicleInformation', $this->vehicleInformation)
        ];
    }
}
