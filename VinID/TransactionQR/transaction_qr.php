<?php

namespace VinID\TransactionQR;

class TransactionQR {
    const VERSION = 'v2';

    public static $callbackUrl;
    public static $serviceType = 'PURCHASE';
    public static $currencyCode = 'VND';

    public static function setCallbackURL($url)
    {
        self::$callbackUrl = $url;
    }

    public static function setServiceType($type)
    {
        self::$serviceType = $type;
    }

    public static function setCurrencyCode($curCode)
    {
        self::$currencyCode = $curCode;
    }

    public static function generateQR($storeCode, $posCode, $amount, $description, $order_ref = '', $extra_data = '') {
        $url = '/merchant-integration/v1/qr/gen-transaction-qr';
        $method = 'POST';
        $timestamp = time();
        $nonce = (string)$timestamp;

        $params = [
            'callback_url' => self::$callbackUrl,
            'description' => $description,
            'extra_data' => $extra_data,
            'order_amount' => $amount,
            'order_currency' => self::$currencyCode,
            'order_reference_id' => $order_ref,
            'pos_code' => $posCode,
            'service_type' => self::$serviceType,
            'store_code' => $storeCode
        ];
        $requestBody = json_encode($params);
        $apiKey = \VinID\VinID::getApiKey();
        $privateKey = \VinID\VinID::getPrivateKey();
        $sign = \VinID\Security\Signature::generate($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey);
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Key-Code: '.$apiKey,
            'X-Nonce: '.$nonce,
            'X-Timestamp: '.$timestamp,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, \VinID\VinID::getHost().$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);

        print_r($result);
        return $result;
    }
}