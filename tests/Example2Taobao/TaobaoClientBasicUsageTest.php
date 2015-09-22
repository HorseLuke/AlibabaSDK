<?php

namespace Example2Taobao;

use AlibabaSDK\Taobao\TaobaoClient;

/**
 * \AlibabaSDK\Taobao\TaobaoClient基础用法示例和测试
 * @author horseluke
 *
 */
class TaobaoClientBasicUsageTest extends  \PHPUnit_Framework_TestCase{
    
    /**
     * 
     * @var \AlibabaSDK\Taobao\TaobaoClient
     */
    protected $mockClient;
    
    protected function setUp(){
        parent::setUp();
        
        $this->mockClient =  \AlibabaSDK\Integrate\ServiceLocator::getInstance()->getService('TaobaoClientSandbox');
    }
    
    /**
     * 最基础用法，以测试taobao.products.search为例子
     * @link http://open.taobao.com/apidoc/api.htm?path=cid:4-apiId:5
     */
    public function testSend(){
        $response = $this->mockClient->send('taobao.products.search', array(
            'q' => '爆款',
        ));
        
        if(!$response->isOk()){
            $this->fail(
                "RESPONSE_HAS_ERROR. "
                . "ERROR INFO:". $response->getError(). PHP_EOL
                . "RAW HTTP RETURN BODY:". PHP_EOL.  $response->getRawResult(). PHP_EOL
            );
        }
        
        $result = $response->getResult();
        
        $this->assertArrayHasKey('total_results', $result);
        
    }
    
}