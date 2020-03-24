<?php

namespace OneId\Api;

use OneId\Api\NonceManager\RandomNonceManager;
use OneId\Utilities;
use PHPUnit\Framework\TestCase;
use const OneId\API_BASEURL_SANDBOX;
use const OneId\API_ENDPOINT_TRANSACTION_QR;
use const OneId\TEST_API_KEY;
use const OneId\TEST_PRIVATE_KEY;

class ClientTest extends TestCase
{
    public function testDefaultClient()
    {
        global $defaultClient;
        $this->assertEquals($defaultClient, Client::defaultClient());
    }

    protected function _testGetterSetter_WithEnv($getter, $setter, $envVar)
    {
        $val1 = Utilities::generateGUID4();
        $val2 = Utilities::generateGUID4();
        $client = new Client();

        putenv($envVar."=".$val1);
        $this->assertEquals($val1, $client->{$getter}());

        $client->{$setter}($val2);
        $this->assertEquals($val2, $client->{$getter}());

        $client->{$setter}($val1);
        $this->assertEquals($val1, $client->{$getter}());
    }

    protected function _testGetterSetter($getter, $setter)
    {
        $val1 = Utilities::generateGUID4();
        $val2 = Utilities::generateGUID4();
        $client = new Client();

        $client->{$setter}($val2);
        $this->assertEquals($val2, $client->{$getter}());

        $client->{$setter}($val1);
        $this->assertEquals($val1, $client->{$getter}());
    }

    public function testGetSetNonceManager()
    {
        $val1 = new RandomNonceManager();
        $val2 =  new RandomNonceManager();
        $client = new Client();

        $client->setNonceManager($val2);
        $this->assertEquals($val2, $client->getNonceManager());

        $client->setNonceManager($val1);
        $this->assertEquals($val1, $client->getNonceManager());

        $this->expectError();
        $this->expectErrorMessageMatches('/iNonceManager/');
        $wrong = 'abc';
        $client->setNonceManager($wrong);

    }

    public function testGetApiEndPoint()
    {
        $client = new Client();

        $client->setBaseUrl('/a/b/c');
        $this->assertEquals('/a/b/c/1234', $client->getApiEndPoint('/1234'));
    }

    public function testGetSetApiKey()
    {
        $this->_testGetterSetter_WithEnv('getApiKey', 'setApiKey', 'ONEID_API_KEY');
    }

    public function testGetSetPrivateKey()
    {
        $this->_testGetterSetter_WithEnv('getPrivateKey', 'setPrivateKey', 'ONEID_PRIVATE_KEY');
    }

    public function testGetSetBaseUrl()
    {
        $this->_testGetterSetter_WithEnv('getBaseUrl', 'setBaseUrl', 'ONEID_API_BASEURL');
    }

    function dataProvider_doRequest()
    {
        return [
            array(
                'POST',
                API_ENDPOINT_TRANSACTION_QR,
                array("a"=>1234),
                array(
                    'headers'=>[
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'X-Key-Code' => null,
                        'X-Nonce' => null,
                        'X-Timestamp' => null,
                        'X-Signature' => null,
                    ],
                    "body"=>'{"a":1234}',
                )),
        ];
    }

    /**
     * @dataProvider dataProvider_doRequest
     * @throws \OneId\InvalidPrivateKeyException
     */
    public function testPrepareRequest($method, $url, $body, $expected)
    {
        $client = new Client();
        $client->setPrivateKey(TEST_PRIVATE_KEY);
        $client->setApiKey(TEST_API_KEY);
        $client->setBaseUrl(API_BASEURL_SANDBOX);

        $req = $client->prepareRequest($method, $url, $body);

        $expectedHeaders = $expected['headers'];
        $realHeaders = $req->getHeaders();
        foreach ($expectedHeaders as $key => $val) {
            if (is_null($val)) $this->assertArrayHasKey($key, $realHeaders);
            else $this->assertEquals($val, $realHeaders[$key]);
        }
        $this->assertIsInt($realHeaders['X-Timestamp']);
        $this->assertIsString($realHeaders['X-Key-Code']);
        $this->assertIsString($realHeaders['X-Signature']);

        $expectedSignature = Utilities::generateSignature(
            $req->url,
            $req->method,
            $req->nonce,
            $req->timestamp,
            $req->apiKey,
            $req->getEncodedBody(),
            $client->getPrivateKey(),
        );
        $this->assertEquals($expectedSignature, $realHeaders['X-Signature']);

        $this->assertEquals($expected['body'], $req->getEncodedBody());
    }
}
