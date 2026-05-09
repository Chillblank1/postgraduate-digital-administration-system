<?php

namespace App\Exceptions;

use RuntimeException;

class WorkflowException extends RuntimeException
{
    public static function transitionNotAllowed(string $reason): self
    {
        return new self($reason);
    }
}
