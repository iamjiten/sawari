<?php

namespace App\Services;

use App\Enums\MoverStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTrackEnum;
use App\Enums\TripStatusEnum;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * @throws Exception
     */
    public function completeOrder($order): bool
    {
        $trip = $order->trip;

        if ($trip->user_id != auth()->id())
            throw new \Exception('unauthorized user');

        DB::beginTransaction();
        try {
            $this->changeOrderStatus($order, OrderStatusEnum::Delivered->value);
            $this->changeTripStatus($trip, TripStatusEnum::Completed->value);

            addToOrderTrack($order, ['old' => OrderStatusEnum::On_Drop_Location->name, 'new' => OrderStatusEnum::Delivered->name], OrderTrackEnum::Status->value);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }

    }

    private function changeOrderStatus($order, $status): bool
    {

        return $order->update([
            'status' => $status
        ]);
    }

    private function changeTripStatus($trip, $status): bool
    {
        return $trip->update([
            'status' => $status
        ]);
    }
}
