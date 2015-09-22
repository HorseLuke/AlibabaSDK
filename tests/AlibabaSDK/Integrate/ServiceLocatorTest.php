<?php

namespace AlibabaSDK\Integrate;

class ServiceLocatorTest extends \PHPUnit_Framework_TestCase{
    
    /**
     *
     * @var \AlibabaSDK\Integrate\ServiceLocator
     */
    protected $mockTest;
    
    protected function setUp(){
        parent::setUp();
        //注意：这里仅是为了测试而开new ServiceLocator。如果在实际使用时，请直接使用ServiceLocator::getInstance()获取单例！
        $this->mockTest = new ServiceLocator();
    }
    
    public function testGetService(){

        $signal = "test____". mt_rand();
        
        $this->mockTest->setService("test1", function($loader) use ($signal){
            $instance = new \stdClass();
            $instance->signal = $signal;
            $instance->rand = mt_rand();
            return $instance;
        });
        
        $this->assertEquals($signal, $this->mockTest->getService('test1')->signal);
        $this->assertNotEmpty($this->mockTest->getService('test1')->rand);
        $this->assertEquals($this->mockTest->getService('test1')->rand, $this->mockTest->getService('test1')->rand);
        
    }
    
    public function testCreateService(){

        $this->mockTest->setService("test1", function($loader){
            $instance = new \stdClass();
            $instance->rand = mt_rand();
            return $instance;
        });
    
        $this->assertNotEmpty($this->mockTest->createService('test1')->rand);
        $this->assertNotEquals($this->mockTest->createService('test1')->rand, $this->mockTest->createService('test1')->rand);
    
    }
    
    public function testDelService(){
        $this->mockTest->setService("test1", function($loader){
            $instance = new \stdClass();
            $instance->rand = mt_rand();
            return $instance;
        });
        
        $this->assertNotEmpty($this->mockTest->getService('test1'));
        $this->mockTest->delService("test1");
        $this->assertEmpty($this->mockTest->getService('test1'));
        
    }
    
}