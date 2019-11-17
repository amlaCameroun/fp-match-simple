<?php

namespace AmlaCameroun\FPMatchSimple\Core;

use AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException;
use GuzzleHttp\Client;

class FPServerAPI 
{
    // private const AUTH_TOKEN_KEY = 'Intern-Auth-Token';

    protected const SYNCHRONIZE_URL = '/fpmatch-simple/synchronize';
    protected const IDENTIFY_URL = '/fpmatch-simple/identity';

    /**
     * @var string
     */
    protected static $baseUrl;

    /**
     * @var string
     */
    protected static $certPath;

    /**
     * Synchronise identity with FP server.
     * 
     * TODO: Test $response to verify communication with FP Server
     *
     * @param \AmlaCameroun\FPMatchSimple\Core\Identity $identity
     * @return boolean true if synchronisation succeeds
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public static function synchronise(Identity $identity)
    {
        self::validateBaseUrl();

        $payload = ['identity' => $identity->toArray()];
        $payload = json_encode($payload);

        $options = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                // self::AUTH_TOKEN_KEY => self::AUTH_TOKEN_VALUE,
            ],
            'body' => $payload,
        ];
        $client = self::makeClient($options);
        $url = self::getUrl('synchronize');
        $response = self::perform($client, $url, 'post');

        return $response == 'OK';
    }

    /**
     * Get person $id.
     * 
     * TODO: Test $response to verify communication with FP Server
     *
     * @param string $fp
     * @return int|null Person ID
     * @throws \AmlaCameroun\FPMatchSimple\Exceptions\FPServerAPIException
     * @throws \GuzzleHttp\Exception\RequestException
     */
    public static function getPersonId(string $fp)
    {
        self::validateBaseUrl();
        if($fp == '') throw new FPServerAPIException('Fingerprint cannot be empty.');

        $options = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                // self::AUTH_TOKEN_KEY => self::AUTH_TOKEN_VALUE,
            ],
        ];
        $client = self::makeClient($options);
        $url = self::getUrl('identify');
        $url .= sprintf('?fp=%s', urlencode($fp));
        $response = self::perform($client, $url);

        return (substr($response, 0, 3) == 'id=') ? intval(str_replace('id=', '', $response)) : null;
    }

    /**
     * Configure FP Server API options.
     * 
     * @param array $options Associative array which can have keys:
     *  'base_url', 'cert_path'. cert_path options will be used for
     *  for ssl verification if set.
     * @return void
     */
    public static function setConfigs(array $options)
    {
        if (array_key_exists('base_url', $options)) self::setBaseUrl($options['base_url']);
        if (array_key_exists('cert_path', $options)) self::setCertpath($options['cert_path']);
    }

    /**
     * @param string $baseUrl
     * @return void
     */
    public static function setBaseUrl(string $baseUrl)
    {
        self::$baseUrl = $baseUrl;
    }

    /**
     * @param string $path
     * @return void
     */
    public static function setCertpath(string $path)
    {
        self::$certPath = $path;
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
        $options = [
            'timeout'  => 10.0,
        ];
        if(self::$certPath) $options['verify'] = self::$certPath;

        foreach ($params as $key => $value) $options[$key] = $value;

        return new Client($options);
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\RequestException
     */
    protected static function perform(Client $client, string $url, string $method = 'get')
    {
        $response = $client->$method($url);
        $response = trim((string)$response->getBody());
        return $response;
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
            case 'identify':
                $url .= self::IDENTIFY_URL;
                break;
        }
        return $url;
    }

}