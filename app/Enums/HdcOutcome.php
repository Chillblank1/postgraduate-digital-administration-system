<?php

namespace App\Enums;

enum HdcOutcome: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case RevisionsRequired = 'revisions_required';
    case Rejected = 'rejected';
    case Deferred = 'deferred';
}
