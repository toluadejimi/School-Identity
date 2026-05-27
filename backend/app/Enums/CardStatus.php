<?php

namespace App\Enums;

enum CardStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Lost = 'lost';
    case Replaced = 'replaced';
}
