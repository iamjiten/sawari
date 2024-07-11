<?php

namespace App\Enums;

enum RentalOrderStatusEnum: int
{
    case Booked = 6;
    case Reject = 5;
    case Cancelled = 4;
    case Completed = 3;
    case Assigned = 2;
    case Received = 1;
    case Pending = 0;
}
