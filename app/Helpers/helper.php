<?php

if (!function_exists('sendSMS')) {
    function sendSMS(string $number, string $message): bool
    {
        return (new \App\Helpers\SendSMS())->sendOtp($number, $message);
    }
}

if(!function_exists('addToOrderTrack')){
    function addToOrderTrack($order, $properties, $order_track_status){
        return (new \App\Services\OrderTrackService())->addToOderTrack($order, $properties, $order_track_status);
    }
}