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
     * Synchronize multiples Identities
     *
     * @param \AmlaCameroun\FPMatchSimple\Core\Identity[] $identities
     * @return float Task total execution time
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\IdentityException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public static function synchronizeMultiples(array $identities)
    {
        $response = FPServerAPI::synchronizeMultiples($identities);
        return $response->getTime();
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
        $response = FPServerAPI::synchronize($this);
        if ($response->getStatus() == FPServerAPIResponseModel::STATUS_OK) {
            $this->syncTime =  $response->getTime();
            return true;
        }
    }

    /**
     * Get user id whose figerprints matches the given value
     *
     * @param string $fp
     * @return \AmlaCameroun\FPMatchSimple\Core\SearchResultModel
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public static function find(string $fp)
    {
        return new SearchResultModel(FPServerAPI::find($fp));
    }

    /**
     * Clear FP database
     *
     * @return float Task total execution time
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public static function clearDB()
    {
        $response = FPServerAPI::clearDB();
        return $response->getTime();
    }

    /**
     * Forget the user whose ID is $id
     *
     * @param int $id
     * @return float Task total execution time
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public static function forget(int $id)
    {
        $response = FPServerAPI::forget($id);
        if ($response->getStatus() == FPServerAPIResponseModel::STATUS_OK) 
            return $response->getTime();
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

