<?php

namespace AmlaCameroun\FPMatchSimple\Core;

use AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException;

class FPServerAPIResponseModel 
{

    public const STATUS_OK = "OK";
    public const STATUS_NOT_FOUND = "NOT_FOUND";
    // public const STATUS_INVALID_TOKEN = "INVALID_TOKEN";

    /**
     * @var string Response status
     */
    protected $status;

    /**
     * @var float Time in seconds
     */
    protected $time;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param string $str
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     */
    public function __construct(string $str)
    {
        $responseJson = json_decode(trim($str), true);
        if ($responseJson === null) 
            throw new FPServerAPIException('The response is not JSON formatted: ' . $str);
        if (!(array_key_exists('status', $responseJson) && array_key_exists('time', $responseJson)))
            throw new FPServerAPIException('Unsupported response format.' . $str);
        
        $this->status = $responseJson['status'];
        $this->time = $responseJson['time'];
        if(array_key_exists('data', $responseJson)) $this->data = $responseJson['data'];
    }

    

    /**
     * Get response status
     *
     * @return  string
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get time in seconds
     *
     * @return  float
     */ 
    public function getTime()
    {
        return $this->time;
    }
}