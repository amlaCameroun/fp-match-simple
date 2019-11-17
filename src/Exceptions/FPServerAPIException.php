<?php

namespace AmlaCameroun\FPMatchSimple\Exceptions;

class FPServerAPIException extends BaseException
{
    protected function setCode()
    {
        $this->code = BaseException::CODE_FP_SERVER_API_EXCEPTION;
    }
}