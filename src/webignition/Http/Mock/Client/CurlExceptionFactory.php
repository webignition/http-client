<?php

namespace webignition\Http\Mock\Client;

use \webignition\Http\Client\CurlException;


class CurlExceptionFactory {
    
    public static function raiseMalformedUrlException() {
        self::raiseCurlException(CurlException::MALFORMED_URL_CODE);
    }
    
    
    public static function raiseCouldntResolveHostException() {
        self::raiseCurlException(CurlException::COULDNT_RESOLVE_HOST_CODE);
    }
    
    
    public static function raiseOpertionTimedoutException() {
        self::raiseCurlException(CurlException::OPERATION_TIMEDOUT);
    } 
    
    /**
     *
     * @param int $code
     * @throws CurlException 
     */
    public static function raiseCurlException($code) {
        $exception = new \Exception('', $code, null);
        $exception->curlCode = $exception->getCode();
        throw new CurlException($exception);        
    }
    
}