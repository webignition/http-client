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
     * Collection of known errors to report. Key is cURL exit code, value is cUrl exit code description.
     * @see http://curl.haxx.se/docs/manpage.html
     * 
     * Special case for code -1 (minus one), this is what is used when we don't know what went wrong
     * 
     * @var array 
     */
    private $messages = array(
        -1 => 'An unknown exception occured. Examine $this->getPrevious() for details of underlying exception.',
        6 => 'Couldn\'t resolve host. The given remote host was not resolved.',
        3 => 'URL malformat. The syntax was not correct.',
        28 => 'Operation timeout. The specified time-out period was reached according to the conditions.'
    );
    
    
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
}