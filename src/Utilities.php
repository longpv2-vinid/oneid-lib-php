<?php

namespace OneId;

use Throwable;

class InvalidPrivateKeyException extends \Exception
{
    public $privateKey;

    public function __construct($privateKey)
    {
        $this->privateKey = $privateKey;
        parent::__construct("Can not load private key");
    }
}


class Utilities
{
    /**
     * @todo -o Long please comment here
     * @param string $url
     * @param string $method POST or GET
     * @param string $nonce
     * @param int $timestamp
     * @param string $apiKey
     * @param string $requestBody
     * @param string $privateKey PEM formation for RSA private key
     * @return string the generated signature. Empty string if can not generate signature
     * @throws InvalidPrivateKeyException
     */
    static function GenerateSignature($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey)
    {
        $data = $url . ";" . $method . ";" . $nonce . ";" . $timestamp . ";" . $apiKey . ";" . $requestBody;
        $p = openssl_pkey_get_private($privateKey);
        if (!$p) {
            throw new InvalidPrivateKeyException($privateKey);
        }
        $signSuccess = openssl_sign($data, $signature, $p, OPENSSL_ALGO_SHA256);
        $encodedSignature = base64_encode($signature);
        openssl_free_key($p);
        return $encodedSignature;
    }
}
