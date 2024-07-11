<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedReceiverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"  => $this->id,
            "name"  => $this->name,
            "mobile"  => $this->mobile,
            "nick_name"  => $this->nick_name,
            "address"  => $this->address,
            "latitude"  => $this->latitude,
            "longitude"  => $this->longitude,
            "created_at"  => Carbon::parse($this->created_at)->format('Y-m-d H:i:s')
        ];
    }
}
