<?php

namespace AlibabaSDK\Aliyun;

use Testsmoke_Loader;
use AlibabaSDK\Integrate\Loader;

/**
 * TaobaoClient基础测试
 * @author Horse Luke
 *
 */
class AliyunClientTest extends \PHPUnit_Framework_TestCase{
    
    /**
     * @link https://docs.aliyun.com/#/pub/ecs/open-api/requestmethod&signature
     */
    public function testBuildSignature(){
        $aliyunClient = \AlibabaSDK\Integrate\ServiceLocator::getInstance()->getService('AliyunClientBuildSignatureTest');
        
        $paramStr = 'TimeStamp=2012-12-26T10:33:56Z&Format=XML&AccessKeyId=testid&Action=DescribeRegions&SignatureMethod=HMAC-SHA1&RegionId=region1&SignatureNonce=NwDAxvLU6tFE0DVb&Version=2014-05-26&SignatureVersion=1.0';
        parse_str($paramStr, $param);
        $sig = $aliyunClient->buildSignature($param, 'GET');
        
        $this->assertEquals('K9fCVP6Jrklpd3rLYKh1pfrrFNo=', $sig);  //文档说是“SDFQNvyH5rtkc9T5Fwo8DOjw5hc=”，文档有错误？
        
    }
    
}