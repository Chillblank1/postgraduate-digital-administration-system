<?php

namespace App\Enums;

enum EvaluatorType: string
{
    case Internal = 'internal';
    case External = 'external';
    case Supervisor = 'supervisor';
}
