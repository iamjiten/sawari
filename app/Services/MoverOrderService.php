<?php

namespace App\Services;

use App\Enums\MoverStatusEnum;
use App\Enums\OrderTrackEnum;
use App\Enums\TransactionStatusEnum;
use App\Enums\TripStatusEnum;
use App\Enums\UserTypeEnum;
use App\Events\MoverOrderTripCancelEvent;
use App\Events\OrderAssignedEvent;
use App\Events\OrderCancelEvent;
use App\Events\OrderReceivedEvent;
use App\Events\OrderTrackEvent;
use App\Events\RemoveDynamicOrderEvent;
use App\Notifications\OrderTrackNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MoverOrderService
{
    /**
     * @throws \Exception
     */
    public function toReceived($order): bool
    {
        if ($order->user_id != auth()->id())
            throw new \Exception("unauthorized to change status of this order");
        DB::beginTransaction();
        try {
            $order->update(['status' => MoverStatusEnum::Received->value, 'expires_at' => (new ExpireData())->getMoverExpiresAt()]);
            $order->transaction()->create([
                'pid' => Str::uuid()->toString(),
                'user_id' => auth()->id(),
                'amount' => $order->net_amount,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);
            addToOrderTrack($order, ['old' => MoverStatusEnum::Pending->name, 'new' => MoverStatusEnum::Received->name], OrderTrackEnum::Status->value);
            DB::commit();
            $order->load('user:id,name');
            broadcast(new OrderReceivedEvent($order))->toOthers();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new Exception($e);
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function toAssigned($order): bool
    {
        $rider = auth()->user();
        if ($rider->type != UserTypeEnum::Rider)
            throw new \Exception('not a valid rider');

        DB::beginTransaction();
        try {
            $this->changeStatus($order, MoverStatusEnum::Assigned->value);
            $order->trip()->create([
                'user_id' => $rider->id,
                'amount' => $order->net_amount,
                'action_by' => $rider->id,
            ]);
            addToOrderTrack($order, ['old' => MoverStatusEnum::Received->name, 'new' => MoverStatusEnum::Assigned->name], OrderTrackEnum::Status->value);
            DB::commit();
            $order->user->notify(new OrderTrackNotification($order));
            sendSMS($order->user->mobile, "Otp: " . $order->otp);
            broadcast(new OrderAssignedEvent($order))->toOthers();
            broadcast(new RemoveDynamicOrderEvent($order))->toOthers();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function toOnPickupLocation($order): bool
    {
        if (auth()->id() != $order->trip->user_id)
            throw new \Exception('not a valid rider');

        $this->changeStatus($order, MoverStatusEnum::On_Pickup_Location->value);
        $order->user->notify(new OrderTrackNotification($order));
        addToOrderTrack($order, ['old' => MoverStatusEnum::Received->name, 'new' => MoverStatusEnum::On_Pickup_Location->name], OrderTrackEnum::Status->value);
        broadcast(new OrderTrackEvent($order))->toOthers();
        return true;
    }

    /**
     * @throws \Exception
     */
    public function toOnWay($order): bool
    {
        if (auth()->id() != $order->trip->user_id)
            throw new \Exception('not a valid rider');

        $this->changeStatus($order, MoverStatusEnum::On_Way->value);
        $order->user->notify(new OrderTrackNotification($order));
        addToOrderTrack($order, ['old' => MoverStatusEnum::On_Pickup_Location->name, 'new' => MoverStatusEnum::On_Way->name], OrderTrackEnum::Status->value);
        broadcast(new OrderTrackEvent($order))->toOthers();
        return true;
    }

    /**
     * @throws \Exception
     */
    public function toOnDropLocation($order): bool
    {
        if (auth()->id() != $order->trip->user_id)
            throw new \Exception('not a valid rider');

        $this->changeStatus($order, MoverStatusEnum::On_Drop_Location->value);
        $order->user->notify(new OrderTrackNotification($order));
        addToOrderTrack($order, ['old' => MoverStatusEnum::On_Way->name, 'new' => MoverStatusEnum::On_Drop_Location->name], OrderTrackEnum::Status->value);
        broadcast(new OrderTrackEvent($order))->toOthers();
        return true;
    }

    /**
     * @throws \Exception
     */
    public function toCompleted($order): bool
    {
        if (auth()->id() != $order->trip->user_id)
            throw new \Exception('not a valid rider');

        // implement completed logic here
        DB::beginTransaction();
        try {
            $this->changeStatus($order, MoverStatusEnum::Completed->value);
            $order->user->update([
                'ask_to_rate_trip' => $order->trip->id
            ]);
            $order->transaction->update([
                'status' => TransactionStatusEnum::Completed->value
            ]);
            addToOrderTrack($order, ['old' => MoverStatusEnum::On_Drop_Location->name, 'new' => MoverStatusEnum::Completed->name], OrderTrackEnum::Status->value);
            DB::commit();
            $order->user->notify(new OrderTrackNotification($order));
        } catch (Exception $e) {
            DB::rollBack();
            throw new \Exception($e);
        }
        broadcast(new OrderTrackEvent($order))->toOthers();
        return true;
    }

    /**
     * @throws \Exception
     */
    public function toCancelledWithReason($order, $reason_id): bool
    {
        if (auth()->id() == $order->user_id || auth()->id() == $order->trip->user_id) {
            DB::beginTransaction();
            try {
                $this->changeStatus($order, MoverStatusEnum::Cancelled->value);
                $order->trip->update(['status' => TripStatusEnum::Cancelled, 'reason_id' => $reason_id, 'action_by' => auth()->id()]);
                addToOrderTrack($order, ['old' => MoverStatusEnum::Assigned->name, 'new' => MoverStatusEnum::Cancelled->name], OrderTrackEnum::Status->value);
                DB::commit();
                $order->user->notify(new OrderTrackNotification($order));
                $order->trip->user->notify(new OrderTrackNotification($order));
            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception($e);
            }
            broadcast(new OrderCancelEvent($order))->toOthers();
            return true;
        } else {
            throw new \Exception("not a valid user");
        }
    }


    /**
     * @throws \Exception
     */
    public function toCancelledWithoutReason($order): bool
    {
        if (auth()->id() != $order->user_id)
            throw new \Exception("not a valid user");
        $this->changeStatus($order, MoverStatusEnum::Cancelled->value);
        addToOrderTrack($order, ['old' => MoverStatusEnum::Received->name, 'new' => MoverStatusEnum::Cancelled->name], OrderTrackEnum::Status->value);
        $order->user->notify(new OrderTrackNotification($order));
        broadcast(new RemoveDynamicOrderEvent($order))->toOthers();
        return true;
    }

    private function changeStatus($order, $status)
    {
        return $order->update([
            'status' => $status
        ]);
    }
}
