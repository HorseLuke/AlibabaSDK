<?php

namespace AlibabaSDK\Taobao;

use Testsmoke_Loader;

/**
 * TaobaoClient基础测试
 * @author Horse Luke
 *
 */
class TaobaoClientTest extends \PHPUnit_Framework_TestCase{
    
    /**
     * @link http://open.taobao.com/doc/detail.htm?id=101617&spm=a219a.7386781.1998342838.20.juxYvY
     */
    public function testBuildSignature(){
        $taobaoClient =  \AlibabaSDK\Integrate\ServiceLocator::getInstance()->getService('TaobaoClientBuildSignatureTest');
        
        $sig = $taobaoClient->buildSignature(array(
            'method' => 'taobao.user.seller.get',
            'timestamp' => '2013-05-06 13:52:03',
            'format' => 'xml',
            'app_key' => 'test',
            'v' => '2.0',
            'fields' => 'nick',
            'sign_method' => 'md5',
            'session' => 'test',
        ), 'GET');
        
        $this->assertEquals('72CB4D809B375A54502C09360D879C64', $sig);
        
    }
    
    /**
     * @link http://open.taobao.com/doc/detail.htm?id=101617&spm=a219a.7386781.1998342838.20.juxYvY
     */
    public function testBuildSignatureWithFile(){
        $taobaoClient =  \AlibabaSDK\Integrate\ServiceLocator::getInstance()->getService('TaobaoClientBuildSignatureTest');
    
        $sig = $taobaoClient->buildSignature(array(
            'method' => 'taobao.user.seller.get',
            'timestamp' => '2013-05-06 13:52:03',
            'format' => 'xml',
            'app_key' => 'test',
            'v' => '2.0',
            'fields' => 'nick',
            'sign_method' => 'md5',
            'session' => 'test',
            'file' => $taobaoClient->curl_file_create(D_APP_DIR. '/Assets/1.txt'),    //该变量由于是文件，应不会影响最终签名
        ), 'POST');
    
        $this->assertEquals('72CB4D809B375A54502C09360D879C64', $sig);
    
    }
    
}