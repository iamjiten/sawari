<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageSizeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'weight' => $this->weight,
            'weight_unit' => $this->weight_unit,
            'icon' => $this->media->first() ? $this->media->first()->getUrl() : "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSodjn3cyB7s1ZwLkYaPXYkWpjz_M1Mbmli186hNpw4QJclOGHnEbSEMboVNs_6r4WOaNc&usqp=CAU",
            'price' => $this->price,
            'status' => $this->status,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
