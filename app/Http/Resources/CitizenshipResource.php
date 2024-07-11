<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitizenshipResource extends JsonResource
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
            'user' => $this->user?->only(['name', 'email', 'mobile', 'address']),
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'citizenship_number' => $this->citizenship_number,
            'front_image' => $this->getMedia('front_image')->first() ? $this->getMedia('front_image')->first()->getUrl() : 'https://thehimalayantimes.com/uploads/imported_images/wp-content/uploads/2018/11/Citizenship.jpg',
            'back_image' => $this->getMedia('back_image')->first() ? $this->getMedia('back_image')->first()->getUrl() : 'https://thehimalayantimes.com/uploads/imported_images/wp-content/uploads/2018/11/Citizenship.jpg',
            'confirmation_image' => $this->getMedia('confirmation_image')->first() ? $this->getMedia('confirmation_image')->first()->getUrl() : 'https://support.okcoin.jp/hc/article_attachments/360063140653/OK_1.png',
            'issued_at' => $this->issued_at,
            'remarks' => $this->remarks,
            'extra' => $this->extra,
        ];
    }
}
