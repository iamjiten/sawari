<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class SimpleOrderResource extends JsonResource
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
            'model' => 'packages',
            'slug' => $this->slug,
            'name' => $this?->user?->name,
            'amount' => $this->net_amount,
            'packages_count' => $this->packages()->count(),
            'weight' => $this->packages->sum('size.weight'),
            'packages_location' => $this->packages->map(function ($package) {
                $package['sensible'] = $package?->sensible?->name;
                $package['category'] = $package->category->name;
                return $package?->only(['sender_address', 'receiver_address', 'sensible', 'category', 'sender_receiver_distance']);
            }),
            'expires_at' => Carbon::parse($this->expires_at)->format('Y-m-d H:i:s'),
        ];
    }
}
