<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'sender_id' => $this->sender_id,
            'sender' => $this->sender,
            'receiver_id' => $this->receiver_id,
            'is_receiver_user' => $this->is_receiver_user,
            'receiver' => $this->is_receiver_user ? $this->receiverAsUser : $this->receiver,
            'package_category_id' => $this->package_category_id,
            'package_category' => $this->category,
            'package_sensible_id' => $this->package_sensible_id,
            'package_sensible' => $this->sensible,
            'package_size_id' => $this->package_size_id,
            'package_size' => $this->size,
            'sender_address' => $this->sender_address,
            'sender_latitude' => $this->sender_latitude,
            'sender_longitude' => $this->sender_longitude,
            'receiver_address' => $this->receiver_address,
            'receiver_latitude' => $this->receiver_latitude,
            'receiver_longitude' => $this->receiver_longitude,
            'sender_receiver_distance_unit' => $this->sender_receiver_distance_unit,
            'sender_receiver_distance' => $this->sender_receiver_distance,
            'amount' => $this->amount,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
