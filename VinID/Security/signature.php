<?php

namespace VinID\Security;

class Signature {
    public static function generate($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey)
    {
        $data = $url.";".$method.";".$nonce.";".$timestamp.";".$apiKey.";".$requestBody;
        print_r($data . PHP_EOL);
        try {
            $p = openssl_pkey_get_private($privateKey);
            $signSuccess = openssl_sign($data, $signature, $p, OPENSSL_ALGO_SHA256);
            if (!$signSuccess) {
                print("False");
                return "";
            }
            $encodedSignature = base64_encode($signature);
            print($encodedSignature.PHP_EOL);
            openssl_free_key($p);
            return $encodedSignature;
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }
}