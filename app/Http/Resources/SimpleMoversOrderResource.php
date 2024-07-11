<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SimpleMoversOrderResource extends JsonResource
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
            'model' => 'movers',
            'slug' => $this->slug,
            'shifting_from_address' => $this->shifting_from_address,
            'shifting_to_address' => $this->shifting_to_address,
            'shifting_at' => $this->shifting_at,
            'net_amount' => $this->net_amount,
            "distance" => round($this->distance, 2),
            'name' => $this->user->name,
            'expires_at' => Carbon::parse($this->expires_at)->format('Y-m-d H:i:s'),
        ];
    }
}
