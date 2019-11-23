<?php

namespace AmlaCameroun\FPMatchSimple\Core;

use AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException;
use GuzzleHttp\Client;

class FPServerAPI 
{
    protected const SYNCHRONIZE_URL = '/identity/add';
    protected const SYNCHRONIZE_MULTIPLES_URL = '/identity/add/many';
    protected const IDENTIFY_URL = '/identity/find?q=%s';
    protected const REMOVE_URL = '/identity/remove'; 
    protected const INTERN_AUTH_TOKEN_KEY = 'Intern-Auth-Token';

    /**
     * @var string
     */
    protected static $baseUrl;

    /**
     * @var string
     */
    protected static $internAuthTokenValue = '';

    /**
     * @var string
     */
    protected static $certPath;

    /**
     * @var float
     */
    protected static $timeOut = 10.0;

    /**
     * Synchronize identity with FP server.
     * 
     * !!! A tester
     *
     * @param \AmlaCameroun\FPMatchSimple\Core\Identity $identity
     * @return \AmlaCameroun\FPMatchSimple\Core\FPServerAPIResponseModel
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public static function synchronize(Identity $identity)
    {
        self::validateBaseUrl();

        $client = self::makeClient(['json' => $identity->toArray()]);
        $url = self::getUrl('synchronize');
        return  self::perform($client, $url, 'post');
    }

    /**
     * Synchronize multiples identities with FP server.
     * 
     * !!! A tester
     *
     * @param \AmlaCameroun\FPMatchSimple\Core\Identity[] $identities
     * @return \AmlaCameroun\FPMatchSimple\Core\FPServerAPIResponseModel
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public static function synchronizeMultiples(Identity ...$identities)
    {
        self::validateBaseUrl();

        $obj = ['identities' => []];
        foreach ($identities as $identity) array_push($obj['identities'], $identity->toArray());

        $client = self::makeClient(['json' => $obj]);
        $url = self::getUrl('synchronize_multiples');
        return  self::perform($client, $url, 'post');
    }

    /**
     * Forget identity
     * 
     * !!! A tester
     *
     * @param int $id
     * @return \AmlaCameroun\FPMatchSimple\Core\FPServerAPIResponseModel
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public static function forget(int $id)
    {
        self::validateBaseUrl();

        $client = self::makeClient(['json' => ['id' => $id]]);
        $url = self::getUrl('remove');
        return  self::perform($client, $url, 'post');
    }

    /**
     * Find User
     * 
     * !!! A tester
     * TODO: Changer le 404 en 200 en cas d'echec
     *
     * @param string $fp
     * @return \AmlaCameroun\FPMatchSimple\Core\FPServerAPIResponseModel
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public static function find(string $fp)
    {
        self::validateBaseUrl();
        if($fp == '') throw new FPServerAPIException('Fingerprint cannot be empty.');

        $client = self::makeClient();
        $url = sprintf(self::getUrl('identify'), urlencode($fp));
        return self::perform($client, $url);
    }

    /**
     * Configure FP Server API options.
     * 
     * @param array $options Associative array which can have keys:
     *  string 'base_url', string 'auth_key', float 'time_out' (in seconds), 
     *  string 'cert_path'. 'cert_path' options will be used for ssl 
     *  verification if set. The default time out is 10s
     * @return void
     */
    public static function setConfigs(array $options)
    {
        if (array_key_exists('base_url', $options)) self::setBaseUrl($options['base_url']);
        if (array_key_exists('auth_key', $options)) self::setAuthKey($options['auth_key']);
        if (array_key_exists('cert_path', $options)) self::setCertpath($options['cert_path']);
        if (array_key_exists('time_out', $options)) self::setTimeOut($options['time_out']);
    }

    /**
     * Set the base FP server URL
     * 
     * @param string $baseUrl
     * @return void
     */
    public static function setBaseUrl(string $baseUrl)
    {
        self::$baseUrl = $baseUrl;
    }

    /**
     * Set the FP server Authentication Key
     * 
     * @param string $baseUrl
     * @return void
     */
    public static function setAuthKey(string $baseUrl)
    {
        self::$baseUrl = $baseUrl;
    }

    /**
     * Set the ssl certificate path
     * 
     * @param string $path
     * @return void
     */
    public static function setCertpath(string $path)
    {
        self::$certPath = $path;
    }

    /**
     * Set the value of requests time out
     * 
     * @param  float  $timeOut time out in seconds
     * @return  void
     */ 
    public function setTimeOut(float $timeOut)
    {
        self::$timeOut = $timeOut;
    }

    /**
     * @return true
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     */
    protected static function validateBaseUrl()
    {
        if(!is_string(self::$baseUrl) || '' == self::$baseUrl) throw new FPServerAPIException('Invalid FP Server API base URL.');
    }

    /**
     * @param array $params
     * @return \GuzzleHttp\Client
     */
    protected static function makeClient(array $params = [])
    {
        $options = ['timeout'  => self::$timeOut];

        if(!empty(self::$certPath)) $options['verify'] = self::$certPath;

        foreach ($params as $key => $value) $options[$key] = $value;

        $options['headers'][self::INTERN_AUTH_TOKEN_KEY] = self::$internAuthTokenValue;

        return new Client($options);
    }

    /**
     * @return \AmlaCameroun\FPMatchSimple\Core\FPServerAPIResponseModel
     * @throws \GuzzleHttp\Exception\RequestException
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     */
    protected static function perform(Client $client, string $url, string $method = 'get')
    {
        $response = $client->$method($url);
        $response = trim((string)$response->getBody());
        return new FPServerAPIResponseModel($response);
    }

    /**
     * @param string $key
     * @return string
     */
    protected static function getUrl(string $key)
    {
        $url = self::$baseUrl;

        switch ($key) {
            case 'synchronize':
                $url .= self::SYNCHRONIZE_URL;
                break;
            case 'synchronize_multiples':
                $url .= self::SYNCHRONIZE_MULTIPLES_URL;
                break;
            case 'identify':
                $url .= self::IDENTIFY_URL;
                break;
            case 'remove':
                $url .= self::REMOVE_URL;
                break;
        }
        return $url;
    }
}