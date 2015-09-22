<?php

use AlibabaSDK\Taobao\TaobaoClient;
use AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient;
use AlibabaSDK\Aliyun\AliyunClient;
use AlibabaSDK\Integrate\FileRequestLogger;

if(!class_exists('AlibabaSDK\Integrate\ServiceLocator', false)){
    exit('ACCESS DENIED');
}

$config = array();

$config['TaobaoClient'] = function($loader){
    $client = new TaobaoClient(array(
        'appkey' => DEMO_TAOBAO_APPKEY,
        'appsecret' => DEMO_TAOBAO_APPSECRET,
    ));
    
    /*
     * 如果需要记录日志，可参照以下代码，
     * 在使用了\AlibabaSDK\Base\CurlRequestTrait的类中：
     *     - 向setRequestLogger注入匿名函数、
     *     - 或注入实现了\AlibabaSDK\Base\CurlRequestLoggerInterface接口类的实例
     *         （\AlibabaSDK\Integrate\FileRequestLogger为一个示例）
     * 传递的参数请参见方法\AlibabaSDK\Base\CurlRequestLoggerInterface::receiveSignalRequestLogger()
     */
    $fileLogger = new FileRequestLogger(array(
        'logDir' => DEMO_LOGDIR,
    ));
    $client->setRequestLogger('fileLogger', $fileLogger);
    
    return $client;
};


$config['TaobaoOAuthClient'] = function($loader){
    return new TaobaoOAuthClient(array(
        'appkey' => DEMO_TAOBAO_APPKEY,
        'appsecret' => DEMO_TAOBAO_APPSECRET,
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
