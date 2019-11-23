<?php

namespace AmlaCameroun\FPMatchSimple\Core;

use AmlaCameroun\FPMatchSimple\Exceptions\IdentityException;

class Identity 
{
    /**
     * Person ID
     *
     * @var int
     */
    protected $id;

    /**
     * Synchronisation time in seconds
     *
     * @var float
     */
    protected $syncTime;

    /**
     * Person fingerprints.
     *
     * @var array
     */
    protected $fps;

    /**
     * @param integer $id Person ID
     * @param array $fps Person fingerprints
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\IdentityException
     */
    public function __construct(int $id, array $fps)
    {
        $this->id = $id;
        $this->fps = $fps;

        $this->validateId();
        $this->validateFPs();
    }

    /**
     * Synchronize identity with FP server.
     *
     * @return true
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public function synchronize()
    {
        $this->syncTime =  FPServerAPI::synchronise($this);
        return true;
    }

    /**
     * Validates ID.
     *
     * @return true
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\IdentityException
     */
    protected function validateId()
    {
        if (!is_int($this->id) || $this->id === 0) throw new IdentityException('Invalid ID.');

        return true;
    }

    /**
     * Validates figerprints.
     *
     * @return true
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\IdentityException
     */
    protected function validateFPs()
    {
        $fps = $this->fps;
        $tab = [];
        foreach($this->fps as $fp) 
            if(is_string($fp) && !empty(trim($fp))) 
                array_push($tab, $fp);

        if (empty($tab)) throw new IdentityException('Invalid fingerprints.');

        $this->fps = $tab;
        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'fps' => $this->fps,
        ];
    }

    /**
     * Get synchronisation time in seconds
     *
     * @return  float
     */ 
    public function getSyncTime()
    {
        return $this->syncTime;
    }
}

