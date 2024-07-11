<?php

namespace App\Enums;

enum KycTypeEnum: int
{
    case Pending = 1;
    case Approved = 2;
    case Reject = 3;
    case Reviewing = 4;
}
