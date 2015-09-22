<?php

use AlibabaSDK\Taobao\TaobaoClient;
use AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient;
use AlibabaSDK\Aliyun\AliyunClient;

if(!class_exists('AlibabaSDK\Integrate\ServiceLocator', false)){
    exit('ACCESS DENIED');
}

$config = array();

$config['AliyunClientBuildSignatureTest'] = function($loader){
    return new AliyunClient(array(
        'accessKeyId' => 'testid',
        'accessKeySecret' => 'testsecret',
        'regionId' => 'cn-hangzhou',
    ));
};

$config['TaobaoClientBuildSignatureTest'] = function($loader){
    return new TaobaoClient(array(
        'appkey' => 'test',
        'appsecret' => 'test',
        'gatewayUrl' => 'https://eco.taobao.com/router/rest',  //https支持
    ));
};

$config['TaobaoClientSandbox'] = function($loader){
    return new TaobaoClient(array(
        'appkey' => '111111',
        'appsecret' => '111111',
        'gatewayUrl' => 'http://gw.api.tbsandbox.com/router/rest',
    ));
};

return $config;
