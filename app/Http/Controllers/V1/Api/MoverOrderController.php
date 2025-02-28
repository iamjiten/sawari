<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\MoverStatusEnum;
use App\Events\OrderReceivedEvent;
use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\ChangeMoverOrderRequest;
use App\Http\Requests\CheckMoverOtpRequest;
use App\Http\Requests\MoverOrderRequest;
use App\Http\Requests\MoverOrderUpdateRequest;
use App\Http\Requests\RetryOrderRequest;
use App\Http\Resources\MoverActivityResource;
use App\Http\Resources\MoverOrderResource;
use App\Http\Resources\SimpleMoversOrderResource;
use App\Http\Resources\TripResource;
use App\Models\MoverOrder;
use App\Models\Trip;
use App\Services\ExpireData;
use App\Services\MoverOrderService;
use Illuminate\Http\Request;

class MoverOrderController extends SuperController
{
    public function __construct(public MoverOrderService $moverService)
    {
        parent::__construct(
            MoverOrder::class,
            MoverOrderResource::class,
            MoverOrderRequest::class,
            MoverOrderUpdateRequest::class,
        );
    }

    public function store()
    {
        $model = $this->model::query()
            ->selfOngoing()
            ->latest()
            ->first();

        if ($model) {
            return $this->error('Ongoing order found', 422);
        }

        return parent::store(); // TODO: Change the autogenerated stub
    }

    public function changeOrderStatus(ChangeMoverOrderRequest $request)
    {
        $order = MoverOrder::find($request->order_id);

        if ($order) {
            switch ($request->status) {
                case MoverStatusEnum::Received->value:
                    try {
                        $this->moverService->toReceived($order);
                    } catch (\Exception $e) {
                        return $this->somethingWentWrong($e);
                    }
                    $order->refresh();
                    return $this->success(MoverOrderResource::make($order), 'status changed to received successfully');

                case MoverStatusEnum::Assigned->value:
                    try {
                        $this->moverService->toAssigned($order);
                    } catch (\Exception $e) {
                        return $this->somethingWentWrong($e);
                    }
                    return $this->success(null, 'status changed to assigned successfully');

                case MoverStatusEnum::On_Pickup_Location->value:
                    try {
                        $this->moverService->toOnPickupLocation($order);
                    } catch (\Exception $e) {
                        return $this->somethingWentWrong($e);
                    }
                    return $this->success(null, 'status changed to pickup location successfully');

                case MoverStatusEnum::On_Drop_Location->value:
                    try {
                        $this->moverService->toOnDropLocation($order);
                    } catch (\Exception $e) {
                        return $this->somethingWentWrong($e);
                    }
                    return $this->success(null, 'status changed to on drop location successfully');

                case MoverStatusEnum::Completed->value:
                    try {
                        $this->moverService->toCompleted($order);
                    } catch (\Exception $e) {
                        return $this->somethingWentWrong($e);
                    }
                    return $this->success(null, 'status changed to completed successfully');

                case MoverStatusEnum::Cancelled->value:
                    try {
                        $this->moverService->toCancelledWithReason($order, $request->reason_id);
                    } catch (\Exception $e) {
                        return $this->somethingWentWrong($e);
                    }
                    return $this->success(null, 'status changed to cancelled successfully');
                default:

            }
        } else {
            return $this->error('order does not exists', 500);
        }
    }

    public function cancelOrderWithoutReason(Request $request)
    {
        $request->validate([
            'order_id' => [
                'required'
            ]
        ]);
        $order = MoverOrder::find($request->order_id);

        if ($order->status != MoverStatusEnum::Received) {
            return $this->error('you cannot cancel this order now', 401);
        }

        try {
            $this->moverService->toCancelledWithoutReason($order);
        } catch (\Exception $e) {
            return $this->somethingWentWrong($e);
        }
        return $this->success(null, 'status changed to cancel successfully');
    }

    public function receivedOrders()
    {
        $user = auth()->user();
        $vehicle_type = $user->load('vehicle')->vehicle->vehicle_type_id;
        $model = $this->model::query()
            ->receivedOrders()
            ->where('vehicle_type_id',$vehicle_type)
            ->exceptSelf();

        return SimpleMoversOrderResource::collection($model->paginates());
    }

    public function checkOtp(CheckMoverOtpRequest $request)
    {
        $order = MoverOrder::find($request->order_id);
        if ($order) {
            if ($order->otp == $request->otp) {
                try {
                    $this->moverService->toOnWay($order);
                } catch (\Exception $e) {
                    return $this->somethingWentWrong($e);
                }
                return $this->success(null, 'otp verified');
            };
            return $this->error('otp verification failed', 500);
        } else {
            return $this->error('order does not exist', 404);
        }

    }

    public function retryOrder(RetryOrderRequest $request)
    {
        $order = MoverOrder::find($request->order_id);

        if ($order->user_id != auth()->id()) return $this->error('unauthenticated user', 401);
        if ($order) {
            //retry here
            $order->update(['expires_at' => (new ExpireData())->getMoverExpiresAt()]);
            broadcast(new OrderReceivedEvent($order));
            return $this->success(MoverOrderResource::make($order), 'retry successful');
        } else {
            return $this->error('order does not exists', 404);
        }
    }

    public function selfOngoing()
    {
        $model = $this->model::initializer()
            ->selfOngoing()
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->latest()
            ->first();

        if ($model) {
            return $this->resource::make($model);
        } else {
            return $this->success(null, 'No Data Found');
        }
    }

    public function selfActivity()
    {
        $model = $this->model::initializer()
            ->selfActivity()
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        return MoverActivityResource::collection($model->paginates());
    }

    public function riderActivity()
    {
        $model = Trip::query()
            ->moverRiderActivity()
            ->when(property_exists($this, 'withAll'), fn($query) => $query->with($this->withAll))
            ->when(property_exists($this, 'withCount'), fn($query) => $query->withCount($this->withCount))
            ->when(property_exists($this, 'withAggregate'), fn($query) => $this->applyWithAggregate($query))
            ->when(property_exists($this, 'scopes'), fn($query) => $this->applyScopes($query))
            ->when(property_exists($this, 'scopeWithValue'), fn($query) => $this->applyScopesWithValue($query));

        return TripResource::collection($model->paginate());
    }
}
