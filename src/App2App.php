<?php

namespace VinID;

class App2App extends Client
{
    public function __construct($isProduction = false)
    {
        parent::__construct($isProduction);
    }

    function createTransactionOrder()
    {
        $method = 'POST';
        $timestamp = time();
        $nonce = (string)$timestamp;

        $params = [
            'callback_url' => $this->callbackURL,
            'description' => $this->description,
            'extra_data' => $this->extraData,
            'order_amount' => $this->amount,
            'order_currency' => $this->currency,
            'order_reference_id' => $this->orderReferenceId,
            'pos_code' => $this->posCode,
            'service_type' => $this->serviceType,
            'store_code' => $this->storeCode,
            'user_id' => $this->userId
        ];
        $requestBody = json_encode($params);
        return $this->doRequest($this->apiKey, $method, $nonce, $timestamp, ENDPOINT_CREATE_ORDER, $requestBody);
    }

    function queryOrderStatus($orderId)
    {
        $url = ENDPOINT_QUERY_ORDER_STATUS . $orderId;
        $method = 'GET';
        $timestamp = time();
        $nonce = (string)$timestamp;

        return $this->doRequest($this->apiKey, $method, $nonce, $timestamp, $url, '');
    }
}