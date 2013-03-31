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
    const CURLE_COULDNT_RESOLVE_HOST = 6;
    const CURL_TIMEOUT_EXIT_CODE = 28;
    
    const INVALID_URL_PREFIX = 'http://localhost/';
    
    
    /**
     *
     * @param \Exception $previousException 
     */
    public function __construct($message = null, $code = null, \Exception $previousException = null) {        
        parent::__construct($message, $code, $previousException);
        
        if (!is_null($previousException)) {
            $rootException = $this->getRootException();

            if (isset($rootException->curlCode) && $rootException->curlCode > 0) {
                throw new CurlException($rootException);
            }            
        }
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
    public function hasHttpEncodingException() {        
        return $this->getRootException() instanceof \HttpEncodingException;
    }     
}