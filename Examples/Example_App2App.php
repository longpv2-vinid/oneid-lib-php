<?php

require '../init.php';

$apiCode = 'YOUR-API-KEY-HERE';
$private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
YOUR PRIVATE KEY HERE
-----END RSA PRIVATE KEY-----
EOD;


$isUsingProductionEnv = false;
$client = new \VinID\App2App($isUsingProductionEnv);

// set required values
$client->setApiKey($apiCode);
$client->setPrivateKey($private_key);
$client->setStoreCode('YOUR-STORE-CODE');
$client->setPosCode('YOUR-POS-CODE');
$client->setAmount(10000);

// optional
$client->setUserId('your-system-user-id-here');
$client->setDescription('Test Create Order');
$client->setCallbackURL('https://your-api-endpoint-here.com');
$client->setCurrency('VND');
$client->setExtraData('YOUR-PARTNER-CODE', '[{"item_topping":"bar something","note":"foo"},{"item_topping":"foo","note":"bar"}]');
$client->setOrderReferenceId('your-system-order-id-here');

// generate Transaction QR
$jsonOrder = $client->createTransactionOrder();
echo $jsonOrder . PHP_EOL;
// Since this example use time() as Nonce, we should wait some seconds before continue request to server.
sleep(5);
// Get OrderID from previous request
$order = json_decode($jsonOrder);
echo $order->data->order_id . PHP_EOL;
$result = $client->queryOrderStatus($order->data->order_id);
echo $result;