<?php

namespace AlibabaSDK\Base;

/**
 * Response基础测试
 * @author Horse Luke
 *
 */
class ResponseTest extends \PHPUnit_Framework_TestCase{
    
    public function testGetCode(){
        $response = new Response();
        $this->assertEquals(-1, $response->getCode());
        $this->assertFalse($response->isOk());
    }
    
    public function testCreate(){
        $code = 200;
        $rawResult = '111111111111111';
    
        $response = new Response();
        $response->create($code, $rawResult);
    
        $this->assertTrue($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
        $this->assertEquals($rawResult, $response->getResult());
    }
    
    public function testCreateAllowEmpty(){
        $code = 200;
        $rawResult = '';
    
        $response = new Response(array('allow_body_empty' => true));
        $response->create($code, $rawResult);
    
        $this->assertTrue($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
        $this->assertEquals($rawResult, $response->getResult());
        $this->assertTrue($response->getConfig('allow_body_empty'));
    
    }
    
    public function testCreateWithErrorEmptyBody(){
        $code = 200;
        $rawResult = '';
    
        $response = new Response();
        $response->create($code, $rawResult);
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('HTTP_BODY_EMPTY', $error);
    }
    
    public function testCreateWithHttpErrorCode(){
        $code = 403;
        $rawResult = 'OK';
    
        $response = new Response();
        $response->create($code, $rawResult);
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('HTTP_CODE_ERROR', $error);
    }
    
    
    public function testGetExtractInfo(){
        $response = new Response();
        $response->setExtractInfo(array('http_code' => 200));
        $this->assertArrayHasKey('http_code', $response->getExtractInfo());
    }
    
    public function testSetError(){
        $response = new Response();
        $response->setError("DEFAULT_ERROR", "DEFAULT_ERROR_DETAIL");
        $error = $response->getError(true);
        $this->assertEquals('DEFAULT_ERROR', $error['error']);
        $this->assertEquals('DEFAULT_ERROR_DETAIL', $error["errorDetail"]);
        
    }
    
}