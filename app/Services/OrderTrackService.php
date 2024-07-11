<?php

namespace App\Services;

class OrderTrackService
{
    public function addToOderTrack($order, array $properties, $order_track_status): void
    {
        $order->track()->create([
            'action_type' => $order_track_status,
            'causer_id' => auth()->id(),
            'properties' => $properties
        ]);
    }
}