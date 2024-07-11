<?php

namespace App\Enums;

enum OrderStatusEnum: int
{
    case Cancelled = 7;
    case Delivered = 6;
    case On_Drop_Location = 5;
    case On_Way = 4;
    case On_Pickup_Location = 3;
    case Assigned = 2;
    case Received = 1;
    case Pending = 0;
}
