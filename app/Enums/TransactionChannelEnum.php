<?php

namespace App\Enums;

enum TransactionChannelEnum: int
{
    case Fone_Pay = 4;
    case Connect_IPS = 3;
    case Khalti = 2;
    case Esewa = 1;
    case Manual = 0;
}
