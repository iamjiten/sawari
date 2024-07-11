<?php

namespace App\Http\Resources;

use App\Enums\RentalOrderStatusEnum;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalysisRentalResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'merchant' => MerchantResource::make($this->merchant),
            'earned_amount' => count($this->settlement) > 0 ? round($this?->settlement()?->orderBy('id', 'desc')?->first()?->total_earned_amount, 2) : 0,
            'settlement_amount' => count($this->settlement) > 0 ? round($this?->settlement()?->orderBy('id', 'desc')?->first()?->total_settlement_amount, 2) : 0,
            'wallet' => count($this->wallet) > 0 ? $this?->wallet()?->orderBy('id', 'desc')?->first()?->total_amount : 0,
            'image' => $this->getMedia('profile')->first() ? $this->getMedia('profile')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT8lCCz3naP8eDnFr476DeuRV8FvxmratoD6q-yuxjKsMu-0DQCKMzH5PeTAk6qB7UyxmE&usqp=CAU',
            'booked_orders_count' => $this->whenLoaded('vehicle')?->rentalOrders->where('status', RentalOrderStatusEnum::Booked)->count(),
            'completed_orders_count' => $this->whenLoaded('vehicle')?->rentalOrders->where('status', RentalOrderStatusEnum::Completed)->count(),
        ];
    }
}
