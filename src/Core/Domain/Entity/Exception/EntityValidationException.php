<?php

namespace Core\Domain\Entity\Exception;

use Exception;
use Throwable;

class EntityValidationException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}