<?php

namespace VinID;

use Credential\Signature;
use Exception;
use stdClass;

const SANDBOX_URL = 'https://api-merchant-sandbox.vinid.dev';
const PRODUCTION_URL = 'https://api-merchant.vinid.net';
const ENDPOINT_TRANSACTION_QR = '/merchant-integration/v1/qr/gen-transaction-qr';
const ENDPOINT_CREATE_ORDER = '/merchant-integration/v1/qr/create-transaction-order';
const ENDPOINT_QUERY_ORDER_STATUS = '/merchant-integration/v1/qr/query/';

abstract class Client
{
    public $isProductionEnvironment = false;
    protected $executeHost;

    protected $apiKey;
    protected $privateKey;

    protected $callbackURL;
    protected $description;
    protected $extraData;
    protected $amount;
    protected $currency = 'VND';
    protected $orderReferenceId;
    protected $posCode;
    protected $serviceType = 'PURCHASE';
    protected $storeCode;
    protected $userId;

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function getServiceType()
    {
        return $this->serviceType;
    }

    public function setServiceType($serviceType)
    {
        $this->serviceType = $serviceType;
        return $this;
    }

    public function getCallbackURL()
    {
        return $this->callbackURL;
    }

    public function setCallbackURL($callbackURL)
    {
        $this->callbackURL = $callbackURL;
        return $this;
    }

    public function getExtraData()
    {
        return $this->extraData;
    }

    public function setExtraData($partnerCode, $extraData)
    {
        if (strlen($partnerCode) == 0) {
            throw new Exception('[VinID] Merchant Code cannot be null!');
        }
        json_decode($extraData);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('[VinID] ExtraData should be in JSON format!');
        }
        $this->extraData = new stdClass();
        $this->extraData->partner_code = $partnerCode;
        $this->extraData->order_info = $extraData;
        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    public function getOrderReferenceId()
    {
        return $this->orderReferenceId;
    }

    public function setOrderReferenceId($orderReferenceId)
    {
        $this->orderReferenceId = $orderReferenceId;
        return $this;
    }

    public function getStoreCode()
    {
        return $this->storeCode;
    }

    public function setStoreCode($storeCode)
    {
        $this->storeCode = $storeCode;
        return $this;
    }

    public function getPosCode()
    {
        return $this->posCode;
    }

    public function setPosCode($posCode)
    {
        $this->posCode = $posCode;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    public function __construct($isProduction = false)
    {
        if ($isProduction) {
            $this->executeHost = PRODUCTION_URL;
        } else {
            $this->executeHost = SANDBOX_URL;
        }
        $this->isProductionEnvironment = $isProduction;
    }

//    abstract function generateTransactionQR();
//    abstract function createTransactionOrder();
//    abstract function queryOrderStatus($orderId);

    protected function doRequest($apiKey, $method, $nonce, $timestamp, $url, $body)
    {
        $sign = Signature::generate($url, $method, $nonce, $timestamp, $this->apiKey, $body, $this->privateKey);
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Key-Code: ' . $apiKey,
            'X-Nonce: ' . $nonce,
            'X-Timestamp: ' . $timestamp,
            'X-Signature: ' . $sign,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->executeHost . $url);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}