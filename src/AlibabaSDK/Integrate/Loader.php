<?php

namespace AlibabaSDK\Integrate;

/**
 * 接合本SDK的Loader。
 * 该Loader为单文件自包含，故不采用任何设计模式，代码也进行了额外冗余。
 * 如果你的框架已经使用了PSR-4载入方式，并且已经放置到正确的位置，那么不用执行本类的reg2SPL()方法。
 * 否则，请在使用本SDK前，调用静态方法Loader::getInstance()初始化本Loader，然后执行reg2SPL()方法注册，即：
 *     require_once '[SDK下的src目录]/AlibabaSDK/Integrate/Loader.php';
 *     \AlibabaSDK\Integrate\Loader::getInstance()->reg2SPL();
 * @author Administrator
 *
 */
class Loader{
    
    const ALLOW_CLASSNAME_PREFIX = 'AlibabaSDK';
    
    protected static $instance;
    
    /**
     * 配置：本SDK的载入路径。
     * 注意：一般情况下，不用修改本设置。
     * @var string|array
     */
    protected $cfg_loadPath;
    
    /**
     * windows模式下，使用本SDK的严格大小写debug
     * @var unknown
     */
    protected $cfg_winLoaderDebug = false;
    
    protected $registerSPLAutoload = false;
    
    /**
     * 初始化对象
     * 非特殊情况下，请使用静态方法Loader::getInstance()获取单例！
     *
     * @param array $config            
     */
    public function __construct(array $config = null)
    {
        if (! empty($config)) {
            $this->setConfig($config);
        }
        
        if(null === $this->cfg_loadPath){
            $this->cfg_loadPath = __DIR__ . '/../../';
        }
        
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
     * @return \AlibabaSDK\Integrate\Loader
     */
    public static function getInstance(array $config = null){
        if(null === self::$instance){
            self::$instance = new self($config);
        }
        
        return self::$instance;
    }
    
    /**
     * 向SPL注册自动载入本SDK类的方法
     * 如果你已经实现了PSR-4载入方式，并且正确的该sdk代码，那么请不要使用本方法
     * 否则，请运行该方法定义自动载入，最简单的代码是：
     *     \AlibabaSDK\Integrate\Loader::getInstance()->reg2SPL();
     * 注意：该方法仅需运行一次，虽然在单例模式下，多次执行也不会出问题。
     */
    public function reg2SPL(){
        if($this->registerSPLAutoload === true){
            return ;
        }
        
        spl_autoload_register(array($this, 'autoLoadSDKByPSR4'));
        $this->registerSPLAutoload = true;
        
    }
    
    /**
     * 按照PSR-4载入本SDK
     * @param string $className
     */
    public function autoLoadSDKByPSR4($className){
        $num = stripos($className, self::ALLOW_CLASSNAME_PREFIX);
        
        if($num === false || $num > 3){
            return ;
        }
        
        $className = str_replace('\\', '/', $className);
        $className = ltrim($className, '/'). '.php';
        
        $realFilepath = $this->cfg_loadPath. DIRECTORY_SEPARATOR. $className;
        
        if($this->file_exists_case($realFilepath)){
            require_once $realFilepath;
            return ;
        }
    }
    
    /**
     * 严格按照大小写，判断本地文件是否存在
     * 部分代码来自ThinkPHP，并进行适当裁剪
     * @param string $file
     * @return bool
     */
    public function file_exists_case($filename){
        static $iswin = null;
        if(null === $iswin){
            $iswin = 0 === stripos(PHP_OS, 'win');
        }
        if (file_exists($filename)) {
            if ($iswin && $this->cfg_winLoaderDebug){
                if (basename(realpath($filename)) != basename($filename)){
                    $realpath = realpath($filename);
                    $errorStr = 'File_name_case_sensitive_error_on_linux_emulator_for_win!';
                    $errorStr .= ' [PASS PARAM FILE basename] '. basename($filename) . ' != [REAL FILE basename] '. basename($realpath);
                    $errorStr .= ' [PASS PARAM FILE] '. $filename. ' ;[REAL FILE]'. $realpath;
                    throw new \InvalidArgumentException($errorStr);
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    
}