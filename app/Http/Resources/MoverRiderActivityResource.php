<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoverRiderActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->order->slug,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'amount' => $this->amount,
            'order_id' => $this->order->id,
            'shifting_from_address' => $this->order->shifting_from_address,
            'shifting_to_address' => $this->order->shifting_to_address,
            'galli_distance' => $this->order->galli_distance,
            'date' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s')
        ];
    }
}
