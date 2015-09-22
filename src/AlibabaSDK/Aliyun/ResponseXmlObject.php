<?php

namespace AlibabaSDK\Aliyun;

use AlibabaSDK\Base\Response;

/**
 * 阿里云api返回结果xml解析器：以xml方式解析结果，并解析为对象SimpleXMLElement
 * @author Horse Luke
 *
 */
class ResponseXmlObject extends Response
{
    
    /**
     * 以xml方式解析结果，并解析为对象SimpleXMLElement
     * @return boolean
     */
    protected function parseResult(){
        $result = simplexml_load_string($this->rawResult);
        if (false === $result) {
            return $this->setError('PARSE_ERROR_RESPONSE_XML');
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
     * 仅供特定方法parseResult使用
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