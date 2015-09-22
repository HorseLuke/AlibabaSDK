<?php

namespace AlibabaSDK\TaobaoOAuth;

use AlibabaSDK\Base\ConfigTrait;
use AlibabaSDK\Base\CurlRequestTrait;
use AlibabaSDK\Base\Response;

/**
 * 淘宝开放平台的OAuth客户端
 * 为了简单，不使用任何自定义Response
 * @author horseluke
 *
 */
class TaobaoOAuthClient{
    
    use ConfigTrait;
    
    use CurlRequestTrait;
    
    protected $cfg_appkey;
    
    protected $cfg_appsecret;
    
    protected $cfg_redirect_uri;
    
    protected $cfg_authUrl = "https://oauth.taobao.com/authorize";
    
    protected $cfg_tokenUrl = "https://oauth.taobao.com/token";
    
    /**
     * 初始化对象
     *
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        if (! empty($config)) {
            $this->setConfig($config);
        }
    
    }
    
    /**
     * 获取授权url
     * @param array $param 额外参数
     * @return string
     */
    public function getAuthUrl(array $param = null){
        if(!is_array($param)){
            $param = array();
        }
        
        if(empty($param['state'])){
            trigger_error('IN ORDER TO DEFENSE ATTACK, YOU SHOULD SET A RANDOM STRING IN param[\'state\'], STORE IN SESSION AND VERIFY IT BEFORE CALL getAccessToken() METHOD. See http://blog.sina.com.cn/s/blog_56b798f801018jyb.html', E_USER_WARNING);
        }
        if(isset($param['client_secret'])){
            unset($param['client_secret']);
        }
        
        $defaultParam = array();
        $defaultParam['client_id'] = $this->cfg_appkey;
        $defaultParam['response_type'] = 'code';
        $defaultParam['redirect_uri'] = $this->cfg_redirect_uri;
        
        return $this->cfg_authUrl. '?'. http_build_query(array_merge($defaultParam, $param));
        
    }
    
    /**
     * 获取AccessToken
     * @param array $param
     * @return array
     */
    public function getAccessToken(array $param = null){
        if(!is_array($param)){
            $param = array();
        }
        
        if(empty($param['code'])){
            return $this->createErrorBodyArray("INTERNAL_ERROR", 'EMPTY_PARAM_CODE');
        }
        
        $param['client_id'] = $this->cfg_appkey;
        $param['client_secret'] = $this->cfg_appsecret;
        if(!isset($param['redirect_uri'])){
            $param['redirect_uri'] = $this->cfg_redirect_uri;
        }
        if(!isset($param['grant_type'])){
            $param['grant_type'] = 'authorization_code';
        }
        
        $response = new Response();
        
        $this->rawSend($this->cfg_tokenUrl, $param, 'POST', $response);
        
        $result = $response->getRawResult();
        
        if(empty($result)){
            return $this->createErrorBodyArray("INTERNAL_ERROR", 'EMPTY_RESPONSE: '. $response->getError());
        }
        
        $result = json_decode($result, true);
        if(!is_array($result)){
            return $this->createErrorBodyArray("INTERNAL_ERROR", 'RESPONSE_JSON_FORMAT_ERROR');
        }
        
        return $result;
        
    }
    
    
    protected function createErrorBodyArray($error, $error_description = null){
        return array(
            'error' => $error,
            'error_description' => $error_description,
        );
    }
    
}