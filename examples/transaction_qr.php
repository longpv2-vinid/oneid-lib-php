<?php

require '../init.php';

$apiCode = 'Your api / keycode here';
$private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
-----END RSA PRIVATE KEY-----
EOD;

\VinID\VinID::setApiKey($apiCode);
\VinID\VinID::setPrivateKey($private_key);

\VinID\TransactionQR\TransactionQR::generateQR('SBLONGPV2', 'SBLONGPV2', 10000, 'Test trans QR');