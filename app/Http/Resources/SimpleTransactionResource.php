<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "pid" => $this->pid,
            "amount" => $this->amount,
            "status" => $this->status,
            "status_parsed" => $this->status?->name,
            "channel" => $this->channel,
            "channel_parsed" => $this->channel?->name,
            "transactional_id" => $this->transactional_id,
        ];
    }
}
