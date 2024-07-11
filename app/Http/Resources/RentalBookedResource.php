<?php

namespace App\Http\Resources;

use App\Enums\RentalOrderStatusEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalBookedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->order?->status == RentalOrderStatusEnum::Booked) {
            $key = "Booked";
        } else {
            $key = "Reserved";
        }
        return [
            'key' => $key,
            'from' => Carbon::parse($this->from)->toFormattedDateString() . ' ' . Carbon::parse($this->from)->format('H:i a'),
            'to' => Carbon::parse($this->from)->toFormattedDateString() . ' ' . Carbon::parse($this->to)->format('H:i a'),
        ];
    }
}
