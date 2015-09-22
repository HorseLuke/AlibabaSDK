<?php

namespace AlibabaSDK\Base;

trait ConfigTrait{
    
    /**
     * 批量或单个设置配置
     * @param mixed $key 如果是数组，则批量设置，此时不用传value
     * @param mixed $value
     */
    public function setConfig($key, $value = null)
    {
        if(is_array($key)){
            foreach ($key as $k => $v) {
                $k = 'cfg_' . $k;
                if (!property_exists($this, $k)) {
                    continue;
                }
                $this->{$k} = $v;
            }
            
        }else{
            $k = 'cfg_' . $key;
            if (property_exists($this, $k)) {
                $this->{$k} = $value;
            }
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
    
}