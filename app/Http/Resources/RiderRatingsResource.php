<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiderRatingsResource extends JsonResource
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
            'name' => $this->name,
            'mobile' => $this->mobile,
            'photo' => $this->photo,
            'kyc_status' => $this->kyc_status,
            'total_trip' => $this->trip_count,
            'total_review' => $this->ratings_count,
            'review_percentage' => (int)round($this->divideByZeroHandler($this->ratings_count, $this->trip_count) * 100),
            'positive_review' => (int)round($this->divideByZeroHandler($this->positive_rating, $this->ratings_count) * 100),
            'trip_completed' => $this->trip_completed,
            'ratings' => SimpleRatingResource::collection($this->ratings),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d H:i:s')
        ];
    }

    private function divideByZeroHandler($numerator, $denominator): float
    {
        try {
            return $numerator / $denominator;
        } catch (\DivisionByZeroError $e) {
            return 0;
        }
    }

}
