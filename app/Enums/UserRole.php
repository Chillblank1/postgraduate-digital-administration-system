<?php

namespace App\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Supervisor = 'supervisor';
    case Hod = 'hod';
    case InternalEvaluator = 'internal_evaluator';
    case ExternalEvaluator = 'external_evaluator';
    case FpgcR = 'fpgc_r';
    case Fpgc = 'fpgc';
    case Admin = 'admin';
}
