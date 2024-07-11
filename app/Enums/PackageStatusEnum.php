<?php

namespace App\Enums;

enum PackageStatusEnum: int
{
    case Delivered = 3;
    case Processing = 2;
    case Pending = 1;
}
