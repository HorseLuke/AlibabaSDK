<?php

use AlibabaSDK\Taobao\TaobaoClient;
use AlibabaSDK\Integrate\FileRequestLogger;

require __DIR__. '/../CommonHeaderForDemoOnly.php';

$taobaoClient = new TaobaoClient(array(
    'appkey' => DEMO_TAOBAO_APPKEY,
    'appsecret' => DEMO_TAOBAO_APPSECRET,
    'gatewayUrl' => 'https://eco.taobao.com/router/rest',  //https支持，可选
));

/*
 * 如果需要记录日志，可参照以下代码，
 * 在使用了\AlibabaSDK\Base\CurlRequestTrait的类中：
 *     - 注入实现了\AlibabaSDK\Base\CurlRequestLoggerInterface接口类的实例
 *         （\AlibabaSDK\Integrate\FileRequestLogger为一个示例）
 * 传递的参数请参见方法\AlibabaSDK\Base\CurlRequestLoggerInterface::receiveSignalRequestLogger()
*/
$fileLogger = new FileRequestLogger(array(
    'logDir' => DEMO_LOGDIR,
));
$taobaoClient->setRequestLogger('fileLogger', $fileLogger);

$response = $taobaoClient->send('alibaba.security.yundun.spam.validate', array(
    'content' => '你好！'
));

if(!$response->isOk()){
    exit("API Error!:". var_export($response->getError(true)));
}

$result = $response->getResult();
var_export($result);
