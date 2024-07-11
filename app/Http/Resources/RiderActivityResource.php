<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiderActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $package = $this->order->packages->first();
        return [
            'slug' => $this->order->slug,
            'status' => $this->status,
            'status_parsed' => $this->status?->name,
            'packages_count' =>$this->order->packages()->count(),
            'amount' => $this->amount,
            'date' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s'),
            'from' => $package->sender_address,
            'to' => $package->receiver_address,
            'package_category_icon' => $package->category->icon,
            'package_sensible' => $package->package_sensible_id
        ];
    }

}
