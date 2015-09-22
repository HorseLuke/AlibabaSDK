<?php

/**
 * 这是一个简单的OAuth登录全过程示例
 */
use AlibabaSDK\TaobaoOAuth\TaobaoOAuthClient;
use AlibabaSDK\Integrate\Loader;
use AlibabaSDK\Integrate\FileRequestLogger;

require __DIR__. '/../CommonHeaderForDemoOnly.php';
require __DIR__. '/CommonTaobaoOAuthForDemoOnly.php';
require __DIR__. '/ControllerOAuthProcessUsage.php';

define('DEMO_ENTRY_FILE', __FILE__);
$controller = new \ControllerOAuthProcessUsage\Controller();
$action = isset($_GET['action']) ? $_GET['action'] : 'index';
$action .= 'Action';
$controller->$action();
