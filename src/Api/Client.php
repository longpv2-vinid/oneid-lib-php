<?php

namespace OneId\Api;

use OneId\NonceManager\iNonceManager;
use OneId\NonceManager\RandomNonceManager;

class Client
{
    private $baseUrl;

    private $apiKey;
    private $privateKey;
    private $nonceManager;

    public function __construct($nonceManager=null, $apiKey=null, $privateKey=null, $baseUrl=null)
    {
        $this->setNonceManager($nonceManager);
        $this->setApiKey($apiKey);
        $this->setPrivateKey($privateKey);
        $this->setBaseUrl($baseUrl);
    }

    public function getApiKey()
    {
        if (is_null($this->apiKey)) {
            $this->apiKey = Utilities::readValueFromEnv("ONEID_API_KEY");
        }
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getPrivateKey()
    {
        if (is_null($this->privateKey)) {
            $this->privateKey = Utilities::readValueFromEnv("ONEID_PRIVATE_KEY");
        }
        return $this->privateKey;
    }

    public function setNonceManager($manager)
    {
        if (is_null($manager)) return;
        if (!($manager instanceof iNonceManager)) {
            trigger_error(sprintf("%s is not instance of %s\iNonceManager", $manager, __NAMESPACE__), E_USER_ERROR);
            return;
        }
        $this->nonceManager = $manager;
    }

    /**
     * @return iNonceManager
     */
    public function getNonceManager()
    {
        if (is_null($this->nonceManager)) {
            $this->nonceManager = new RandomNonceManager();
        }
        return $this->nonceManager;
    }

    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
    }

    public function getBaseUrl()
    {
        if (is_null($this->baseUrl)) {
            $baseUrl = Utilities::readValueFromEnv("ONEID_API_BASEURL");
            if (is_null($baseUrl)) {
                $oneIdEnv = strtolower(Utilities::readValueFromEnv("ONEID_ENV", "sandbox"));
                if ($oneIdEnv == "prod" || $oneIdEnv == "production") {
                    $baseUrl = API_BASEURL_PRODUCTION;
                } else {
                    $baseUrl = API_BASEURL_SANDBOX;
                }
            }
            $this->baseUrl = $baseUrl;
        }
        return $this->baseUrl;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getApiEndPoint($apiPath)
    {
        return $this->getBaseUrl().$apiPath;
    }

    /**
     * Make request to OneID's APIs
     *
     * @param string $method
     * @param string $url
     * @param mixed $body
     * @param string|null $nonce
     * @param string|null $timestamp
     * @return bool|string
     * @throws InvalidPrivateKeyException
     */
    function doRequest($method, $url, $body, $nonce=null, $timestamp=null)
    {
        if (is_null($nonce)) $nonce = $this->getNonceManager()->generateNonce();
        if (is_null($timestamp)) $timestamp = time();
        $apiKey = $this->getApiKey();
        $body = json_encode($body);
        $signature = Utilities::generateSignature($url, $method, $nonce, $timestamp, $apiKey, $body, $this->getPrivateKey());
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Key-Code: ' . $apiKey,
            'X-Nonce: ' . $nonce,
            'X-Timestamp: ' . $timestamp,
            'X-Signature: ' . $signature,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getApiEndPoint($url));
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if ($result === false) {
            trigger_error(curl_error($ch), E_USER_WARNING);
        }
        curl_close($ch);

        return $result;
    }

    /**
     * @return Client
     */
    static function defaultClient()
    {
        global $defaultClient;
        return $defaultClient;
    }
}

$defaultClient = new Client();