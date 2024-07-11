<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile_number' => $this->mobile_number,
            'profile' => $this->getMedia('profile')->first() ? $this->getMedia('profile')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT8lCCz3naP8eDnFr476DeuRV8FvxmratoD6q-yuxjKsMu-0DQCKMzH5PeTAk6qB7UyxmE&usqp=CAU',
            'email' => $this->email,
            'address' => $this->address,
            'pan_number' => $this->pan_number ?? 'N/A',
            'website' => $this->website ?? 'N/A',
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
