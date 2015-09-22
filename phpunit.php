<?php

if(!class_exists('PHPUnit_TextUI_Command', false)){
    exit('THIS IS FOR PHPUNIT RUN ONLY');
}

//SDK初始化
require __DIR__. '/src/AlibabaSDK/Integrate/Loader.php';
\AlibabaSDK\Integrate\Loader::getInstance()->reg2SPL();

//初始化Service Locator
$serviceLocatorConfig = array(
    'configFile' => array(
        __DIR__. '/testsmock/LoaderConfig/ServiceLocatorDefault.php',
        __DIR__. '/testsmock/LoaderConfig/ServiceLocatorPHPUnit.php',
        __DIR__. '/testsmock/LoaderConfig/ServiceLocatorProduction.php',
    ),
);
\AlibabaSDK\Integrate\ServiceLocator::getInstance($serviceLocatorConfig);


//mock初始化
require __DIR__ . '/testsmock/Class/Testsmoke_Loader.php';
Testsmoke_Loader::regLoadClassPath("testcase", __DIR__. '/tests');

Testsmoke_Loader::define(array(
    'D_APP_DIR' => __DIR__ . '/testsmock',
    'D_ENTRY_FILE' => __FILE__,
    'D_ENV' => 'Dev',
    'D_DEBUG' => 1,
));

$printPHPUnit = function($buffer = ""){
    echo PHP_EOL;
    if(!empty($buffer)){
        echo "\x1b[30;42m". $buffer. "\x1b[0m";
    }
};

$printPHPUnit();
$printPHPUnit("PHPUnit Test Prepare OK");
$printPHPUnit();
$printPHPUnit();
