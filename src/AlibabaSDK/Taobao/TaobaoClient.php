<?php

namespace AlibabaSDK\Taobao;

use AlibabaSDK\Base\ConfigTrait;
use AlibabaSDK\Base\CurlRequestTrait;
use AlibabaSDK\Base\Response;

/**
 * 调用淘宝开放平台api的Client
 * @author Administrator
 *
 */
class TaobaoClient{
    
    use ConfigTrait;
    
    use CurlRequestTrait;
    
    protected $cfg_appkey;
    
    protected $cfg_appsecret;
    
    /**
     * API网关地址。
     * 1、http 网关
     *       正式环境：http://gw.api.taobao.com/router/rest
     *       沙箱环境：http://gw.api.tbsandbox.com/router/rest
     * 2、https 网关
     *       正式环境：https://eco.taobao.com/router/rest
     *      沙箱环境：https://gw.api.tbsandbox.com/router/rest 
     * @var string
     */
    protected $cfg_gatewayUrl = "http://gw.api.taobao.com/router/rest";
    
    protected $cfg_format = "json";
    
    protected $cfg_signMethod = "md5";
    
    protected $cfg_apiVersion = "2.0";
    
    /**
     * 用户的session，也是淘宝开放平台的Access Token
     * @var string
     */
    protected $cfg_session;
    
    protected $sdkVersion = "baba-sdk-php-20150920";
    
    /**
     * 初始化对象
     *
     * @param array $config            
     */
    public function __construct(array $config = null)
    {

        //trait不允许重复定义属性。故在此处修改
        $this->cfg_defaultResponseClass = 'AlibabaSDK\Taobao\ResponseJson';
        
        if (! empty($config)) {
            $this->setConfig($config);
        }
        
    }
    
    /**
     * 发送请求，并返回结果
     * @param string $actionName
     * @param array|string $param
     * @param string $requestMethod 请求方法，必须全大写。不传入则根据$param来定
     * @param \AlibabaSDK\Base\Response $response
     * @return \AlibabaSDK\Base\Response $response
     */
    public function send($actionName, $param = null, $requestMethod = null, Response $response = null){
        if(null === $requestMethod){
            $requestMethod = empty($param) ? 'GET' : 'POST';
        }else{
            $requestMethod = strtoupper($requestMethod);
        }
        
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
    
    /**
     * 构建url的查询参数
     * @param string $actionName
     * @param array|null $param
     * @param string $requestMethod 请求方法，必须全大写
     * @return multitype:string unknown NULL |unknown
     */
    protected function buildUrlParam($actionName, $param = null, $requestMethod = 'GET'){
        
        $sysParam = array();
        $sysParam['method'] = $actionName;
        $sysParam['timestamp'] = gmdate("Y-m-d H:i:s", time() + 28800);    //防止时区干扰
        $sysParam['format'] = $this->cfg_format;
        $sysParam['app_key'] = $this->cfg_appkey;
        $sysParam['v'] = $this->cfg_apiVersion;
        $sysParam['sign_method'] = $this->cfg_signMethod;
        if(null !== $this->cfg_session){
            $sysParam['session'] = $this->cfg_session;
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
            $sysParam['sign'] = $sign;
            return $sysParam;
        }
        
        $allParam['sign'] = $sign;
        return $allParam;
    }
    
    /**
     * 对参数进行签名
     * @param array $param
     */
    public function buildSignature($param, $requestMethod){
        ksort($param);
        
        $singString = $this->cfg_appsecret;
        
        foreach($param as $k => $v){
            //文件上传
            if ($requestMethod == 'POST' && $this->isUploadAtomCmd($v)) {
                continue;
            }
        
            $singString .= $k.$v;
        
        }
        
        $singString .= $this->cfg_appsecret;
        
        return strtoupper(md5($singString));
    }
    
}