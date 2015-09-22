<?php

namespace AlibabaSDK\Aliyun;

/**
 * ResponseXmlObject基础测试
 * @author Horse Luke
 *
 */
class ResponseXmlObjectTest extends \PHPUnit_Framework_TestCase{
    
    protected function setUp(){
        parent::setUp();
    }
    
    public function testCreate(){
        $code = 200;
        $rawResult = '<?xml version="1.0" encoding="UTF-8"?> <APINAMEResponse><RequestId>4C467B38-3910-447D-87BC-AC049166F216</RequestId></APINAMEResponse>';
        
        $response = new ResponseXmlObject();
        $response->create($code, $rawResult);
        
        $this->assertTrue($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
        
        $result = $response->getResult();
        $this->assertObjectHasAttribute('RequestId', $result);
        $this->assertObjectNotHasAttribute('Code', $result);
    }
    
    
    public function testCreateWithError(){
        $code = 403;
        $rawResult = '<?xml version="1.0" encoding="UTF-8"?><Error><RequestId>8906582E-6722-409A-A6C4-0E7863B733A5</RequestId><HostId>ecs.aliyuncs.com</HostId><Code>UnsupportedOperation</Code><Message>The specified action is not supported.</Message></Error>';
        
        $response = new ResponseXmlObject();
        $response->create($code, $rawResult);
        
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('API_RETURN_ERROR_CODE', $error);
    }
    
    public function testCreateWithErrorXMLError(){
        $code = 200;
        $rawResult = 'callback({"code":400,"msg":"11","request_id":"22"})';
    
        $response = new ResponseXmlObject();
        
        set_error_handler(array($this, 'forErrorHandler'));
        $response->create($code, $rawResult);
        restore_error_handler();
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('PARSE_ERROR_RESPONSE_XML', $error);
    }
    
    
    public function forErrorHandler($errno, $errstr, $errfile, $errline){
        
        $detectXmlError = stripos($errstr, 'simplexml_load_string');
        if($detectXmlError !== false && $detectXmlError < 10){
            return ;
        }
        
        $this->fail(var_export(array($errno, $errstr, $errfile, $errline), true));
        return false;
        
    }
    
}