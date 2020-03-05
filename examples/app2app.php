<?php

require '../init.php';

$apiCode = 'Your-api-code-here';
$private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
-----END RSA PRIVATE KEY-----
EOD;

$appClient = new \VinID\App2App();

\VinID\VinID::setApiKey($apiCode);
\VinID\VinID::setPrivateKey($private_key);

$order = $appClient->createTransactionOrder('your-store-code', 'your-pos-code','user-phone-number', 10000, 'Test App 2 App');
echo $order->getOrderID() . PHP_EOL;
// Since this example use time() as Nonce, we should wait some seconds before continue request to server.
sleep(5);
$result = $appClient->queryOrderStatus($order->getOrderID());
echo $result;