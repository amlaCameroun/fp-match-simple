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
     * Synchronise identity with FP server.
     *
     * @return bool Whether or not synchronisation succeeds.
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     */
    public function synchronize()
    {
        return FPServerAPI::synchronise($this);
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
}

