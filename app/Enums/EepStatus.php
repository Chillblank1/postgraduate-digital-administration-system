<?php

namespace App\Enums;

enum EepStatus: string
{
    case Pending = 'pending';
    case AcceptedByFpgc = 'accepted_by_fpgc';
    case RejectedByFpgc = 'rejected_by_fpgc';
}
