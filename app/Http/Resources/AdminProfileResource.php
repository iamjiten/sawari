<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'merchant_id' => $this->merchant_id,
            'merchant' => $this->merchant ? MerchantResource::make($this->merchant) : null,
            'mobile' => $this->mobile,
            'image' => $this->getMedia('profile')->first() ? $this->getMedia('profile')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT8lCCz3naP8eDnFr476DeuRV8FvxmratoD6q-yuxjKsMu-0DQCKMzH5PeTAk6qB7UyxmE&usqp=CAU',
            'email' => $this->email,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'is_online' => $this->is_online,
            'last_seen' => $this->last_seen,
            'permissions' => $this->getPermissionsViaRoles(),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
