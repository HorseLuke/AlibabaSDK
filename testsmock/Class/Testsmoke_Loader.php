<?php

class Testsmoke_Loader{
    
    
    protected static $loadClassPath = array();
    
    public static $starttime;
    
    /**
     * 以PSR-4标准载入类
     * @param string $className
     */
    public static function loadClass($className) {
    
        $className = str_replace('\\', '/', $className);
        $className = ltrim($className, '/'). '.php';
    
        foreach(self::$loadClassPath as $findpath){
            $realFilepath = $findpath. DIRECTORY_SEPARATOR. $className;
            if(self::file_exists_case($realFilepath)){
                require $realFilepath;
                return ;
            }
        }
    
    }
    
    public static function regLoadClassPath($name, $path){
        self::$loadClassPath[$name] = $path;
    }
    
    public static function define($define){
    
        self::$starttime = microtime(true);
        
        foreach($define as $k => $v){
            define($k, $v);
        }
    
        // 务必定义应用目录路径
        if(!defined('D_APP_DIR') || !defined('D_ENTRY_FILE')){
            throw new \InvalidArgumentException('D_APP_DIR OR D_ENTRY_FILE NOT PASSED!');
        }
    
        if(!defined('D_ENV')){
            define('D_ENV', 'Dev');
        }
        
        self::$loadClassPath['app'] = D_APP_DIR. DIRECTORY_SEPARATOR. 'Class';
    
        spl_autoload_register(array(
            'self',
            'loadClass'
        ));
    
    }
    
    /**
     * 严格按照大小写，判断本地文件是否存在
     * 部分代码来自ThinkPHP，并进行适当裁剪
     * @param string $file
     * @return bool
     */
    public static function file_exists_case($filename){
        static $iswin = null;
        if(null === $iswin){
            $iswin = 0 === stripos(PHP_OS, 'win');
        }
        if (file_exists($filename)) {
            if ($iswin && D_DEBUG){
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
    
    
    /**
     * 从指定源中读取配置，依次读取路径：
     *     D_APP_DIR/Config/Default目录下的指定文件
     *     D_APP_DIR/Config/D_ENV目录下的指定文件
     * @param string $source 源。注意：此处不检查其路径！
     * @return array
     */
    public static function configRead($source){
        $conf = array();
    
        $sourceFiles = array(
            D_APP_DIR. '/Config/Default/'. $source. '.php',
            D_APP_DIR. '/Config/'. D_ENV. '/'. $source. '.php',
        );
    
        foreach($sourceFiles as $file){
    
            if(!file_exists($file)){
                continue;
            }
    
            $newConf = require $file;
            if(is_array($newConf) && !empty($newConf)){
                $conf = self::ArrayHelper_merge($conf, $newConf);
            }elseif(is_callable($newConf, true)){
                $conf = $newConf($conf);
            }
        }
    
        return $conf;
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
    public static function ArrayHelper_merge($a, $b){
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_integer($k)) {
                    isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::ArrayHelper_merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }
    
        return $res;
    }
    
    
}