<?php

namespace AlibabaSDK\Base;

/**
 * 以curl进行http/https请求的trait
 * @author Administrator
 *
 */
trait CurlRequestTrait{
    
    protected $cfg_defaultResponseClass = 'AlibabaSDK\Base\Response';
    
    protected $cfg_curlDisableSslVerify = true;
    
    /**
     * 等同于CURLOPT_TIMEOUT：The maximum number of seconds to allow cURL functions to execute.
     * @var int
     */
    protected $cfg_curlTimeout = 10;
    
    /**
     * 等同于CURLOPT_CONNECTTIMEOUT： The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
     * @var int
     */
    protected $cfg_curlConnectTimeout = 7;
    
    protected $curlInit;
    
    protected $requestLoggerStack = array();
    
    /**
     * 原始发送请求
     * @param string $url 完整URL
     * @param string|array $bodyParam body请求体。$requestMethod为POST时有效
     * @param string $requestMethod 请求方法，必须全大写
     * @param Response $response
     * @return Response $response
     */
    public function rawSend($url, $bodyParam = null, $requestMethod = 'GET', Response $response = null){
    
        if(null === $response){
            $response = $this->createDefaultResponse();
        }
        
        if(null === $this->curlInit){
            $this->curlInit = curl_init();
        }
    
        $curlOpt = $this->getDefaultCurlOpt();
        
        $curlOpt[CURLOPT_URL] = $url;
        
        if($requestMethod == 'POST'){
            $curlOpt[CURLOPT_POST] = true;
        }
        $curlOpt[CURLOPT_CUSTOMREQUEST] = $requestMethod;
        
        if($requestMethod == 'POST' || $requestMethod == 'PUT'){
            if(is_array($bodyParam)){
                if(!$this->rawSendCheckHasFile($bodyParam)){
                    $bodyParam = http_build_query($bodyParam);
                }else{
                    $bodyParam = $this->rawSendBuildCleanUploadBody($bodyParam);
                }
            }
            
            if($bodyParam !== null && $bodyParam !== ""){
                $curlOpt[CURLOPT_POSTFIELDS] = $bodyParam;
            }else{
                $curlOpt[CURLOPT_POSTFIELDS] = "";
            }
        }
        
        curl_setopt_array($this->curlInit, $curlOpt);
    
        $rawResult = curl_exec($this->curlInit);
        $curlInfo = curl_getinfo($this->curlInit);
    
        $curl_errno = curl_errno($this->curlInit);
        if($curl_errno){
            $response->setError("CURL_ERROR", curl_error($this->curlInit). '[ErrCode '. $curl_errno. ']');
        }else{
            $response->create($curlInfo['http_code'], $rawResult);
        }
    
        $response->setExtractInfo($curlInfo);
        
        if(!empty($this->requestLoggerStack)){
            $this->dispatchRequestLogger(
                $url,
                isset($curlOpt[CURLOPT_POSTFIELDS]) ? $curlOpt[CURLOPT_POSTFIELDS] : null,
                $requestMethod, 
                $response
            );
        }
        
        return $response;
    }
    
    public function createDefaultResponse(){
        $className = $this->cfg_defaultResponseClass;
        return new $className();
    }
    
    public function getDefaultCurlOpt(){
        $curlOpt = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => $this->cfg_curlTimeout,
            CURLOPT_CONNECTTIMEOUT => $this->cfg_curlConnectTimeout,
            CURLOPT_USERAGENT => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'DG_SRV no useragent'),
        );
        
        if ($this->cfg_curlDisableSslVerify) {
            $curlOpt[CURLOPT_SSL_VERIFYPEER] = false;
            $curlOpt[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        
        if(defined('CURLOPT_SAFE_UPLOAD')){
            $curlOpt[CURLOPT_SAFE_UPLOAD] = true;
        }
        
        return $curlOpt;
    }
    
    /**
     * 检查数组请求体内是否有文件参数
     * @param array $params
     * @return boolean
     */
    protected function rawSendCheckHasFile($params){
         
        foreach($params as $v){
            
            if($this->isUploadAtomCmd($v)){
                return true;
            }
            
        }
         
        return false;
    }
    
    /**
     * 对数组body进行检查和净化
     * @param array $param
     * @return array
     */
    protected function rawSendBuildCleanUploadBody($param){
        foreach($param as $k => $v){
            if($this->isUploadAtomCmd($v)){
                continue;
            }
            
            //curl在CURLOPT_POSTFIELDS接收数组参数时，不支持field为数组
            if(is_array($v)){
                unset($param[$k]);
                continue;
            }
            
            if(is_string($v) && !empty($v) && $v{0} == '@'){
                unset($param[$k]);
                continue;
            }
            
        }
        
        return $param;
    }
    
    /**
     * 设置一个requestLogger
     * @param string $name
     * @param CurlRequestLoggerInterface $logger
     */
    public function setRequestLogger($name, CurlRequestLoggerInterface $logger){
        $this->requestLoggerStack[$name] = $logger;
    }
    
    /**
     * 删除一个requestLogger
     * @param string $name
     */
    public function delRequestLogger($name){
        if(isset($this->requestLoggerStack[$name])){
            unset($this->requestLoggerStack[$name]);
        }
    }
    
    /**
     * 分发RequestLogger作记录
     * @param string $url 
     * @param nill|string|array $finalBodyParam 请求的body体。
     *     null表示没有发送任何body体。
     *     string形式，表示以application/x-www-form-urlencoded组body。
     *     array形式，表示以multipart/form-data组body体。常见于文件上传。
     * @param string $requestMethod 请求方式
     * @param Response $response 结果
     */
    protected function dispatchRequestLogger($url, $finalBodyParam, $requestMethod, Response $response){
        foreach($this->requestLoggerStack as $logger){
            $logger->receiveSignalRequestLogger($url, $finalBodyParam, $requestMethod, $response);
        }
    }
    
    /**
     * PHP < 5.5 的curl_file_create函数兼容。
     * 在PHP < 5.5使用CurlRequestTrait时，必须使用本方法，不能自己拼接@文件上传路径
     * @param string $filename
     * @param string $mimetype
     * @param string $postname
     * @return string
     */
    public function curl_file_create($filename, $mimetype = '', $postname = ''){
        
        if (!function_exists('curl_file_create')) {
            return new CURLFileCompat($filename, $mimetype, ($postname ? $postname : basename($filename)));
        }
        
        return curl_file_create($filename, $mimetype, $postname);
    }
    
    /**
     * 是否是一个文件上传的原子指令？
     * @param mixed $v
     * @param bool
     */
    protected function isUploadAtomCmd($v){
        
        //CURLFileCompat为低于php 5.5的兼容
        if($v instanceof \CURLFile || $v instanceof CURLFileCompat){
            return true;
        }
        
        return false;
    }
    
    public function __destruct(){
        if ($this->curlInit) {
            curl_close($this->curlInit);
        }
    }
    
}