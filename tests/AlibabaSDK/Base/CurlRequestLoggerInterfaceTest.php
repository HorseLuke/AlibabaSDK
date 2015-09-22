<?php

namespace AlibabaSDK\Base;

class CurlRequestLoggerInterfaceTest extends \PHPUnit_Framework_TestCase{

    /**
     *
     * @var \AlibabaSDK\Base\CurlRequestLoggerInterfaceExtendMock
     */
    protected $mockTest;
    
    protected function setUp(){
        parent::setUp();
        $this->mockTest = new CurlRequestLoggerInterfaceExtendMock();
    }
    
    public function testReceiveSignalRequestLogger(){
        $result = 'phpunit_'. mt_rand();
        $response = new Response();
        $response->create(200, $result);
        $this->mockTest->receiveSignalRequestLogger("http://notexistdomain.com", null, 'GET', $response);
        $this->assertEquals($result, $this->mockTest->responseRawResult);
    }
    
}