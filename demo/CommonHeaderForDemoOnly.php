<?php

exit("Comment out this code line to run");

error_reporting(E_ALL);

date_default_timezone_set('PRC');

define('DEMO_TAOBAO_APPKEY', '');
define('DEMO_TAOBAO_APPSECRET', '');

define('DEMO_ALIYUN_ACCESSKEY_ID', '');
define('DEMO_ALIYUN_ACCESSKEY_SECRET', '');

define('DEMO_LOGDIR', '/media/ramdisk/');    //FileRequestLogger使用

//如果你已经实现了PSR-4载入方式，并且正确的该sdk代码，那么请忽略以下载入代码
//否则需运行该代码定义，以自动载入
require __DIR__. '/../src/AlibabaSDK/Integrate/Loader.php';
\AlibabaSDK\Integrate\Loader::getInstance()->reg2SPL();
//定义自动载入完毕

