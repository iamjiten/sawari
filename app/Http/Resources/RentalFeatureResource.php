<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class RentalFeatureResource extends JsonResource
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
            'module' => $this->module,
            'category' => $this->category,
            'key' => $this->key,
            'value' => $this->value,
            'status' => $this->status,
            'icon' => $this->getMedia('icon')->first() ? $this->getMedia('icon')->first()->getUrl() : 'https://e7.pngegg.com/pngimages/376/197/png-clipart-car-gear-stick-manual-transmission-car-text-car.png',
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
