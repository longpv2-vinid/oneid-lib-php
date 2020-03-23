<?php

use OneId\Utilities;

use PHPUnit\Framework\TestCase;

class UtilitiesTest extends TestCase
{
    function generateSignatureDataProvider()
    {
        return [
            'succeed 01' => [
                "https://a.b",
                "POST",
                "12345678",
                123456789,
                "123456789@123abc",
                '{"a":2,"b":1}',
                TEST_PRIVATE_KEY,
                "XJNR40m9ReovfuIt+PU/mmRUnS6q4VYiqBrgovTjkQ17uINLeRD1pjoaNOaWfhhC/tv4iYtptLvLLg/WsVdreZ3l0ViOlgJIpzVRrTINGqE3PIh92Sj2i7Gm3yQQ3qG/iys+TRWHeQX8wukH/zmOPlWpoTLH4pK5f4uBM53A4GxZZOvll4hkAqxyEJ98VLzxDZnheAg/uI+Xu3rYg1tSVE7U0NxXvnB02tPJRf7cUvHDTZWay2LlMDL6Ff5iumLlHSY6V15pHJxS9dGhd5iW2KGLCROgX0ztLcQebGUA8s5GaKDTNnMdUycJKMfT4vfUKjzn08GWLPUlHEk/OrPIAQ=="
            ],
            'succeed 02' => [
                "https://a.b",
                "POST",
                "12345678",
                123456789,
                "123456789@123abd",
                '{"a":2,"b":1}',
                TEST_PRIVATE_KEY,
                "VZryPIDqGRPPBX0EoCy7C/bu7UmAc/i2csMcdj+lzFxS76xYVZjtI+4E9h/rkCXTkTw2YMkplHx3eZIplqbXE7qT+jpbLxt3SBCWoCmE2KJeyPEEo9eaYc05YsvlZrhn6rEbVXit//xE467txLLO+SO8jGR792Ym5oZktV0aQdThq+Nj8xfidO9FjDXKeC03xLAwrZbUMd0UEvDWIiFRoblLKjsrFNfbL5gLd4xp4nwdRcuzLZY26WA18SSjAPaV55zmgbEQPAD90V6X852WwGbcCHgtSbRB1clHAc0FxzPDf0ptQ4s8JNeIkwKCx09pYdxROzVEQg3aJt89KKmONQ=="
            ],
        ];
    }

    /**
     * @dataProvider generateSignatureDataProvider
     */
    public function testGenerateSignature_Succeed($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey, $expected)
    {
        $signature = Utilities::generateSignature($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey);
        $this->assertEquals($expected, $signature);
    }

    /**
     * @dataProvider generateSignatureDataProvider
     * @expectedException \OneId\InvalidPrivateKeyException
     */
    public function testGenerateSignature_WrongKey($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $privateKey, $expected)
    {
        $charPosToRemove = random_int(0, strlen($privateKey));
        $wrongPrivateKey = substr($privateKey, 0, $charPosToRemove) . substr($privateKey, $charPosToRemove+1);
        $this->expectException("OneId\InvalidPrivateKeyException");
        $signature = Utilities::generateSignature($url, $method, $nonce, $timestamp, $apiKey, $requestBody, $wrongPrivateKey);
    }
}
