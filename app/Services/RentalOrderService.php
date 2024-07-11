<?php

namespace App\Services;

use Carbon\Carbon;

class RentalOrderService
{
    public function calculateActualAmount($vehicle_fare, $driver_fare, $withDriver)
    {
        if ($withDriver == 0) {
            return $vehicle_fare;
        }
        return $vehicle_fare + $driver_fare;
    }

    public function calculateReserveDays($pickup_date, $dropOff_date): float
    {
        $pickup_datetime = Carbon::parse($pickup_date);
        $dropOff_datetime = Carbon::parse($dropOff_date);

        $minutes = $pickup_datetime->diffInMinutes($dropOff_datetime);

        $days = $minutes / (24 * 60);

        if ($days <= 1) {
            return 1;
        }

        return round($days, 2);
    }

    public function calculateReserveDuration($pickup_date, $dropOff_date)
    {
        $pickup_datetime = Carbon::parse($pickup_date);
        $dropOff_datetime = Carbon::parse($dropOff_date);

        $minutes = $dropOff_datetime->diffInMinutes($pickup_datetime);

        if ($minutes <= 1440) {
            return '1 day';
        }

        $days = floor($minutes / (24 * 60)); // Calculate the number of days
        $remainingMinutes = $minutes % (24 * 60); // Calculate the remaining minutes
        $hours = floor($remainingMinutes / 60); // Calculate the number of hours
        $remainingMinutes = $remainingMinutes % 60; // Calculate the remaining minutes

        $result = "";

        if ($days > 0) {
            $result .= $days . ' day';
            if ($days > 1) {
                $result .= 's';
            }
            $result .= ' ';
        }

        if ($hours > 0) {
            $result .= $hours . ' hrs';
            $result .= ' ';
        }

        if ($remainingMinutes > 0) {
            $result .= $remainingMinutes . ' min';
            if ($remainingMinutes > 1) {
                $result .= 's';
            }
        }

        return $result;
    }
}
