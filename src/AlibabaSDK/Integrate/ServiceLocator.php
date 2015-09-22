<?php

namespace AlibabaSDK\Integrate;

/**
 * 依赖注入Service Locator简单实现。
 * 该Service Locator为单文件自包含，故不采用任何设计模式，代码也进行了额外冗余。
 * @author Administrator
 *
 */
class ServiceLocator{
    
    protected static $instance;
    
    /**
     * 依赖注入Service Locator配置文件位置
     * 如果为数组，下一个文件会覆盖上一个文件配置！
     * @var string
     */
    protected $cfg_configFile;
    
    /**
     * 依赖注入Service Locator注册树
     * @var string
     */
    protected $registry = array();
    
    /**
     * 依赖注入Service Locator实例树
     * @var string
     */
    protected $serviceLocatorInstance = array();
    
    
    /**
     * 初始化对象
     * 非特殊情况下，请使用静态方法ServiceLocator::getInstance()获取单例！
     *
     * @param array $config            
     */
    public function __construct(array $config = null)
    {
        if (! empty($config)) {
            $this->setConfig($config);
        }
        
        $this->initServiceByConfigFile();
        
    }
    
    /**
     * 设置配置
     * @param array $config
     */
    public function setConfig(array $config)
    {
        foreach ($config as $k => $v) {
            $k = 'cfg_' . $k;
            if (!property_exists($this, $k)) {
                continue;
            }
            $this->{$k} = $v;
        }
    }
    
    /**
     * 获取配置
     *
     * @param string $k
     * @return string
     */
    public function getConfig($k)
    {
        $k = 'cfg_' . $k;
        return isset($this->{$k}) ? $this->{$k} : null;
    }
    
    
    /**
     * 获取单例
     * @return \AlibabaSDK\Integrate\ServiceLocator
     */
    public static function getInstance(array $config = null){
        if(null === self::$instance){
            self::$instance = new self($config);
        }
        
        return self::$instance;
    }
    
    /**
     * 依赖注入Service Locator：获取一个注册的Service实例
     * 每次调用均为同一个单例
     * @param string $name
     * @return mixed
     */
    public function getService($name){
        if(isset($this->serviceLocatorInstance[$name])){
            return $this->serviceLocatorInstance[$name];
        }
        
        $service = isset($this->registry[$name]) ? $this->registry[$name] : null;
        if(null === $service || !$service instanceof \Closure){
            return $service;
        }
        
        $this->serviceLocatorInstance[$name] = $service($this);
        
        return $this->serviceLocatorInstance[$name];
    }
    
    /**
     * 依赖注入Service Locator：创建一个注册的Service实例
     * 如果注册时是一个匿名函数，则一个每次调用均产生不同的实例。
     * 故需要单例的，请用getService()方法
     * @param string $name
     * @return mixed
     */
    public function createService($name){
        $service = isset($this->registry[$name]) ? $this->registry[$name] : null;
        if(null === $service || !$service instanceof \Closure){
            return $service;
        }
    
        return $service($this);
    }
    
    /**
     * 依赖注入Service Locator：注册一个Service
     * @param string $name Service名称
     * @param mixed $value 建议为匿名函数，函数传递的第一个参数为Loader实例。
     */
    public function setService($name, $value){
        $this->registry[$name] = $value;
        if(isset($this->serviceLocatorInstance[$name])){
            unset($this->serviceLocatorInstance[$name]);
        }
    }
    
    /**
     * 依赖注入Service Locator：删除一个Service
     * @param string $name Service名称
     */
    public function delService($name){
        if(isset($this->registry[$name])){
            unset($this->registry[$name]);
        }
        if(isset($this->serviceLocatorInstance[$name])){
            unset($this->serviceLocatorInstance[$name]);
        }
    }
    
    /**
     * 依赖注入Service Locator：初始化Service注册树
     */
    protected function initServiceByConfigFile(){
        
        if(empty($this->cfg_configFile)){
            return ;
        }
        
        $sourceFiles = is_array($this->cfg_configFile) ? $this->cfg_configFile : array($this->cfg_configFile);
        
        $conf = array();
        
        foreach($sourceFiles as $file){
    
            if(!file_exists($file)){
                continue;
            }
            
            $newConf = require $file;
            if(is_array($newConf) && !empty($newConf)){
                $conf = self::array_merge($conf, $newConf);
            }
        }
        
        $this->registry = $conf;
        
    }
    
    /**
     * 两个数组合并，代码来自yiiframework
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     */
    public static function array_merge($a, $b){
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::array_merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }
    
        return $res;
    }
    
}