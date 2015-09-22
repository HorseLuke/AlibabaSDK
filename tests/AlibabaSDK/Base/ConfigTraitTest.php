<?php

namespace AlibabaSDK\Base;

/**
 * ConfigTrait测试
 * @author Horse Luke
 *
 */
class ConfigTraitTest extends \PHPUnit_Framework_TestCase{
    
    /**
     * 
     * @var \AlibabaSDK\Base\ConfigTrait
     */
    protected $mockConfig;
    
    protected function setUp(){
        parent::setUp();
        $this->mockConfig = new ConfigTraitExtendMock();
    }
    
    public function testGetConfig(){
        $this->assertEquals(1, $this->mockConfig->getConfig('test_in_phpunit'));
    }
    
    public function testSetConfig(){
        $this->mockConfig->setConfig(array('test_in_phpunit' => 999));
        $this->assertEquals(999, $this->mockConfig->getConfig('test_in_phpunit'));
    }
    
    public function testSetConfigBySingleValue(){
        $this->mockConfig->setConfig('test_in_phpunit', 10000);
        $this->assertEquals(10000, $this->mockConfig->getConfig('test_in_phpunit'));
    }
    
    public function testSetConfigNoUse(){
        $this->mockConfig->setConfig(array('no_use_2' => 1));
        $this->assertEquals(null, $this->mockConfig->getConfig('no_use'));
        $this->assertEquals(null, $this->mockConfig->getConfig('no_use_2'));
    }
    

}