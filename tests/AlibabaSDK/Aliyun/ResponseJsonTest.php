<?php

namespace AlibabaSDK\Aliyun;

/**
 * ResponseJson基础测试
 * @author Horse Luke
 *
 */
class ResponseJsonTest extends \PHPUnit_Framework_TestCase{
    
    public function testCreate(){
        $code = 200;
        $rawResult = '{"RequestId": "4C467B38-3910-447D-87BC-AC049166F216"}';
        
        $response = new ResponseJson();
        $response->create($code, $rawResult);
        
        $this->assertTrue($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
        
        $result = $response->getResult();
        $this->assertArrayHasKey('RequestId', $result);
    }
    
    public function testCreateWithError(){
        $code = 403;
        $rawResult = '{"RequestId": "8906582E-6722-409A-A6C4-0E7863B733A5","HostId": "ecs.aliyuncs.com","Code": "UnsupportedOperation", "Message": "The specified action is not supported."}';
        
        $response = new ResponseJson();
        $response->create($code, $rawResult);
        
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('API_RETURN_ERROR_CODE', $error);
    }
    
    public function testCreateWithErrorJSONError(){
        $code = 200;
        $rawResult = 'callback({"code":400,"msg":"11","request_id":"22"})';
    
        $response = new ResponseJson();
        $response->create($code, $rawResult);
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('PARSE_ERROR_RESPONSE_JSON', $error);
    }
    
}