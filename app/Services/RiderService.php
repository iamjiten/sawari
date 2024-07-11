<?php

namespace App\Services;

use App\Enums\TripStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class RiderService
{
    /**
     * @throws Exception
     */
    public function riderRatings($id): ?User
    {
        $rider = User::query()
            ->select('id', 'name', 'mobile', 'kyc_status', 'type', 'photo')
            ->withCount(['trip', 'ratings',
                'ratings as positive_rating' => function ($query) {
                    $query->where('rating', '>', 3);
                },
                'trip as trip_completed' => function ($query) {
                    $query->where('status', TripStatusEnum::Completed->value);
                }
            ])
            ->findOrFail($id);

        if (!$this->riderCheck($rider)) {
            throw new Exception("Not a rider");
        }

        return $rider
            ->load([
                'ratings' => function ($query) {
                    $query
                        ->select('id', 'user_id', 'rating', 'review', 'rated_by', 'ratings.updated_at')
                        ->with('rater:id,name');
                }
            ]);
    }

    /**
     * @throws Exception
     */
    public function riderProfile($rider): User
    {
        $rider
            ->loadAvg('ratings', 'rating')
            ->loadSum(['trip' => function ($query) {
                $query->whereStatus(TripStatusEnum::Completed);
            }], 'amount')
            ->loadCount(['trip' => function ($query) {
                $query->where('status', TripStatusEnum::Completed);
            }])
            ->load(
                [
                    'vehicle' => function ($query) {
                        $query
                            ->select(['id', 'user_id', 'vehicle_type_id', 'brand_id', 'model_id', 'color_id', 'number_plate'])
                            ->with('vehicleType:id,weight_capacity,weight_unit,name')
                            ->with(['brand:id,value', 'model:id,value', 'color:id,value']);
                    }
                ]
            )
        ->load(['wallet' => fn($q) => $q->orderBy('id', 'desc')->first()]);

        return $rider;
    }

    private function riderCheck($rider): bool
    {
        if ($rider->type != UserTypeEnum::Rider) {
            return false;
        }
        return true;
    }
}
