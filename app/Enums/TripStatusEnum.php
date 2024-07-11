<?php

namespace App\Enums;

enum TripStatusEnum:int
{
    case Assigned = 0;
    case Completed = 1;
    case Cancelled = 2;
}
