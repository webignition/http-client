<?php

namespace webignition\Http\Client;

/**
 * Exception for \webignition\Http\Client. Tries to cleanly indicate the
 * nature of the exceptional condition that was encountered.
 * 
 * Exception code and messages match cURL exit codes and descriptions.
 *
 * @package webignition\Http\Client
 *
 */
class Exception extends \Exception {
    
    const CURL_MALFORMED_URL_EXIT_CODE = 3;
    const CURL_TIMEOUT_EXIT_CODE = 28;
    
    const INVALID_URL_PREFIX = 'http://localhost/';
    
    /**
     * Collection of known errors to report. Key is cURL exit code, value is cUrl exit code description.
     * @see http://curl.haxx.se/docs/manpage.html
     * 
     * Special case for code -1 (minus one), this is what is used when we don't know what went wrong
     * 
     * @var array 
     */
    private $messages = array(
        -1 => 'An unknown exception occured. Examine $this->getPrevious() for details of underlying exception.',
        3 => 'URL malformat. The syntax was not correct.',
        28 => 'Operation timeout. The specified time-out period was reached according to the conditions.'
    );
    
    
    /**
     *
     * @param \Exception $previousException 
     */
    public function __construct(\Exception $previousException) {     
        parent::__construct(0, null, $previousException);
        $this->code = $this->deriveCode();
        $this->message = $this->messages[$this->getCode()];
    }
    
    
    /**
     * Dervives error code based on child exceptions
     * 
     * @return null 
     */
    private function deriveCode() {                     
        if ($this->isInvalidUrlException()) {
            return self::CURL_MALFORMED_URL_EXIT_CODE;
        }  

        if ($this->isTimeoutException()) {
            return self::CURL_TIMEOUT_EXIT_CODE;
        }
        
        return -1;
    }
    
    
    /**
     *
     * @return \HttpRequestException|null 
     */
    public function getHttpRequestException() {
        $previous = $this->getPrevious();
        if (is_null($previous)) {
            return null;
        }
        
        if ($previous instanceof \HttpRequestException) {
            return $previous;
        }
        
        $previous = $previous->getPrevious();
        if ($previous instanceof \HttpRequestException) {
            return $previous;
        }
        
        return null;
    }
    
    
    /**
     *
     * @return \webignition\Http\Client\HttpEncodingException|null 
     */
    public function getHttpEncodingException() {
        $previous = $this->getPrevious();
        if (is_null($previous)) {
            return null;
        }
        
        if ($previous instanceof \HttpEncodingException) {
            return $previous;
        }
        
        $previous = $previous->getPrevious();
        if ($previous instanceof \HttpEncodingException) {
            return $previous;
        }
        
        return null;       
    }
    
    /**
     * Get the root underyling exception
     * 
     * @return \Exception
     */
    public function getRootException() {
        $rootException = $this->getPrevious();
        while ($rootException->getPrevious()) {
            $rootException = $rootException->getPrevious();
        }
        
        return $rootException;
    }    
    
    /**
     *
     * @return boolean
     */
    public function hasHttpRequestException() {
        return $this->getHttpRequestException() instanceof \HttpRequestException;
    }
    
    
    /**
     *
     * @return boolean
     */
    public function hasHttpEncodingException() {
        return $this->getHttpEncodingException() instanceof \HttpEncodingException;
    }    
    
    
    /**
     *
     * @return boolean 
     */
    public function isInvalidUrlException() {
        if (!$this->hasHttpRequestException()) {
            return false;
        }
        
        $trace = $this->getHttpRequestException()->getTrace();
        foreach ($trace as $traceItem) {
            if (count($traceItem['args']) > 0) {
                if ($traceItem['args'][0] instanceof \HttpRequest) {
                    /* @var $httpRequest \HttpRequest */
                    
                    $httpRequest = $traceItem['args'][0];
                    $responseInfo = $httpRequest->getResponseInfo();
                    $effectiveUrl = $responseInfo['effective_url'];
                    
                    if (substr($effectiveUrl, 0, strlen(self::INVALID_URL_PREFIX)) == self::INVALID_URL_PREFIX) {
                        return true;
                    }
                }
            }             
        }
        
        return false;
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function isTimeoutException() {
        if (!$this->hasHttpRequestException()) {
            return false;
        }
        
        if ($this->isInvalidUrlException()) {
            return false;
        }
        
        return $this->getHttpRequestException()->curlCode == self::CURL_TIMEOUT_EXIT_CODE;
    }
    
}