<?php

namespace webignition\Http\Request;

/**
 * Sends an HTTP request. Handles retrying on failure.
 *
 * @package webignition\Http\Request
 *
 */
class Sender {
    
    const REQUEST_OPTIONS_DEFAULT_TIMEOUT = 30;    
    const REQUEST_OPTIONS_DEFAULT_CONNECTTIMEOUT = 30;
    
    const DEFAULT_RETRY_LIMIT = 3;
    
    private $retryLimit = null;
    private $retryCount = 0;
    
    private $exceptions = array();
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    public function send(\HttpRequest $request) {
        $this->setDefaultOptions($request);        
        $this->reset();        
        
        while ($this->retryCount < $this->getRetryLimit()) {
            $this->retryCount++;
            
            try { 
                return $request->send();
            } catch (\Exception $exception) {                
                $this->exceptions[] = $exception;
            }
        }
        
        $exception = new \webignition\Http\Client\Exception(null, null, $this->exceptions[count($this->exceptions) - 1]);
        
        if ($this->isExceptionValid($exception, $request)) {
            throw $exception;
        }
        
        return $request->getResponseMessage();
    }
    
    
    private function reset() {
        $this->exceptions = array();
        $this->retryCount = 0;
    }
    
    
    /**
     * 
     * @var int $retryLimit
     */
    public function setRetryLimit($retryLimit) {
        $this->retryLimit = $retryLimit;
    }
    
    
    /**
     * 
     * @return int
     */
    public function getRetryLimit() {
        return (is_null($this->retryLimit)) ? self::DEFAULT_RETRY_LIMIT : $this->retryLimit;
    }
 
    
    /**
     *
     * @param \HttpRequest $request 
     */
    private function setDefaultOptions(\HttpRequest $request) {
        $options = $request->getOptions();
        
        if (!isset($options['timeout'])) {
            $options['timeout'] = self::REQUEST_OPTIONS_DEFAULT_TIMEOUT;
        }
        
        if (!isset($options['connecttimeout'])) {
            $options['connecttimeout'] = self::REQUEST_OPTIONS_DEFAULT_CONNECTTIMEOUT;
        }        
        
        $request->setOptions($options);       
    }
    
    
    private function isExceptionValid(\webignition\Http\Client\Exception $exception, \HttpRequest $request) {        
        if ($exception->hasHttpEncodingException() && $request->getResponseCode() === 302) {
            return false;
        }
        
        return true;
    }
}