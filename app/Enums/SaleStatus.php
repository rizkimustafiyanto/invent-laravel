<?php

namespace App\Enums;

enum SaleStatus: string
{
    case PAID = 'PAID';
    case UNPAID = 'UNPAID';
}
