<?php

namespace AmlaCameroun\FPMatchSimple\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    protected const CODE_IDENTITY_EXCEPTION = 10;
    protected const CODE_FP_SERVER_API_EXCEPTION = 11;

    public function __construct(string $message = '')
    {
        $this->setCode();
        $this->message = $message;
    }

    protected abstract function setCode();
}