<?php

namespace AlibabaSDK\Taobao;

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
        $rawResult = '<?xml version="1.0" encoding="utf-8" ?><time_get_response><time>2015-09-16 15:52:52</time><request_id>1</request_id></time_get_response><!--e010101080212.zmf-->';
        
        $response = new ResponseXmlObject();
        $response->create($code, $rawResult);
        
        $this->assertTrue($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
        
        $result = $response->getResult();
        $this->assertObjectHasAttribute('time', $result);
        $this->assertObjectNotHasAttribute('code', $result);
    }
    
    
    public function testCreateWithError(){
        $code = 403;
        $rawResult = '<?xml version="1.0" encoding="utf-8" ?><error_response><code>11</code><msg>Insufficient isv permissions</msg><sub_code>isv.permission-api-package-limit</sub_code><sub_msg>scope ids is 274 287</sub_msg><request_id>alibaba</request_id></error_response><!--top010178001146.n.et2-->';
        
        $response = new ResponseXmlObject();
        $response->create($code, $rawResult);
        
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('API_RETURN_ERROR_CODE', $error);
    }
    
    public function testCreateWithErrorSubCode(){
        $code = 403;
        $rawResult = '<?xml version="1.0" encoding="utf-8" ?><error_response><code>11</code><msg>Insufficient isv permissions</msg><sub_code>isv.permission-api-package-limit</sub_code><sub_msg>scope ids is 274 287</sub_msg><request_id>alibaba</request_id></error_response><!--top010178001146.n.et2-->';
    
        $response = new ResponseXmlObject();
        $response->create($code, $rawResult);
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError(true);
        $this->assertEquals('API_RETURN_ERROR_CODE', $error['error']);
        $this->assertTrue(stripos($error['errorDetail'], 'Insufficient isv permissions') !== false);
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
    
    public function testCreateWithHttpErrorCode(){
        $code = 403;
        $rawResult = '<?xml version="1.0" encoding="utf-8" ?><time_get_response><time>2015-09-16 15:52:52</time><request_id>1</request_id></time_get_response><!--e010101080212.zmf-->';
    
        $response = new ResponseXmlObject();
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