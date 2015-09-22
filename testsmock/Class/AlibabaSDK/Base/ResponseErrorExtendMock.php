<?php

namespace AlibabaSDK\Base;

class ResponseErrorExtendMock extends Response{
    
    /**
     * 错误扩展parseResult，没有返回true、或者错误时没有用setError设置
     * @see \TaobaoSDK\Base\Response::parseResult()
     */
    protected function parseResult(){
        
    }
    
}