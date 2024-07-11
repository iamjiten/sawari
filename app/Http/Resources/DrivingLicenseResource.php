<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DrivingLicenseResource extends JsonResource
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
            'user' => @$this?->user?->only(['name', 'email', 'mobile', 'address']),
            'license_number' => $this->license_number,
            'front_image' => $this->getMedia('front_image')->first() ? $this->getMedia('front_image')->first()->getUrl() : 'https://thehimalayantimes.com/uploads/imported_images/wp-content/uploads/2018/11/Citizenship.jpg',
            'back_image' => $this->getMedia('back_image')->first() ? $this->getMedia('back_image')->first()->getUrl() : 'https://thehimalayantimes.com/uploads/imported_images/wp-content/uploads/2018/11/Citizenship.jpg',
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'expired_at' => $this->expired_at,
            'remarks' => $this->remarks,
            'extra' => $this->extra,
        ];
    }
}
