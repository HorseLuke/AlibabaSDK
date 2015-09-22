<?php

namespace AlibabaSDK\Base;

/**
 * 用于测试CurlRequestTrait是否正常工作的mock
 * 同时也是用于测试CurlRequestLoggerInterface以implements方式调用自己是否正常工作的mock
 * @author Administrator
 *
 */
class CurlRequestTraitExtendMock implements CurlRequestLoggerInterface{
    
    protected $testLastResponseRawResult;
    
    use CurlRequestTrait{
        getDefaultCurlOpt as parentTrait_getDefaultCurlOpt;
    }
    
    public function getDefaultCurlOpt(){
        $curlOpt = $this->parentTrait_getDefaultCurlOpt();
        
        //https://harde.org/blog/2015/02/curl-35-error14077410.html
        //$curlOpt[CURLOPT_SSLVERSION] = 3;
        //$curlOpt[CURLOPT_SSL_CIPHER_LIST] = 'SSLv3';
        
        return $curlOpt;
    }
    
    public function receiveSignalRequestLogger($url, $finalBodyParam, $requestMethod, Response $response){
        $this->testLastResponseRawResult = $response->getRawResult();
    }
    
    public function getTestLastResponseRawResult(){
        return $this->testLastResponseRawResult;
    }
    
}