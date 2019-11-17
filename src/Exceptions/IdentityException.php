<?php

namespace FPMatchSimple\Exceptions;

use Exception;

class IdentityException extends Exception
{
    private const CODE = 10;

    public function __construct(string $message = '')
    {
        $this->code = self::CODE;
        $this->message = $message;
    }
}