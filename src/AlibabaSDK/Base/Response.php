<?php

namespace AlibabaSDK\Base;

/**
 * 以text方式解析结果的Response体
 * @author horseluke
 *
 */
class Response
{
    
    use ConfigTrait;
    
    protected $code = - 1;

    protected $rawResult;

    protected $result;

    protected $error;

    protected $isOk = false;

    protected $errorDetail;

    protected $extraInfo;
    
    /**
     * 配置项：是否允许body体为空
     * @var bool
     */
    protected $cfg_allow_body_empty = false;

    /**
     * 初始化对象
     *
     * @param array $config            
     */
    public function __construct(array $config = null)
    {
        if (! empty($config)) {
            $this->setConfig($config);
        }
    }
    
    /**
     * 正式创建对象
     * @param int $code http返回的code
     * @param string $rawResult
     * @return boolean
     */
    public function create($code, $rawResult)
    {
        $this->code = $code;
        $this->rawResult = $rawResult;
        
        if (!$this->cfg_allow_body_empty && empty($this->rawResult)) {
            return $this->setError('HTTP_BODY_EMPTY');
        }
        
        if(true !== $this->parseResult()){
            if(empty($this->error)){
                return $this->setError('PARSE_ERROR_UNDEFINED_ERROR');
            }
            return false;
        }
        
        if ($this->code != 200) {
            return $this->setError('HTTP_CODE_ERROR');
        }
        
        $this->isOk = true;
        return true;
    }
    
    /**
     * 把返回结果当作text直接解析
     * @return boolean
     */
    protected function parseResult(){
        $this->result = $this->rawResult;
        return true;
    }
    
    /**
     * 获取返回的http code
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 获取原始的返回结果
     * @return string
     */
    public function getRawResult()
    {
        return $this->rawResult;
    }

    /**
     * 获取经过解析的返回的结果
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * 获取错误
     * @param string $withDetail 是否同时返回错误详细信息？默认false
     * @return null|string|array
     */
    public function getError($withDetail = false)
    {
        if (! $withDetail) {
            return $this->error;
        }
        return array(
            'error' => $this->error,
            'errorDetail' => $this->errorDetail
        );
    }

    /**
     * 获取返回结果是否正常？
     * @return boolean
     */
    public function isOk()
    {
        return $this->isOk;
    }

    /**
     * 设置错误
     * @param string $error
     * @param mixed $errorDetail
     * @return boolean 总是返回false
     */
    public function setError($error, $errorDetail = null)
    {
        $this->error = $error;
        if (! empty($errorDetail)) {
            $this->errorDetail = $errorDetail;
        }
        return false;
    }

    /**
     * 设置额外信息
     * @param mixed $info
     */
    public function setExtractInfo($info)
    {
        $this->extraInfo = $info;
    }

    /**
     * 获取额外信息
     * @param mixed $info
     */
    public function getExtractInfo()
    {
        return $this->extraInfo;
    }
}