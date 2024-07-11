<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TypeSettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->media->first() ? $this->media->first()->getUrl() : null,
            'price' => $this->price,
            'type' => $this->type,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'parent_id' => $this->parent_id,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
