<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->media->first() ? $this->media->first()->getUrl() : "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQQW-leymuAPn7liA_za4WhHiYI9mth23SgNfS_1s3XgO3QmY-rhIwzLvJ6cUvvzF6nR1s&usqp=CAU",
            'min_day' => $this->min_day,
            'max_day' => $this->max_day,
            'price' => $this->price,
            'status' => $this->status,
            'extra' => $this->extra,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
