<?php

namespace AlibabaSDK\TaobaoOAuth;

class TaobaoOAuthClientTest  extends \PHPUnit_Framework_TestCase{
    
    public function testGetAuthUrl(){
        $param = array();
        $param['state'] = 'phpunit_TEST_phpunit_test_phpunit_test_phpunit_test_phpunit_test_'. mt_rand();
        
        $client = new TaobaoOAuthClient();
        $url = $client->getAuthUrl($param);
        
        $cond = strpos($url, $param['state']);
        $this->assertTrue($cond !== false);
    }
    

    /**
     * 模拟开发误传client_secret。本类将清除
     */
    public function testGetAuthUrlWithRemoveSecret(){
        $param = array();
        $param['state'] = 'phpunit_TEST_state_'. mt_rand();
        $param['client_secret'] = 'phpunit_TEST_client_secret';    //模拟开发误传client_secret。本类将清除
    
        $client = new TaobaoOAuthClient();
        $url = $client->getAuthUrl($param);
    
        $cond = strpos($url, $param['client_secret']);
        $this->assertTrue($cond === false);
    }
    
    
    public function testGetAuthUrlWithoutStateTriggerError(){
        set_error_handler(array($this, 'forErrorHandlerNoState'));
        $client = new TaobaoOAuthClient();
        $url = $client->getAuthUrl();
        restore_error_handler();
    
    }
    
    public function forErrorHandlerNoState($errno, $errstr, $errfile, $errline){
    
        $detectError = stripos($errstr, 'IN ORDER TO DEFENSE ATTACK');
        if($detectError !== false && $detectError < 10){
            return ;
        }
    
        $this->fail(var_export(array($errno, $errstr, $errfile, $errline), true));
        return false;
    
    }
    
    public function testGetAccesstokenWithError(){
        $client = new TaobaoOAuthClient();
        
        //这在测试中是故意的！目的是为了测试异常。正式环境请勿随意修改tokenUrl！
        $client->setConfig(array('tokenUrl' => 'xftp://127.0.0.1/asdfasdfasdf/asdfasdfasdasdf/asdfasd/token'));
        
        $token = $client->getAccessToken(array('code' => 1));
        
        if(empty($token['error'])){
            $this->fail("GetAccesstokenWithError do not return fail as expected");
        }
        
    }
    
    
}