<?php
namespace AlibabaSDK\Integrate;

use AlibabaSDK\Base\CurlRequestLoggerInterface;
use AlibabaSDK\Base\ConfigTrait;
use AlibabaSDK\Base\Response;

/**
 * 文件记录器
 * @author Administrator
 *
 */
class FileRequestLogger implements CurlRequestLoggerInterface{
    
    use ConfigTrait;
    
    protected $cfg_logDir;
    
    protected $cfg_logFileSuffix;
    
    protected $cfg_logFilePrefix = 'default_';
    
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
        
        if(null === $this->cfg_logFileSuffix){
            $this->cfg_logFileSuffix = '-'. substr((isset($_ENV['Path']) ? $_ENV['Path'] : ""). md5(__FILE__), 10, 10). '.log.php';
        }
    }
    
    /**
     * 运行记录器
     * @param string $url
     * @param nill|string|array $finalBodyParam 请求的body体。
     *     null表示没有发送任何body体。
     *     string形式，表示以application/x-www-form-urlencoded组body。
     *     array形式，表示以multipart/form-data组body体。常见于文件上传。
     * @param string $requestMethod 请求方式
     * @param Response $response 结果
     */
    public function receiveSignalRequestLogger($url, $finalBodyParam, $requestMethod, Response $response){
        if(empty($this->cfg_logDir)){
            return ;
        }
        
        $filename = $this->cfg_logFilePrefix. gmdate('Y-m-d-H', time() + 28800). $this->cfg_logFileSuffix;
        
        file_put_contents(
            $this->cfg_logDir. '/'. $filename, 
            $this->buildLogString($url, $finalBodyParam, $requestMethod, $response),
            FILE_APPEND
        );
        
    }
    
    public function buildLogString($url, $finalBodyParam, $requestMethod, Response $response){
        
        $string = '<LOG n="<?php exit;?>">'. PHP_EOL;
        
        $data = array();
        $data['prefix'] = '<SUMMARY>';
        $data['time'] = gmdate('Y-m-d H:i:s', time() + 28800);
        $data['remoteIp'] = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "CLI";
        $data['response_status'] = $response->isOk() ? 'RESP_OK' : 'RESP_ERR_'. $response->getError();
        $data['http_code'] = $response->getCode();
        $data['requestMethod'] = $requestMethod;
        $data['url'] = $url;
        $data['suffix'] = '</SUMMARY>';
        
        $string .= implode("\t", $data);
        
        $string .= PHP_EOL. '<RESP_RAWBODY>' . PHP_EOL;
        $string .= $response->getRawResult();
        $string .= PHP_EOL. '</RESP_RAWBODY>';
        
        if(null !== $finalBodyParam){
            $string .= PHP_EOL. '<REQ_BODY>' . PHP_EOL;
            $string .= is_string($finalBodyParam) ? $finalBodyParam : var_export($finalBodyParam, true);
            $string .= PHP_EOL. '</REQ_BODY>';
        
            $string .= PHP_EOL. '<REQ_BODYTYPE>';
            $string .= is_string($finalBodyParam) ? 'string:application/x-www-form-urlencoded' : 'array:multipart/form-data';
            $string .= '</REQ_BODYTYPE>';
        }
        
        if(!$response->isOk()){
            $string .= PHP_EOL. '<RESP_ERROR>';
            $string .= PHP_EOL. var_export($response->getError(true), true);
            $string .= PHP_EOL. '</RESP_ERROR>';
        }
        
        return $string. PHP_EOL. '</LOG>'. PHP_EOL;
    }
    
    
}