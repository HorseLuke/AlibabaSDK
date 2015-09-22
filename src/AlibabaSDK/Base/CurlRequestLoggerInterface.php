<?php

namespace AlibabaSDK\Base;

/**
 * RequestLogger记录器
 * @author Administrator
 *
 */
interface CurlRequestLoggerInterface{
    
    /**
     * 接收RequestLogger作记录
     * @param string $url 
     * @param nill|string|array $finalBodyParam 请求的body体。
     *     null表示没有发送任何body体。
     *     string形式，表示以application/x-www-form-urlencoded组body。
     *     array形式，表示以multipart/form-data组body体。常见于文件上传。
     * @param string $requestMethod 请求方式
     * @param Response $response 结果
     */
    public function receiveSignalRequestLogger($url, $finalBodyParam, $requestMethod, Response $response);
    
}