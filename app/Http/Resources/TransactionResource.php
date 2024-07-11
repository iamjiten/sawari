<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'pid' => $this->pid,
            'user_id' => $this->user_id,
            'user' => $this->user,
            'amount' => $this->amount,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'channel' => $this->channel,
            'channel_parsed' => $this->channel?->name,
            'parent_id' => $this->parent_id,
            'parent' => $this->parent,
            'created_by' => $this->createdBy,
            'updated_by' => $this->updatedBy,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
