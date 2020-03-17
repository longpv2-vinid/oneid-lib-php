<?php

namespace VinID;

class TransactionQR extends Client
{
    public function __construct($isProduction = false)
    {
        parent::__construct($isProduction);
    }

    function generateTransactionQR()
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
            'store_code' => $this->storeCode
        ];
        $requestBody = json_encode($params);
        return $this->doRequest($this->apiKey, $method, $nonce, $timestamp, ENDPOINT_TRANSACTION_QR, $requestBody);
    }
}