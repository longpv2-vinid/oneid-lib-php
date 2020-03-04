<?php

namespace VinID;

use App2App\Order;

class App2App {
    const VERSION = 'v1';

    private static $callbackUrl;
    private static $serviceType = 'PURCHASE';
    private static $currencyCode = 'VND';

    public function setCallbackURL($url)
    {
        self::$callbackUrl = $url;
    }

    public function setServiceType($type)
    {
        self::$serviceType = $type;
    }

    public function setCurrencyCode($curCode)
    {
        self::$currencyCode = $curCode;
    }

    public function createTransactionOrder($storeCode, $posCode, $userId, $amount, $description, $order_ref = '', $extra_data = '') {
        $url = '/merchant-integration/v1/qr/create-transaction-order';
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
            'store_code' => $storeCode,                
            'user_id' => $userId
        ];
        $requestBody = json_encode($params);
        $apiKey = \VinID\VinID::getApiKey();
        $privateKey = \VinID\VinID::getPrivateKey();
        $sign = \VinID\Signature::generate($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey);
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Key-Code: '.$apiKey,
            'X-Nonce: '.$nonce,
            'X-Timestamp: '.$timestamp,
            'X-Signature: '.$sign,
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

        echo $result.PHP_EOL;
        $obj = json_decode($result);
        return new \VinID\Order($obj->meta, $obj->data);
    }

    public function queryOrderStatus($orderId) {
        $url = '/merchant-integration/v1/qr/query/'.$orderId;
        $method = 'GET';
        $timestamp = time();
        $nonce = (string)$timestamp;

        $apiKey = \VinID\VinID::getApiKey();
        $privateKey = \VinID\VinID::getPrivateKey();
        $sign = \VinID\Signature::generate($url, $method, $nonce, $timestamp, $apiKey, '', $privateKey);
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Key-Code: '.$apiKey,
            'X-Nonce: '.$nonce,
            'X-Timestamp: '.$timestamp,
            'X-Signature: '.$sign,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, \VinID\VinID::getHost().$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }
}