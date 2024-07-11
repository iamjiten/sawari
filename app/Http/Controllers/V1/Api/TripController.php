<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\MoverStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTrackEnum;
use App\Enums\PackageStatusEnum;
use App\Enums\SettlementChannelEnum;
use App\Enums\TripStatusEnum;
use App\Events\OrderAssignedEvent;
use App\Events\OrderCancelEvent;
use App\Events\OrderTrackEvent;
use App\Events\RemoveDynamicOrderEvent;
use App\Events\TripStatusEvent;
use App\Exceptions\ApiResponder;
use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\CancelMoversTripRequest;
use App\Http\Requests\TripRequest;
use App\Http\Requests\VerifyReceiverOtpRequest;
use App\Http\Resources\MostEarnedResource;
use App\Http\Resources\MostTripResource;
use App\Http\Resources\MoverRiderActivityResource;
use App\Http\Resources\RiderActivityResource;
use App\Http\Resources\TripResource;
use App\Models\MoverOrder;
use App\Models\Order;
use App\Models\Trip;
use App\Models\Wallet;
use App\Notifications\OrderTrackNotification;
use App\Services\OrderService;
use App\Services\SettlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripController extends SuperController
{
    use ApiResponder;

    public function __construct()
    {
        parent::__construct(
            Trip::class,
            TripResource::class,
            TripRequest::class,
            TripRequest::class
        );

        $this->middleware('auth:sanctum')->only(['tripHandler']);
    }

    public function tripHandler(TripRequest $request)
    {
        $userId = auth()->id();
        $response = false;
        $message = '';

        $order_id = $request->order_id;
        $order = Order::find($order_id);

        if ($order->status == OrderStatusEnum::On_Way) {
            return $this->error('order already on way', 500);
        }

        $trip = $order->trip;

        if (!$trip) {
            try {
                DB::beginTransaction();
                if ($order->expires_at <= now()) {
                    return $this->error('order has been expired', 404);
                }
                $newTrip = $order->trip()->create([
                    'user_id' => $userId,
                    'amount' => $order->net_amount,
                    'action_by' => $userId
                ]);

                $senderMobileNumber = $order->user->mobile;

                $package = $order->packages->first();

                if ($package->is_receiver_user) {
                    $receiver = $package->receiverAsUser;
                } else {
                    $receiver = $package->receiver;
                }

                sendSMS($senderMobileNumber, 'token: ' . $order->token . "\norder id: " . $order->slug);
                sendSMS($receiver->mobile, 'token: ' . $order->receiver_token . "\norder id: " . $order->slug);

                $response = (bool)$newTrip->order()->update([
                    'status' => OrderStatusEnum::Assigned->value,
                ]);
                addToOrderTrack($order, ['old' => OrderStatusEnum::Received->name, 'new' => OrderStatusEnum::Assigned->name], OrderTrackEnum::Status->value);
                DB::commit();
                $order->user->notify(new OrderTrackNotification($order));
                broadcast(new OrderAssignedEvent($order))->toOthers();
                broadcast(new RemoveDynamicOrderEvent($order))->toOthers();

                return $this->success([
                    "status" => $response
                ], "Trip Created Successfully");
            } catch (Exception $e) {
                return $this->somethingWentWrong($e);
            }


        } else {
            $hasStatus = $request->status;
            if ($hasStatus) {
                $requestStatus = $request->status;
                if ($requestStatus == TripStatusEnum::Cancelled->value && !in_array($trip->order->status?->value, [OrderStatusEnum::Received->value, OrderStatusEnum::Assigned->value])) {
                    return $this->error('you cannot cancel this order now', 422);
                }
                DB::beginTransaction();
                $request->merge([
                    'action_by' => auth()->id(),
                ]);
                if ($requestStatus == TripStatusEnum::Cancelled->value) {
                    $trip->update($request->toArray());
                    $response = (bool)($trip->order()->update([
                        'status' => OrderStatusEnum::Cancelled->value
                    ]));
                    addToOrderTrack($order, ['old' => OrderStatusEnum::Received->name, 'new' => OrderStatusEnum::Cancelled->name], OrderTrackEnum::Status->value);

                    broadcast(new TripStatusEvent($trip))->toOthers();
                    $message = 'Trip Cancelled';
                }
                DB::commit();
                $order->user->notify(new OrderTrackNotification($order));
                $order->trip->user->notify(new OrderTrackNotification($order));
                return $this->success([
                    "status" => $response
                ], $message);
            }

            return $this->error("Status filed is required", 404);

        }
    }


    public
    function mostTrips()
    {
        // below code gives only tips of the delivery module
        $mostTrips = Trip::query()
            ->selectRaw('user_id, count(user_id) as total_trips')
            ->with('user')
            ->type(Order::class)
            ->groupBy('user_id')
            ->having('total_trips', '>', 2)
            ->orderBy('total_trips', 'desc')
            ->limit(20)
            ->get();

        return MostTripResource::collection($mostTrips);
    }

    public
    function mostEarned()
    {
        $mostEarned = Trip::query()
            ->selectRaw('user_id, SUM(amount) as amount')
            ->with('user')
            ->type(Order::class)                        //only from delivery type trip (not from mover trip)
            ->where('status', TripStatusEnum::Completed)
            ->groupBy('user_id')
            ->orderBy('amount', 'desc')
            ->limit(20)
            ->get();

        return MostEarnedResource::collection($mostEarned);
    }


    public
    function riderActivity()
    {
        $model = $this->model::initializer()
            ->riderActivity()
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        return RiderActivityResource::collection($model->paginates());
    }

    public
    function riderMoverActivity()
    {
        $model = $this->model::initializer()
            ->moverActivity()
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        return MoverRiderActivityResource::collection($model->paginates());
    }

    public function cancelMoversTrip(CancelMoversTripRequest $request)
    {
        $order = MoverOrder::find($request->order_id);
        $trip = $order->trip;

        DB::beginTransaction();
        try {
            $trip->update([
                'status' => TripStatusEnum::Cancelled->value,
                'reason_id' => $request->reason_id,
                'action_by' => auth()->id(),
                'action_at' => now()
            ]);
            $order->update(['status' => MoverStatusEnum::Cancelled->value]);
            addToOrderTrack($order, ['old' => MoverStatusEnum::Assigned->name, 'new' => MoverStatusEnum::Cancelled->name], OrderTrackEnum::Status->value);

            DB::commit();
            broadcast(new OrderCancelEvent($order))->toOthers();
            return $this->success(null, 'order cancelled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->somethingWentWrong($e);
        }
    }

    public function completeMoversTrip(Request $request)
    {
        $request->validate([
            'order_id' => [
                'required',
                'exists:mover_orders,id'
            ]
        ]);
        $order = MoverOrder::find($request->order_id);
        $trip = $order->trip;

        DB::beginTransaction();
        try {
            $trip->update([
                'status' => TripStatusEnum::Completed->value
            ]);
            $order->update(['status' => MoverStatusEnum::Completed->value]);

            addToOrderTrack($order, ['old' => MoverStatusEnum::On_Drop_Location->name, 'new' => MoverStatusEnum::Completed->name], OrderTrackEnum::Status->value);

            DB::commit();
            $trip->order->user->notify(new OrderTrackNotification($order));
            $trip->user->notify(new OrderTrackNotification($order));
            return $this->success(null, 'order completed');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->somethingWentWrong($e);
        }
    }

    public function verifyReceiverOtp(VerifyReceiverOtpRequest $request): JsonResponse
    {
        $order = Order::find($request->order_id);
        if ($order) {
            if ($order->receiver_token == $request->token) {

                DB::beginTransaction();
                // change order status to complete
                try {
                    (new OrderService())->completeOrder($order);
                    $order->user->update([
                        'ask_to_rate_trip' => $order->trip->id
                    ]);
                    $trip = $order->trip;
                    // create settlement here
                    $settlementService = new SettlementService($trip);
                    $settlement = $settlementService->createSettlement([
                        "type" => "CR",
                        "channel" => SettlementChannelEnum::MANUAL->value,
                        "trip_id" => $trip->id,
                        "user_id" => $trip->user_id,
                        "actual_amount" => $trip->amount
                    ]);

                    // update rider wallet here
                    $previousWallet = Wallet::where('user_id', $settlement->user_id)->orderBy('id', 'desc')->first();
                    $previousTotalAmount = $previousWallet ? $previousWallet->total_amount : 0;
                    $settlement->wallet()->create([
                        "type" => 'DR',
                        "user_id" => $settlement->user_id,
                        "amount" => $settlement->settlement_amount,
                        "total_amount" => $previousTotalAmount - $settlement->settlement_amount
                    ]);
                    addToOrderTrack($order, ['old' => OrderStatusEnum::On_Drop_Location->name, 'new' => OrderStatusEnum::Delivered->name], OrderTrackEnum::Status->value);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->somethingWentWrong($e);
                }
                broadcast(new OrderTrackEvent($order));
                return $this->success(null, 'token verified');
            } else {
                return $this->error('token verification failed', 404);
            }
        } else {
            return $this->error('order does not exist', 404);
        }

    }

}
