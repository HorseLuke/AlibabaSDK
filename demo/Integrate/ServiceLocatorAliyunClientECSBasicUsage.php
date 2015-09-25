<?php
/*
 * 使用依赖注入Service Locator调用本SDK：AliyunClient（调用ECS）
 */

use AlibabaSDK\Integrate\ServiceLocator;

require __DIR__. '/../CommonHeaderForDemoOnly.php';

//初始化依赖注入Service Locator的单实例配置（\AlibabaSDK\Integrate\ServiceLocator）
$SLConfig = array(
    'configFile' => __DIR__. '/ConfigServiceLocatorDefaultDemo.php',    //配置文件写法见本文件所在文件夹下的ConfigServiceLocatorDefaultDemo.php
);
ServiceLocator::setInstanceDefaultConfig($SLConfig);
//初始化依赖注入Service Locator的单实例配置完毕


//请注意这里和demo文件/demo/TaobaoClient/basicUsage.php的不同。
//通过Service Locator，你可以随时调用，而无需重新初始化
$aliyunClient = ServiceLocator::getInstance()->getService('AliyunClient');


$response = $aliyunClient->send('DescribeInstanceTypes');

if(!$response->isOk()){
    exit("API Error!:". var_export($response->getError(true)));
}

$result = $response->getResult();
var_export($result);
