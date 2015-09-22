<?php

use AlibabaSDK\Taobao\TaobaoClient;
use AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient;
use AlibabaSDK\Aliyun\AliyunClient;

if(!class_exists('AlibabaSDK\Integrate\ServiceLocator', false)){
    exit('ACCESS DENIED');
}

$config = array();

$config['TaobaoClient'] = function($loader){
    return new TaobaoClient(array(
        'appkey' => '',
        'appsecret' => '',
    ));
};


$config['TaobaoOAuthClient'] = function($loader){
    return new TaobaoOAuthClient(array(
        'appkey' => '',
        'appsecret' => '',
        'redirect_uri' => '',
    ));
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
$config['AliyunClient'] = function($loader){
    return new AliyunClient(array(
        'accessKeyId' => '',
        'accessKeySecret' => '',
        'regionId' => 'cn-hangzhou',
    ));
};

$config['AliyunClientRDS'] = function($loader){
    return new AliyunClient(array(
        'accessKeyId' => '',
        'accessKeySecret' => '',
        'regionId' => 'cn-hangzhou',
        'version' => '2014-08-15',
        'gatewayUrl' => 'https://rds.aliyuncs.com',
    ));
};

return $config;
