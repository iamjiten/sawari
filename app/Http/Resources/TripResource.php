<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TripResource extends JsonResource
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
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'amount' => $this->amount,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'reason_id' => $this->reason_id,
            'reason' => $this->reason,
            'action_by' => $this->action_by,
            'action_at' => $this->action_at,
            'extra' => $this->extra,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s')
        ];

    }
}
