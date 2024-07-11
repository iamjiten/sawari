<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiResponder;
use App\Http\Resources\OrderTrackResource;
use App\Models\MoverOrder;
use App\Models\Order;
use App\Models\RentalOrder;
use Illuminate\Http\Request;

class OrderTrackController extends Controller
{
    use ApiResponder;

    public function track(Request $request)
    {
        $request->validate([
            'slug' => [
                'required'
            ],
            'token' => [
                'required'
            ],
            'module' => [
                'required'
            ]
        ]);

        $module = $this->getModule($request->module);

        $order = $module::query()
            ->when(
                $request->module == 'mover',
                fn($query) => $query->whereOtp($request->token),
                fn($query) => $query->where(function ($query) use ($request) {
                    $query->where('token', $request->token)
                        ->orWhere('receiver_token', $request->token);
                })
            )
            ->whereSlug($request->slug)
            ->first('id');

        if (!$order) {
            return $this->error('Token/Tracking Id Not Match', 404);
        }

        return OrderTrackResource::collection($order->track()->orderBy('created_at', 'desc')->get());
    }

    public function authTrack(Request $request)
    {
        $request->validate([
            'slug' => [
                'required'
            ],
            'module' => [
                'required'
            ]
        ]);

        $module = $this->getModule($request->module);
        $order = $module::whereUserId(auth()->id())
            ->whereSlug($request->slug)
            ->first();

        if ($order) {
            return OrderTrackResource::collection($order->track()->orderBy('created_at', 'desc')->get());
        }
        return $this->error('no order found', 404);
    }

    private function getModule($module)
    {
        return match ($module) {
            'package' => Order::class,
            'rental' => RentalOrder::class,
            'mover' => MoverOrder::class
        };
    }
}
