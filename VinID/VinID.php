<?php

namespace VinID;

class VinID
{
    /** API key generated from VinID merchant site */
    public static $apiKey;

    /** Private key generated from VinID merchant site */
    public static $privateKey;

    /** Enviroment flag */
    public static $isProduction = false;

    /** Sandbox Host */
    public static $sandboxHost = 'https://api-merchant-sandbox.vinid.dev';
    
    /** Production Host */
    public static $prodHost = 'https://api-merchant.vinid.net';

    /**
     * @return string the API key used for requests
     */
    public static function getApiKey()
    {
        return self::$apiKey;
    }

    /**
     * Sets the API key to be used for requests.
     *
     * @param string $apiKey
     */
    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    /**
     * @return string the private key used for Connect requests
     */
    public static function getPrivateKey()
    {
        return self::$privateKey;
    }

    /**
     * Sets the private key to be used for Connect requests.
     *
     * @param string $privateKey
     */
    public static function setPrivateKey($privateKey)
    {
        self::$privateKey = $privateKey;
    }

    /**
     * @return string current enviroment flag
     */
    public static function getHost()
    {
        if (self::$isProduction) { // PRODUCTION
            return self::$prodHost;
        }
        return self::$sandboxHost; // default is SANDBOX enviroment
    }

    /**
     * Sets enviroment to using PRODUCTION
     */
    public static function usingProductionEnviroment()
    {
        self::$isProduction = true;
    }
}