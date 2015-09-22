<?php

namespace Example2AliyunRDS;

use AlibabaSDK\Aliyun\AliyunClient;

/**
 * \AlibabaSDK\Aliyun\AliyunClient基础用法示例和测试：RDS数据库部分
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
        
        $this->mockClient =  \AlibabaSDK\Integrate\ServiceLocator::getInstance()->getService('AliyunClientRDS');   //模拟读取正式环境配置
        
        $accessKey = $this->mockClient->getConfig('accessKeyId');
        $accessKeySecret = $this->mockClient->getConfig('accessKeySecret');
        
        if(empty($accessKey) || empty($accessKeySecret)){
            $this->markTestSkipped('accessKeyId or accessKeySecret is not set in service config with service name "AliyunClientRDS", test will skipped' );
        }
        
    }
    
    /**
     * 最基础用法，以测试查看数据库实例列表（DescribeDBInstances）为例子
     * @link https://docs.aliyun.com/?spm=5176.100054.3.3.wescEA#/pub/rds/open-api/instance-interface&DescribeDBInstances
     */
    public function testSend(){
        $response = $this->mockClient->send('DescribeDBInstances');
        
        if(!$response->isOk()){
            $this->fail(
                "RESPONSE_HAS_ERROR. "
                . "ERROR INFO:". $response->getError(). PHP_EOL
                . "RAW HTTP RETURN BODY:". PHP_EOL.  $response->getRawResult(). PHP_EOL
            );
        }
        
        $result = $response->getResult();
        $this->assertArrayHasKey('PageRecordCount', $result);
        
    }
    
}