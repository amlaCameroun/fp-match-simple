<?php

namespace FPMatchSimple\Core;

use FPMatchSimple\Exceptions\IdentityException;

class Identity 
{
    /**
     * Person ID
     *
     * @var int
     */
    protected $id;

    /**
     * Person fingerprints
     *
     * @var array
     */
    protected $fps;

    /**
     * Undocumented function
     *
     * @param integer $id Person ID
     * @param array $fps Person fingerprints
     */
    public function __construct(int $id, array $fps)
    {
        $this->id = $id;
        $this->fps = $fps;

        $this->validateId();
        $this->validateFPs();
    }

    /**
     * Validates ID
     *
     * @return bool
     * @throws IdentityException
     */
    protected function validateId()
    {
        if (!is_int($this->id) || $this->id === 0) throw new IdentityException('INVALID ID');

        return true;
    }

    /**
     * Validates figerprints
     *
     * @return bool
     * @throws IdentityException
     */
    protected function validateFPs()
    {
        $fps = $this->fps;
        $tab = [];
        foreach($this->fps as $fp) 
            if(is_string($fp) && !empty(trim($fp))) 
                array_push($tab, $fp);

        if (empty($tab)) throw new IdentityException('INVALID FINGERPRINTS');

        $this->fps = $tab;
        return true;
    }
}

