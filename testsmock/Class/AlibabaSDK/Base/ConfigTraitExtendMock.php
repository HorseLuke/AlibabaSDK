<?php

namespace AlibabaSDK\Base;

/**
 * 仅用于测试\AlibabaSDK\Base\ConfigTrait
 * @author Administrator
 *
 */
class ConfigTraitExtendMock{
    
    use ConfigTrait;
    
    protected $cfg_test_in_phpunit = 1;
    
}