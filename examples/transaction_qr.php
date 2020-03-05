<?php

require '../init.php';

$apiCode = 'Your-api-code-here';
$private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
-----END RSA PRIVATE KEY-----
EOD;

$transtionQR = new \VinID\TransactionQR();

\VinID\VinID::setApiKey($apiCode);
\VinID\VinID::setPrivateKey($private_key);

$jsonQR = $transtionQR->generateQR('your-store-code', 'your-pos-code', 10000, 'Test trans QR');
echo $jsonQR;