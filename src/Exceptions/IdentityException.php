<?php

namespace AmlaCameroun\FPMatchSimple\Exceptions;

class IdentityException extends BaseException
{
    protected function setCode()
    {
        $this->code = BaseException::CODE_IDENTITY_EXCEPTION;
    }

}