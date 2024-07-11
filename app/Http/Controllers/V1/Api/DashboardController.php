<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\TripStatusEnum;
use App\Exceptions\ApiResponder;
use App\Http\Controllers\Controller;
use App\Models\Citizenship;
use App\Models\DrivingLicense;
use App\Models\MoverOrder;
use App\Models\Order;
use App\Models\RentalOrder;
use App\Models\Settlement;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Wallet;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use ApiResponder;

    public function dashboard()
    {
        $pending_packages = Order::where('status', 0)->count();
        $request_packages = Order::where('status', 1)->count();

        $pending_movers = MoverOrder::where('status', 0)->count();
        $request_movers = MoverOrder::where('status', 1)->count();

        $pending_citizen_ships = Citizenship::where('status', 1)->count();
        $pending_license = DrivingLicense::where('status', 1)->count();
        $pending_vehicle = Vehicle::whereNull('is_available')->where('status', 1)->count();

        $riders = User::where('type', 2)->count();
        $customers = User::where('type', 1)->count();

        $checkDate = Carbon::now()->subMinutes(5);
        $online = User::query()
            ->where('type', 2)
            ->where('last_seen', '>=', $checkDate)
            ->count();

        $on_trip = User::query()
            ->where('type', 2)
            ->whereHas('trip', function ($q) {
                $q->whereStatus(TripStatusEnum::Assigned);
            })
            ->count();

        $rental_booked = Vehicle::query()
            ->whereNotNull('is_available')
            ->whereHas('vehicleInformation')
            ->whereHas('basicInfos')
            ->whereHas('services')
            ->has('booked');
        if (auth()->user()->merchant_id) {
            $rental_booked = $rental_booked->where('merchant_id', auth()->user()->merchant_id);
        }
        $rental_booked = $rental_booked->count();

        $rental_available = Vehicle::query()
            ->whereHas('vehicleInformation')
            ->whereHas('basicInfos')
            ->whereHas('services')
            ->where('is_available', 1);
        if (auth()->user()->merchant_id) {
            $rental_available = $rental_available->where('merchant_id', auth()->user()->merchant_id);
        }
        $rental_available = $rental_available->count();

        if (auth()->user()->merchant_id) {
            $total = Settlement::where('user_id', auth()->id())->orderBy('id', 'desc')->first();
            $total_wallet = Wallet::where('user_id', auth()->id())->orderBy('id', 'desc')->first();
            $total_rental_vehicle_request = RentalOrder::query()
                ->whereHas('vehicles', function ($q) {
                    $q->where('merchant_id', auth()->user()->merchant_id);
                })
                ->where('status', 1)
                ->count();

            return [
                'rental_booked' => $rental_booked,
                'rental_available' => $rental_available,
                'total_earned' => $total?->total_earned_amount,
                'total_wallet' => $total_wallet->total_amount,
                'total_rental_vehicle_request' => $total_rental_vehicle_request,
            ];
        } else {
            return [
                'pending_packages' => $pending_packages,
                'request_packages' => $request_packages,
                'pending_movers' => $pending_movers,
                'request_movers' => $request_movers,
                'pending_citizen_ships' => $pending_citizen_ships,
                'pending_license' => $pending_license,
                'pending_vehicle' => $pending_vehicle,
                'riders' => $riders,
                'customers' => $customers,
                'online' => $online,
                'on_trip' => $on_trip,
                'rental_booked' => $rental_booked,
                'rental_available' => $rental_available,
            ];
        }
    }

}
