<?php

namespace App\Enums;

enum UserTypeEnum: int
{
    case Customer = 1;
    case Rider = 2;
    case Admin = 3;
}
