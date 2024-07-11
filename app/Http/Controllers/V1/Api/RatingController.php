<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\KycTypeEnum;
use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\RatingRequest;
use App\Http\Resources\RatingResource;
use App\Http\Resources\TopRatedRiderResource;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;

class RatingController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            Rating::class,
            RatingResource::class,
            RatingRequest::class,
            RatingRequest::class
        );
    }

    public function topRatedRiders()
    {
        $riders = Rating::with('user')
            ->selectRaw('avg(rating) as total_rating, count(id) as total_rating_count, user_id')
            ->groupBy('user_id')->having('total_rating_count', '>', 2)
            ->orderBy('total_rating', 'desc')
            ->orderBy('total_rating_count', 'desc')
            ->limit(20)
            ->get();

        return TopRatedRiderResource::collection($riders);
    }

    public function update($id)
    {
        $model = $this->model::findOrFail($id);
        if ($model->user_id == auth()->id()) {
            return [
                'status' => 422,
                'message' => 'Unauthorized'
            ];
        }
        return parent::update($id);
    }

}

