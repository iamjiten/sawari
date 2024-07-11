<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressBookResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => UserListResource::make($this->user),
            'name' => $this->name,
            'nick_name' => $this->nick_name ?? $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'latitude' => (double)$this->latitude,
            'longitude' => (double)$this->longitude,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
