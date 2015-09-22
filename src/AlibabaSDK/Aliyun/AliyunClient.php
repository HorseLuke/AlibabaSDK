<?php

namespace AlibabaSDK\Aliyun;

use AlibabaSDK\Base\ConfigTrait;
use AlibabaSDK\Base\CurlRequestTrait;
use AlibabaSDK\Base\Response;

/**
 * 调用阿里云api的Client
 * @author Administrator
 *
 */
class AliyunClient{
    
    use ConfigTrait;
    
    use CurlRequestTrait;
    
    protected $cfg_accessKeyId;
    
    protected $cfg_accessKeySecret;
    
    /**
     * API网关地址。默认云服务器https
     * @var string
     */
    protected $cfg_gatewayUrl = "https://ecs.aliyuncs.com";
    
    protected $cfg_format = "json";
    
    protected $cfg_signatureMethod = "HMAC-SHA1";
    
    protected $cfg_signatureVersion = "1.0";
    
    protected $cfg_version = "2014-05-26";
    
    protected $cfg_resourceOwnerAccount;
    
    protected $cfg_regionId;
    
    protected $sdkVersion = "baba-sdk-php-20150920";
    
    /**
     * 初始化对象
     *
     * @param array $config            
     */
    public function __construct(array $config = null)
    {

        //trait不允许重复定义属性。故在此处修改
        $this->cfg_defaultResponseClass = 'AlibabaSDK\Aliyun\ResponseJson';
        
        if (! empty($config)) {
            $this->setConfig($config);
        }
        
    }
    
    /**
     * 发送请求，并返回结果
     * @param string $actionName
     * @param array|string $param
     * @param string $requestMethod 请求方法，必须全大写。默认为GET
     * @param \AlibabaSDK\Base\Response $response
     * @return \AlibabaSDK\Base\Response $response
     */
    public function send($actionName, $param = null, $requestMethod = 'GET', Response $response = null){
        $requestMethod = strtoupper($requestMethod);
        
        return $this->rawSend(
            $this->createUrl($actionName, $param, $requestMethod), 
            $requestMethod != 'GET' ? $param : null, 
            $requestMethod, 
            $response
        );
    }
    
    /**
     * 创建url
     * @param string $actionName
     * @param string $param
     * @param string $requestMethod 请求方法，必须全大写
     * @return string
     */
    public function createUrl($actionName, $param = null, $requestMethod = 'GET'){
        $param = $this->buildUrlParam($actionName, $param, $requestMethod);
        return $this->cfg_gatewayUrl. '?'. http_build_query($param);
    }
    
    protected function buildUrlParam($actionName, $param = null, $requestMethod = 'GET'){
        
        $sysParam = array();
        $sysParam["Action"] = $actionName;
        $sysParam['Format'] = $this->cfg_format;
        $sysParam['Version'] = $this->cfg_version;
        $sysParam['AccessKeyId'] = $this->cfg_accessKeyId;
        $sysParam['SignatureMethod'] = $this->cfg_signatureMethod;
        $sysParam['Timestamp'] = gmdate('Y-m-d\TH:i:s\Z');    //忽略时区差异
        $sysParam['SignatureVersion'] = $this->cfg_signatureVersion;
        $sysParam['SignatureNonce'] = mt_rand();
        if(!empty($this->cfg_resourceOwnerAccount)){
            $sysParam['ResourceOwnerAccount'] = $this->cfg_resourceOwnerAccount;
        }
        if(!empty($this->cfg_regionId)){
            $sysParam['RegionId'] = $this->cfg_regionId;
        }
        $sysParam["partner_id"] = $this->sdkVersion;
        
        if(empty($param)){
            $param = array();
        }elseif(!is_array($param)){
            $paramTmp = array();
            parse_str($param, $paramTmp);
            $param = $paramTmp;
            unset($paramTmp);
        }
        
        $allParam = empty($param) ? $sysParam : array_merge($param, $sysParam);
        
        $sign = $this->buildSignature($allParam, $requestMethod);
        
        if($requestMethod != 'GET'){
            $sysParam['Signature'] = $sign;
            return $sysParam;
        }
        
        $allParam['Signature'] = $sign;
        return $allParam;
        
    }
    
    /**
     * 对参数进行签名
     * @param array $param
     */
    public function buildSignature($param, $requestMethod){
        
        ksort($param);
        
        $singString = "";
        
        foreach($param as $k => $v){
            //文件上传
            if ($requestMethod == 'POST' && $this->isUploadAtomCmd($v)) {
                continue;
            }
        
            $singString .= rawurlencode($k). '='. rawurlencode($v). '&';
        
        }
        
        $singString = substr($singString, 0, -1);
        $singString = $requestMethod. '&%2F&'. rawurlencode($singString);    //%2F即为rawurlencode('/')
        
	    $signature = base64_encode(hash_hmac('sha1', $singString, $this->cfg_accessKeySecret . '&', true));
	    return $signature;
    }
    
}