<?php

namespace App\Enums;

enum WorkflowExecutionStatus: string
{
    case Success = 'success';
    case Failed = 'failed';
    case Skipped = 'skipped';
}
