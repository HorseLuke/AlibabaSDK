<?php

namespace AlibabaSDK\Base;

/**
 * Response错误扩展测试
 * @author Horse Luke
 *
 */
class ResponseErrorExtendTest extends \PHPUnit_Framework_TestCase{
    

    public function testCreateWithExtendError(){
        
        /**
         * testsmock/Class目录下的
         * \AlibabaSDK\Base\ResponseErrorExtendMock
         * 错误扩展方法parseResult，
         * 没有返回true、或者错误时没有用setError设置。
         * 此时Create后isOk()会失败，
         * getError()时并提示PARSE_ERROR_UNDEFINED_ERROR
         */
        $response = new ResponseErrorExtendMock();
        
        $response->create(200, "ffffff");
        
        $this->assertFalse($response->isOk());
    
        $error = $response->getError();
        $this->assertEquals('PARSE_ERROR_UNDEFINED_ERROR', $error);
    }
    
}