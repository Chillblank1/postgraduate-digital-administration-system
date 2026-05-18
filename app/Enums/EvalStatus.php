<?php

namespace App\Enums;

enum EvalStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Finalised = 'finalised';
}
