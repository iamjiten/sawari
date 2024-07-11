<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettlementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "type" => $this->type,
            "channel" => $this->channel,
            "channel_parsed" => $this->channel?->name,
            "user_id" => $this->user_id,
            "user" => $this->user,
            "actual_amount" => $this->actual_amount,
            "settlement_amount" => $this->settlement_amount,
            "total_earned_amount" => $this->total_earned_amount,
            "total_settlement_amount" => $this->total_settlement_amount,
            "created_at" => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
