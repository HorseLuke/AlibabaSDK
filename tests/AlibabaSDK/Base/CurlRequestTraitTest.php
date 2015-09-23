<?php

namespace AlibabaSDK\Base;

/**
 * CurlRequestTraitTest测试
 * @author Horse Luke
 *
 */
class CurlRequestTraitTest extends \PHPUnit_Framework_TestCase{
    
    /**
     * 
     * @var AlibabaSDK\Base\CurlRequestTraitExtendMock
     */
    protected $mockCurlRequestTrait;
    
    const FOR_TEST_ONLY_VALUE_SIGNAL = '99999999999999999999999999';
    
    protected function setUp(){
        parent::setUp();
        $this->mockCurlRequestTrait = new CurlRequestTraitExtendMock();
    }

    public function testRawSend(){
        $url = 'http://gw.api.tbsandbox.com/router/rest';
        
        $response = $this->mockCurlRequestTrait->rawSend($url);
        
        if(!$response->isOk()){
            $this->fail(
                "RESPONSE_HAS_ERROR. "
                . "ERROR INFO:". var_export($response->getError(true), true). PHP_EOL
                . "RAW HTTP RETURN BODY:". PHP_EOL.  $response->getRawResult(). PHP_EOL
            );
        }
        
    }
    
    public function testRawSendPOST(){
        $url = 'http://gw.api.tbsandbox.com/router/rest';
    
        $response = $this->mockCurlRequestTrait->rawSend($url, array('test' => 1), 'POST');
    
        if(!$response->isOk()){
            $this->fail(
                "RESPONSE_HAS_ERROR. "
                . "ERROR INFO:". $response->getError(). PHP_EOL
                . "RAW HTTP RETURN BODY:". PHP_EOL.  $response->getRawResult(). PHP_EOL
            );
        }
        
    }
    
    public function testSetRequestLoggerByInterface(){
        
        $this->mockCurlRequestTrait->setRequestLogger('selfLogger', $this->mockCurlRequestTrait);
        
        $logger2 = new CurlRequestLoggerInterfaceExtendMock();
        $this->mockCurlRequestTrait->setRequestLogger('anotherLogger', $logger2);
        
        $url = 'http://gw.api.tbsandbox.com/router/rest';
        $response = $this->mockCurlRequestTrait->rawSend($url);
        
        $this->assertEquals($response->getRawResult(), $this->mockCurlRequestTrait->getTestLastResponseRawResult());
        $this->assertEquals($response->getRawResult(), $logger2->responseRawResult);
        
    }
    
    public function testDelRequestLogger(){
        $logger2 = new CurlRequestLoggerInterfaceExtendMock();
        
        $this->mockCurlRequestTrait->setRequestLogger('test2', $logger2);
        $this->mockCurlRequestTrait->delRequestLogger('test2');
        
    }
    
}