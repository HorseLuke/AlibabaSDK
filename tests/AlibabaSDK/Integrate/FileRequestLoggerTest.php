<?php

namespace AlibabaSDK\Integrate;

use AlibabaSDK\Base\Response;

class FileRequestLoggerTest extends \PHPUnit_Framework_TestCase{
    
    /**
     *
     * @var \AlibabaSDK\Integrate\FileRequestLogger
     */
    protected $mockTest;
    
    protected function setUp(){
        parent::setUp();
        $this->mockTest = new FileRequestLogger(array(
            'logFilePrefix' => 'phpunit_',
        ));
    }
    
    public function testBuildLogString(){
        $result = 'phpunit-RAWRESULT-FORTESTONLY-phpunit-RAWRESULT-FORTESTONLY-'. mt_rand(). '-phpunit-RAWRESULT-FORTESTONLY';
        $response = new Response();
        $response->create(400, $result);
        
        $logString = $this->mockTest->buildLogString("http://notexistdomain.com", "a=1", 'POST', $response);
        
        $this->assertNotEquals(false, strpos($logString, $result));
    }
    
}