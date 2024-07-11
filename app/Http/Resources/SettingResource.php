<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class SettingResource extends JsonResource
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
            'key' => $this->key,
            'value' => $this->value,
            'value_json' => $this->value_json,
            'parent_id' => $this->parent_id,
            'parent' => $this->parent,
            'editable' => $this->editable,
            'display_order' => $this->display_order,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
