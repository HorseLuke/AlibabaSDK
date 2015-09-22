<?php

namespace Example2Aliyun;

use AlibabaSDK\Taobao\TaobaoClient;
use AlibabaSDK\Aliyun\AliyunClient;

/**
 * \AlibabaSDK\Aliyun\AliyunClient基础用法示例和测试
 * @author horseluke
 *
 */
class AliyunClientBasicUsageTest extends  \PHPUnit_Framework_TestCase{
    
    /**
     * 
     * @var \AlibabaSDK\Aliyun\AliyunClient
     */
    protected $mockClient;
    
    protected function setUp(){
        parent::setUp();
        
        $this->mockClient =  \AlibabaSDK\Integrate\ServiceLocator::getInstance()->getService('AliyunClient');   //模拟读取正式环境配置
        
        $accessKey = $this->mockClient->getConfig('accessKeyId');
        $accessKeySecret = $this->mockClient->getConfig('accessKeySecret');
        
        if(empty($accessKey) || empty($accessKeySecret)){
            $this->markTestSkipped('accessKeyId or accessKeySecret is not set in service config with service name "AliyunClient", test will skipped' );
        }
        
    }
    
    /**
     * 最基础用法，以测试查询实例资源规格列表（DescribeInstanceTypes）为例子
     * @link https://docs.aliyun.com/#/pub/ecs/open-api/other&describeinstancetypes
     */
    public function testSend(){
        $response = $this->mockClient->send('DescribeInstanceTypes');
        
        if(!$response->isOk()){
            $this->fail(
                "RESPONSE_HAS_ERROR. "
                . "ERROR INFO:". $response->getError(). PHP_EOL
                . "RAW HTTP RETURN BODY:". PHP_EOL.  $response->getRawResult(). PHP_EOL
            );
        }
        
        $result = $response->getResult();
        $this->assertArrayHasKey('InstanceTypes', $result);
        
    }
    
}