<?php

namespace App\Enums;

enum TransactionStatusEnum: int
{
    case Completed = 4;
    case Refunded = 3;
    case Failed = 2;
    case Ambiguous = 1;
    case Pending = 0;
}
