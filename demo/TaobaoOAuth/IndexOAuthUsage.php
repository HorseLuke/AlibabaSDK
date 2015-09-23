<?php

/**
 * 这是一个简单的OAuth登录全过程示例
 */
namespace Demo\TaobaoOAuth;

use AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient;
use AlibabaSDK\Integrate\FileRequestLogger;

use Demo\TaobaoOAuth\ViewPage;

require __DIR__. '/../CommonHeaderForDemoOnly.php';
require __DIR__. '/ViewPage.php';


/**
 * 简单的淘宝开放平台OAuth登录全过程Controller
 */
class Controller{

    /**
     *
     * @var \Demo\TaobaoOAuth\ViewPage
     */
    protected $view;

    public function __construct(){
        $this->view = new ViewPage();
    }

    function __call($name, $value){
        $this->sendErrorMsgAndExit($name. ' method IN Controller NOT EXIST');
    }

    function sendErrorMsgAndExit($name){
        $this->view->sendErrorMsgAndExit($name);
    }

    /**
     * 示例：首页示例
     */
    function indexAction(){
        $nextPageUrl = $this->getBaseUrl(true). '?action=redirectAuthUrl';
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
        $param['redirect_uri'] = $this->getBaseUrl(true). '?action=oauthCallback';

        $client = $this->getOAuthClient();
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
        $param['redirect_uri'] = $this->getBaseUrl(true). '?action=oauthCallback';

        $client = $this->getOAuthClient();
        $token = $client->getAccessToken($param);

        if(isset($token['error'])){
            $this->sendErrorMsgAndExit("getAccessToken出错：".  $token['error']. " / ". $token['error_description']);
        }

        $this->view->display('oauthCallback', array('token' => $token));

    }

    protected $baseUrl;

    protected function getBaseUrl($withEntry = false){
        if($this->baseUrl === null){
            $this->baseUrl = ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') ? 'http' : 'https'). '://'. $_SERVER['HTTP_HOST']. substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
        }

        if($withEntry){
            return $this->baseUrl. '/'. basename(DEMO_ENTRY_FILE);
        }else{
            return $this->baseUrl;
        }
    }

    /**
     * 获取TaobaoOAuthClient示例方法
     * @return \AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient
     */
    protected $oauthClient;

    protected function getOAuthClient(){
        if($this->oauthClient !== null){
            return $this->oauthClient;
        }

        $this->oauthClient = new TaobaoOAuthClient(array(
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
        $this->oauthClient->setRequestLogger('fileLogger', $fileLogger);

        return $this->oauthClient;
    }

}

define('DEMO_ENTRY_FILE', __FILE__);
$controller = new Controller();
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$action .= 'Action';
$controller->$action();
