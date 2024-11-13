<?php

namespace App\Enums;

use App\Traits\EnumHelpers;

enum TicketStatus: string
{
    use EnumHelpers;

    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case CLOSED = 'closed';
}
