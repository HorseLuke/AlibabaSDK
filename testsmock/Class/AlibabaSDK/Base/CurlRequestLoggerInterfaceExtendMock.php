<?php

namespace AlibabaSDK\Base;

/**
 * 仅用于测试CurlRequestLoggerInterface是否正常工作的mock
 * @author Administrator
 *
 */
class CurlRequestLoggerInterfaceExtendMock implements CurlRequestLoggerInterface{
    
    public $responseRawResult = 0;
    
    public function receiveSignalRequestLogger($url, $finalBodyParam, $requestMethod, Response $response){
        $this->responseRawResult = $response->getRawResult();
    }
    
}