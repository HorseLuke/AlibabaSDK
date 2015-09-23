<?php

use AlibabaSDK\Taobao\TaobaoClient;
use AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient;
use AlibabaSDK\Aliyun\AliyunClient;
use AlibabaSDK\Integrate\FileRequestLogger;

if(!class_exists('AlibabaSDK\Integrate\ServiceLocator', false)){
    exit('ACCESS DENIED');
}

$config = array();

$config['TaobaoClient'] = function($locator){
    $client = new TaobaoClient(array(
        'appkey' => '',
        'appsecret' => '',
    ));
    return $client;
};


$config['TaobaoOAuthClient'] = function($locator){
    $client = new TaobaoOAuthClient(array(
        'appkey' => '',
        'appsecret' => '',
        'redirect_uri' => '',
    ));
    return $client;
};


/*
 *  \AlibabaSDK\Aliyun\AliyunClient中，可接收的regionId和gatewayUrl见以下连接：
 * @link https://github.com/aliyun/aliyun-openapi-php-sdk/blob/master/aliyun-php-sdk-core/Regions/EndpointConfig.php
 *
 * 注意，gatewayUrl请自行在前面增加https://
 */
/*
 截止20150921，regionId有：
 "cn-hangzhou","cn-beijing","cn-qingdao","cn-hongkong","cn-shanghai","us-west-1","cn-shenzhen","ap-southeast-1"
 */
$config['AliyunClient'] = function($locator){
    $client = new AliyunClient(array(
        'accessKeyId' => '',
        'accessKeySecret' => '',
        'regionId' => 'cn-hangzhou',
    ));
    return $client;
};

$config['AliyunClientRDS'] = function($locator){
    $client = new AliyunClient(array(
        'accessKeyId' => '',
        'accessKeySecret' => '',
        'regionId' => 'cn-hangzhou',
        'version' => '2014-08-15',
        'gatewayUrl' => 'https://rds.aliyuncs.com',
    ));
    return $client;
};


$config['FileRequestLogger'] = function($locator){
    $fileLogger = new FileRequestLogger(array(
        'logDir' => "",
    ));
    
    return $fileLogger;
};

return $config;
