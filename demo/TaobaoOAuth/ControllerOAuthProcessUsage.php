<?php

namespace ControllerOAuthProcessUsage;

use AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient;
use AlibabaSDK\Integrate\FileRequestLogger;

use CommonTaobaoOAuthForDemoOnly\SimpleController;
use CommonTaobaoOAuthForDemoOnly\SimpleView;

/**
 * 简单的淘宝开放平台OAuth登录全过程Controller
 */
class Controller extends SimpleController{
    
    protected $viewClassName = 'ControllerOAuthProcessUsage\View';
    
    /**
     * 示例：首页示例
     */
    function indexAction(){
        $nextPageUrl = HelperDemo::getBaseUrl(true). '?action=redirectAuthUrl';
        $this->view->display('index', array('url' => $nextPageUrl));
    }
    
    /**
     *示例：第一步： 生成的淘宝的OAuth授权页面URL并跳转
     */
    function redirectAuthUrlAction(){
    
        session_start();
    
        $_SESSION['state_for_verify'] = md5(mt_rand(). __FILE__);
    
        $param = array();
        $param['state'] = $_SESSION['state_for_verify'];    //请参见：http://blog.sina.com.cn/s/blog_56b798f801018jyb.html
    
        /*
         * 如果你确定redirect_uri一直保持不变，
         * 且在构建TaobaoOAuthClient时候指定了redirect_uri，那么就不需要传递redirect_uri；
         * 否则，getAuthUrl()需要每次都传入redirect_uri
         *
         */
        $param['redirect_uri'] = HelperDemo::getBaseUrl(true). '?action=oauthCallback';
    
        $client = HelperDemo::getOAuthClient();
        $finalUrl = $client->getAuthUrl($param);
    
        $this->view->display('redirectAuthUrl', array('finalUrl' => $finalUrl));
    }
    
    
    /**
     * 示例：第2步：回调并获取Access Token
     */
    function oauthCallbackAction(){
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        $state = isset($_GET['state']) ? $_GET['state'] : '';
    
        if(empty($code)){
            $this->sendErrorMsgAndExit("code不存在");
        }
    
        session_start();
        $session_state_for_verify = "";
        if(isset($_SESSION['state_for_verify'])){
            $session_state_for_verify = $_SESSION['state_for_verify'];
            unset($_SESSION['state_for_verify']);    //请注意这里！务必unset！
        }
        if(empty($session_state_for_verify) || $state !== $session_state_for_verify){
            $this->sendErrorMsgAndExit("state不正确，请重新登录。");
        }
    
        $param = array();
        $param['code'] = $code;
        $param['state'] = $state;
        /*
         * 如果你确定redirect_uri一直保持不变，
         * 且在构建TaobaoOAuthClient时候指定了redirect_uri，那么就不需要传递redirect_uri；
         * 否则，getAccessToken()需要每次都传入redirect_uri
         *
         */
        $param['redirect_uri'] = HelperDemo::getBaseUrl(true). '?action=oauthCallback';
    
        $client = HelperDemo::getOAuthClient();
        $token = $client->getAccessToken($param);
    
        if(isset($token['error'])){
            $this->sendErrorMsgAndExit("getAccessToken出错：".  $token['error']. " / ". $token['error_description']);
        }
    
        $this->view->display('oauthCallback', array('token' => $token));
    
    }
    
    
}


class View extends SimpleView{
    
    public $title = "淘宝OAuth登录过程Demo";
    
    function sendErrorMsgAndExit($msg){
        $this->display('error', array('msg' => $msg));
        exit();
    }
    
    function indexPage($data){
        echo <<<EOF
        <div><a href="{$data['url']}">点击此处开始：（1）拼接授权url（并跳转）</a></div>
        <div><a href="https://open.taobao.com/doc/detail.htm?id=102635&spm=a219a.7386781.1998342838.19.lDPmBG">点击此处查看淘宝开放平台OAuth文档</a></div>
EOF;
    }
    
    
    function redirectAuthUrlPage($data){
        echo <<<EOF
        <div>\AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient::getAuthUrl()生成的淘宝的OAuth授权页面URL：</div>
        <div><a href="{$data['finalUrl']}">{$data['finalUrl']}</a></div>
        <div>
                在实际开发中，你可以直接将上面的URL通过Header输出跳转，从而使得用户无需点击，即可到达淘宝的OAuth授权页面。
        </div>
        <div>
                点击上面的url以进入淘宝的OAuth授权页面。
        </div>
        <div>
                有关state参数，请<a href="http://blog.sina.com.cn/s/blog_56b798f801018jyb.html">点击这里阅读相关文档。</a>
        </div>
EOF;
    }
    
    function oauthCallbackPage($data){
        $tokenValue = var_export($data['token'], true);
        echo <<<EOF
        <div>access token信息：<pre>{$tokenValue}</pre></div>
EOF;
    }
    
}


class HelperDemo{

    /**
     * 获取TaobaoOAuthClient示例方法
     * @return \AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient
     */
    static public function getOAuthClient(){
        static $clientInstance;
        if($clientInstance !== null){
            return $clientInstance;
        }

        $clientInstance = new TaobaoOAuthClient(array(
            'appkey' => DEMO_TAOBAO_APPKEY,
            'appsecret' => DEMO_TAOBAO_APPSECRET,
            'redirect_uri' => '',    //如果你确定redirect_uri一直保持不变，那么就在建立TaobaoOAuthClient实例的时候指定，否则需要在createAuthUrl()和getAccessToken()中传递
        ));
        
        /*
         * 如果需要记录日志，可参照以下代码，
         * 在使用了\AlibabaSDK\Base\CurlRequestTrait的类中：
         *     - 向setRequestLogger注入匿名函数、
         *     - 或注入实现了\AlibabaSDK\Base\CurlRequestLoggerInterface接口类的实例
         *         （\AlibabaSDK\Integrate\FileRequestLogger为一个示例）
         * 传递的参数请参见方法\AlibabaSDK\Base\CurlRequestLoggerInterface::receiveSignalRequestLogger()
        */
        $fileLogger = new FileRequestLogger(array(
            'logDir' => DEMO_LOGDIR,
        ));
        $clientInstance->setRequestLogger('fileLogger', $fileLogger);

        return $clientInstance;
    }

    static public function getBaseUrl($withEntry = false){
        static $baseUrl;
        if($baseUrl === null){
            $baseUrl = ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') ? 'http' : 'https'). '://'. $_SERVER['HTTP_HOST']. substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
        }

        if($withEntry){
            return $baseUrl. '/'. basename(DEMO_ENTRY_FILE);
        }else{
            return $baseUrl;
        }
    }

}
