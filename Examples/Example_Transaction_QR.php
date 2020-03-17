<?php

use VinID\TransactionQR;

require '../init.php';

$apiCode = 'YOUR-API-KEY';
$private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
YOUR PRIVATE KEY HERE
-----END RSA PRIVATE KEY-----
EOD;

$isUsingProductionEnv = false;
$client = new TransactionQR($isUsingProductionEnv);

// set required values
$client->setApiKey($apiCode);
$client->setPrivateKey($private_key);
$client->setStoreCode('YOUR-STORE-CODE');
$client->setPosCode('YOUR-POS-CODE');
$client->setAmount(10000);

// optional
$client->setDescription('Test Trans QR');
$client->setCallbackURL('https://your-api-endpoint-here.com');
$client->setCurrency('VND');
$client->setExtraData('YOUR-PARTNER-CODE', '[{"item_topping":"topping something","note":""},{"item_topping":"foo","note":"bar"}]');
$client->setOrderReferenceId('your-system-order-id-here');

// generate Transaction QR
$jsonQR = $client->generateTransactionQR();
echo $jsonQR;