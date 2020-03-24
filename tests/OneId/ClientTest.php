<?php

namespace OneId;
use OneId\Client;
use OneId\NonceManager\RandomNonceManager;
use PHPUnit\Framework\TestCase;

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
        $val1 = new NonceManager\RandomNonceManager();
        $val2 =  new NonceManager\RandomNonceManager();
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
            array('POST', API_ENDPOINT_TRANSACTION_QR, array(), 200),
        ];
    }

    /**
     * @dataProvider dataProvider_doRequest
     */
    public function testDoRequest_Success($method, $url, $body, $expected)
    {
        $client = new Client();
        $client->setPrivateKey(TEST_PRIVATE_KEY);
        $client->setApiKey(TEST_API_KEY);
        $client->setBaseUrl(API_BASEURL_SANDBOX);

        $rv = $client->doRequest($method, $url, $body);
        $this->assertEquals($expected, $rv);
    }
}
