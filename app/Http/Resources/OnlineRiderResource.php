<?php

namespace App\Http\Resources;

use App\Services\GeoLocationService;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OnlineRiderResource extends JsonResource
{
    public function toArray($request): array
    {
        $geo = (new GeoLocationService())->geocode(@$this->extra['latitude'], @$this->extra['longitude']);
        return [
            'id' => $this->id,
            'is_online' => $this->is_online,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'longitude' => @$this->extra['longitude'],
            'latitude' => @$this->extra['latitude'],
            'address' => $geo ?? 'N/A',
            'last_seen' => $this->last_seen,
            'image' => $this->getMedia('profile')->first() ? $this->getMedia('profile')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT8lCCz3naP8eDnFr476DeuRV8FvxmratoD6q-yuxjKsMu-0DQCKMzH5PeTAk6qB7UyxmE&usqp=CAU',
        ];
    }
}
