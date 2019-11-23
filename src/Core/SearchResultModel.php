<?php

namespace AmlaCameroun\FPMatchSimple\Core;

class SearchResultModel 
{

    /**
     * @var int|null The user ID
     */
    protected $id;

    /**
     * @var int|null The matching percentage
     */
    protected $percentage;

    /**
     * @var float The request time in seconds
     */
    protected $time;

    public function __construct(FPServerAPIResponseModel $response)
    {

        $this->id = ($response->getStatus() == FPServerAPIResponseModel::STATUS_OK) ?  : null;
        if ($response->getStatus() == FPServerAPIResponseModel::STATUS_OK) {
            $this->id = $response->getData()['id'];
            $this->percentage = $response->getData()['percentage'];
        } else $this->id = null;
        $this->time = $response->getTime();
    }

    /**
     * Get the user ID
     *
     * @return  int|null
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the matching percentage
     *
     * @return  int|null
     */ 
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Get the request time in seconds
     *
     * @return  float
     */ 
    public function getTime()
    {
        return $this->time;
    }
}