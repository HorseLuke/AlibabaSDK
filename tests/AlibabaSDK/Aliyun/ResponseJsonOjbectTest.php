<?php

namespace AlibabaSDK\Aliyun;

/**
 * Response基础测试
 * @author Horse Luke
 *
 */
class ResponseJsonObjectTest extends \PHPUnit_Framework_TestCase{
    
    protected function setUp(){
        parent::setUp();
    }
    
    public function testCreate(){
        $code = 200;
        $rawResult = '{"RequestId": "4C467B38-3910-447D-87BC-AC049166F216"}';
        
        $response = new ResponseJsonObject();
        $response->create($code, $rawResult);
        
        $this->assertTrue($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
        
        $result = $response->getResult();
        $this->assertObjectHasAttribute('RequestId', $result);
        $this->assertObjectNotHasAttribute('Code', $result);
    }
    
    
    public function testCreateWithError(){
        $code = 403;
        $rawResult = '{"RequestId": "8906582E-6722-409A-A6C4-0E7863B733A5","HostId": "ecs.aliyuncs.com","Code": "UnsupportedOperation", "Message": "The specified action is not supported."}';
        
        $response = new ResponseJsonObject();
        $response->create($code, $rawResult);
        
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('API_RETURN_ERROR_CODE', $error);
    }
    
    public function testCreateWithErrorJSONError(){
        $code = 200;
        $rawResult = 'callback({"code":400,"msg":"11","request_id":"22"})';
    
        $response = new ResponseJsonObject();
        
        $response->create($code, $rawResult);
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('PARSE_ERROR_RESPONSE_JSON', $error);
    }
    
}