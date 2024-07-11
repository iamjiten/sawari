<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalysisRiderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'earned_amount' => count($this->settlement) > 0 ? round($this?->settlement()?->orderBy('id', 'desc')?->first()?->total_earned_amount, 2) : 0,
            'settlement_amount' => count($this->settlement) > 0 ? round($this?->settlement()?->orderBy('id', 'desc')?->first()?->total_settlement_amount, 2) : 0,
            'wallet' => count($this->wallet) > 0 ? $this?->wallet()?->orderBy('id', 'desc')?->first()?->total_amount : 0,
            'image' => $this->getMedia('profile')->first() ? $this->getMedia('profile')->first()->getUrl() : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT8lCCz3naP8eDnFr476DeuRV8FvxmratoD6q-yuxjKsMu-0DQCKMzH5PeTAk6qB7UyxmE&usqp=CAU',
            'ratings_avg_rating' => round($this->ratings_avg_rating, 2),
            'trip_count' => round($this->trip_count, 2),
        ];
    }
}
