<?php

namespace AlibabaSDK\Aliyun;

/**
 * Response基础测试
 * @author Horse Luke
 *
 */
class ResponseXmlTest extends \PHPUnit_Framework_TestCase{
    
    public function testCreate(){
        $code = 200;
        $rawResult = '<?xml version="1.0" encoding="UTF-8"?> <APINAMEResponse><RequestId>4C467B38-3910-447D-87BC-AC049166F216</RequestId></APINAMEResponse>';
        
        $response = new ResponseXml();
        $response->create($code, $rawResult);
        
        $this->assertTrue($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
        
        $result = $response->getResult();
        $this->assertArrayHasKey('RequestId', $result);
    }
    
    public function testCreateWithError(){
        $code = 403;
        $rawResult = '<?xml version="1.0" encoding="UTF-8"?><Error><RequestId>8906582E-6722-409A-A6C4-0E7863B733A5</RequestId><HostId>ecs.aliyuncs.com</HostId><Code>UnsupportedOperation</Code><Message>The specified action is not supported.</Message></Error>';
        
        $response = new ResponseXml();
        $response->create($code, $rawResult);
        
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('API_RETURN_ERROR_CODE', $error);
    }
    
    public function testCreateWithErrorXMLError(){
        $code = 200;
        $rawResult = 'callback({"code":400,"msg":"11","request_id":"22"})';
    
        $response = new ResponseXml();
        
        set_error_handler(array($this, 'forErrorHandler'));
        $response->create($code, $rawResult);
        restore_error_handler();
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('PARSE_ERROR_RESPONSE_XML', $error);
    }
    
    public function testCreateWithHttpErrorCode(){
        $code = 403;
        $rawResult = '<?xml version="1.0" encoding="UTF-8"?> <APINAMEResponse><RequestId>4C467B38-3910-447D-87BC-AC049166F216</RequestId></APINAMEResponse>';
    
        $response = new ResponseXml();
        $response->create($code, $rawResult);
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('HTTP_CODE_ERROR', $error);
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