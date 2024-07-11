<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\RentalOrderStatusEnum;
use App\Enums\StatusEnum;
use App\Enums\TripStatusEnum;
use App\Enums\UserTypeEnum;
use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\AdminProfileResource;
use App\Http\Resources\AnalysisRentalResource;
use App\Http\Resources\AnalysisRiderResource;
use App\Http\Resources\MobilCheckResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\OnlineRiderResource;
use App\Http\Resources\OnTripRiderResource;
use App\Http\Resources\RiderProfileResource;
use App\Http\Resources\RiderRatingsResource;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\RiderService;
use Carbon\Carbon;
use Exception;
use App\Notifications\PasswordChange;
use App\Rules\MobileNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class UserController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            User::class,
            UserResource::class,
            UserRequest::class,
            \request()->type == 3 ? UserRequest::class : UserUpdateRequest::class
        );
        $this->listResource = UserListResource::class;
    }

    public function adminProfile(): JsonResponse
    {
        if (auth()->user()->status?->value == StatusEnum::Inactive?->value) {
            return $this->error('Your account has been banned. Please contact to support team', 422);
        }

        return $this->success(AdminProfileResource::make(auth()->user()), 'Profile fetched successfully');
    }

    public function changePassword(Request $request)
    {
        $message = [
            'password_confirmation.same' => "Password confirmation does not match"
        ];
        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|min:6|same:password',
        ], $message);

        $user = auth()->user();
        $hashedPassword = $user->password;

        if (Hash::check($request->input('current_password'), $hashedPassword)) {
            if (!Hash::check($request->input('password'), $hashedPassword)) {
                try {
                    $user->update([
                        'password' => bcrypt($request->input('password')),
                        'is_changed' => 1
                    ]);
                    Notification::send($user, new PasswordChange());

                    return $this->success($user, 'Password changed successfully');
                } catch (Exception $e) {

                    return $this->somethingWentWrong($e);
                }
            } else {

                return $this->error('Current password and new password cannot be the same', 422);
            }
        } else {

            return $this->error('Current password does not match', 422);
        }
    }

    public function checkMobile(Request $request)
    {
        $request->validate([
            'mobile' => ['required', new MobileNumber()]
        ]);
        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json(['data' => null], 200);
        }

        return MobilCheckResource::make($user);
    }

    public function profile(): JsonResponse
    {
        try {
            if (auth()->user()->status?->value == StatusEnum::Inactive?->value) {
                return $this->success(null, 'Your account has been banned. Please contact to support team', 203);
            }

            return $this->success(UserResource::make(auth()->user()), 'profile');
        } catch (Exception $e) {
            return $this->somethingWentWrong($e);
        }
    }

    public function riderProfile(RiderService $riderService, $id = null): JsonResponse
    {
        if ($id) {
            $rider = User::findOrFail($id);
        } else {
            $rider = auth()->user();
        }
        try {
            if ($rider->status?->value == StatusEnum::Inactive?->value) {
                return $this->success(null, 'Your account has been banned. Please contact to support team', 203);
            }
            $rider = $riderService->riderProfile($rider);

            return $this->success(RiderProfileResource::make($rider), 'profile');
        } catch (Exception $e) {
            return $this->somethingWentWrong($e);
        }

    }

    public function changeOnlineStatus(Request $request): JsonResponse
    {
        $request = $request->validate([
            "status" => [
                'required',
                'in:0,1'
            ]
        ]);

        $rider = auth()->user();

        if ($rider->type == UserTypeEnum::Rider) {
            $rider->update([
                'is_online' => $request['status']
            ]);
            return $this->success(null, 'status changed successfully');
        } else {
            return $this->error('you cannot access rider profile', 401);
        }

    }

    public function riderRatings(RiderService $riderService)
    {
        try {
            $rider = $riderService->riderRatings(auth()->id());
            return RiderRatingsResource::make($rider);
        } catch (Exception $e) {
            return $this->somethingWentWrong($e);
        }
    }

//    public function onlineRiders()
//    {
//        $check_date = Carbon::now()->subMinutes(5)->toDateTimeString();
//        $model = $this->model::initializer()
//            ->where('type', 2)
//            ->where('last_seen', '>=', $check_date);
//
//        return OnlineRiderResource::collection($model->paginates());
//    }

    public function onlineRiders(Request $request)
    {
        $filter = request()->input('filters');

        $checkDate = Carbon::now()->subMinutes(5);

        $model = $this->model::initializer()
            ->where('type', 2)
            ->where('last_seen', '>=', $checkDate);

        if ($filter) {
            $filterValue = json_decode($filter, true);
            if (array_key_exists('address', $filterValue)) {
                $lat1 = @$filterValue['address']['value']['latitude'];
                $lon1 = @$filterValue['address']['value']['longitude'];
                $radiusInKm = 5;


                $model = $model->get()->filter(function ($rider) use ($lat1, $lon1, $radiusInKm) {
                    $distance = $this->haversineDistance($lat1, $lon1, @$rider->extra['latitude'], @$rider->extra['longitude']);

                    return $distance <= $radiusInKm;
                });

                return OnlineRiderResource::collection($model);
            }
        }

        return OnlineRiderResource::collection($model->paginates());
    }

    public function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $dLat = $lat2Rad - $lat1Rad;
        $dLon = $lon2Rad - $lon1Rad;

        $a = sin($dLat / 2) * sin($dLat / 2) + cos($lat1Rad) * cos($lat2Rad) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }


    public function onTrip()
    {
        $model = $this->model::initializer()
            ->where('type', 2)
            ->whereHas('trip', function ($q) {
                $q->whereStatus(TripStatusEnum::Assigned);
            })
            ->with('trip.order.vehicleType');

        return OnTripRiderResource::collection($model->paginates());
    }

    public function riderAnalysis()
    {
        $model = $this->model::initializer()
            ->where('type', 2)
            ->withAvg('ratings', 'rating')
            ->with('wallet')
            ->with('settlement')
            ->withCount(['trip' => function ($query) {
                $query->where('status', TripStatusEnum::Completed);
            }])
            ->having('trip_count', '>', 0)
            ->latest('trip_count');

        return AnalysisRiderResource::collection($model->paginates());
    }

    public function merchantAnalysis()
    {
        $model = $this->model::initializer()
            ->where('type', 3)
            ->whereNotNull('merchant_id')
            ->whereHas('vehicle.rentalOrders', function ($q) {
                $q->whereIn('status', [RentalOrderStatusEnum::Booked, RentalOrderStatusEnum::Completed]);
            })
            ->with('vehicle.rentalOrders')
            ->with('wallet')
            ->with('settlement')
            ->latest();

        return AnalysisRentalResource::collection($model->paginates());
    }

    public function setAskToRateTripToNull()
    {
        auth()->user()->update([
            'ask_to_rate_trip' => null
        ]);

        return $this->success(null, 'status changed successfully');
    }

    public function getSelfNotification()
    {
        $notifications = (auth()->user())->notifications;
        return NotificationResource::collection($notifications);
    }

    public function readNotification($id)
    {
        $notification = auth()->user()->notifications->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return $this->success(null, 'success');
        }
        return $this->error('notification not found', 404);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        $user->delete();
        return $this->success(null, 'user deleted successfully');
    }

    public function userLocation(Request $request)
    {
//        $request->validate([
//            'longitude' => [
//                'required'
//            ],
//            'latitude' => [
//                'required'
//            ]
//        ]);

        $location = ["longitude" => $request->longitude, "latitude" => $request->latitude];
        auth()->user()->setRiderLocation($location);
    }

}
