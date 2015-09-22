<?php
namespace AlibabaSDK\Taobao;

use AlibabaSDK\Base\Response;

/**
 * 把返回结果当作json解析，并解析为数组
 * @author horseluke
 *
 */
class ResponseJson extends Response
{
    
    /**
     * 把返回结果当作json解析，并解析为数组
     * @return boolean
     */
    protected function parseResult(){
        $result = json_decode($this->rawResult, true);
        
        if(!is_array($result)) {
            return $this->setError('PARSE_ERROR_RESPONSE_JSON');
        }
        $result = current($result);
        if(!is_array($result)) {
            return $this->setError('PARSE_ERROR_RESPONSE_JSON_ROOT_NODE');
        }
        
        $this->result = $result;
        
        $detectError = $this->parseResultErrorByArray();
        if(!empty($detectError)){
            return $this->setError('API_RETURN_ERROR_CODE', $detectError);
        }
        
        return true;
    }
    
    /**
     * 解析结果数组是否有错误
     * 仅供特定方法parseResult使用
     * @return string
     */
    protected function parseResultErrorByArray(){
        $errorMsg = '';
        if(!empty($this->result['code'])){
            $errorMsg = $this->result['code']. ':'. $this->result['msg'];
            if(!empty($this->result['sub_code'])){
                $errorMsg .= ':'. $this->result['sub_code'];
            }
            if(!empty($this->result['sub_msg'])){
                $errorMsg .= ':'. $this->result['sub_msg'];
            }
        }
        
        return $errorMsg;
    }
    
}