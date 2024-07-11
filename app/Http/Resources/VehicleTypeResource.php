<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'weight_capacity' => $this->weight_capacity,
            'weight_unit' => $this->weight_unit,
            'icon' => $this->media->first() ? $this->media->first()->getUrl() : "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQQW-leymuAPn7liA_za4WhHiYI9mth23SgNfS_1s3XgO3QmY-rhIwzLvJ6cUvvzF6nR1s&usqp=CAU",
            'distance_unit' => $this->distance_unit,
            'per_distance_unit_cost' => $this->per_distance_unit_cost,
            'base_fare' => $this->base_fare,
            'status' => $this->status,
            // 'status_parsed' => $this->status?->name,
            'extra' => $this->extra,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
