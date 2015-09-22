<?php

namespace AlibabaSDK\Aliyun;

use AlibabaSDK\Base\Response;

/**
 * 阿里云api返回结果json解析器：把返回结果当作json解析，并解析为StdClass
 * @author Horse Luke
 *
 */
class ResponseJsonObject extends Response
{
    
    /**
     * 把返回结果当作json解析，并解析为StdClass
     * @return boolean
     */
    protected function parseResult(){
        $result = json_decode($this->rawResult);
    
        if(!is_object($result)) {
            return $this->setError('PARSE_ERROR_RESPONSE_JSON');
        }
        
        $this->result = $result;
    
        $detectError = $this->parseResultErrorByObject();
        if(!empty($detectError)){
            return $this->setError('API_RETURN_ERROR_CODE', $detectError);
        }
    
        return true;
    }
    

    /**
     * 解析结果对象是否有错误
     * 仅供特定方法parseResult_*使用
     * @return string
     */
    protected function parseResultErrorByObject()
    {
        $errorMsg = '';
        if(!empty($this->result->Code)){
            $errorMsg = $this->result->Code. ':'. $this->result->Message. ':[RequestId '. $this->result->RequestId. ']:[HostId '. $this->result->HostId. ']';
        }
        
        return $errorMsg;
    }
    
    
    
}