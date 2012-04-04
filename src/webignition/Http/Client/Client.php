<?php

namespace webignition\Http\Client;

/**
 * An HttpClient.
 * 
 * It'll follow 30X responses if you ask it nicely.
 *
 * @package webignition\Http\Client
 *
 */
class Client {
    
    const DEFAULT_REDIRECT_LIMIT = 10;
    
    /**
     * Collection of HTTP codes for which redirects might apply
     * 
     * @var array
     */
    private $redirectableResponseCodes = array(301, 302, 303, 307, 308);
    
    
    /**
     * Collection of boolean flags denoting whether to follow redirects for given HTTP codes
     * 
     * @var array
     */
    private $followRedirectFor = array();
    
    
    /**
     *
     * @var int
     */
    private $redirectLimit = null;
    
    
    /**
     *
     * @var int
     */
    private $redirectCount = 0;
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    public function getResponse(\HttpRequest $request) {
        $response = $request->send();
        
        while ($this->followRedirectFor($response->getResponseCode()) && $this->redirectCount < $this->redirectLimit()) {
            $request->setUrl($response->getHeader('Location'));
            $this->redirectCount++;
            $response = $request->send();
        }
        
        return $response;
    }
    
    
    /**
     *
     * @return int
     */
    private function redirectLimit() {
        if (is_null($this->redirectLimit)) {
            return self::DEFAULT_REDIRECT_LIMIT;
        }
        
        return $this->redirectLimit;
    }
    
    
    /**
     *
     * @param int $redirectLimit
     * @return boolean 
     */
    public function setRedirectLimit($redirectLimit) {
        if (is_string($redirectLimit)) {
            $redirectLimit = (int)$redirectLimit;
        }
        
        if (!is_int($redirectLimit)) {
            return false;           
        }
        
        return $this->redirectLimit = $redirectLimit;
    }
    
    
    /**
     *
     * @param int $responseCode
     * @return boolean
     */
    private function isRedirectableResponseCode($responseCode) {        
        if (is_string($responseCode)) {
            $responseCode = (int)$responseCode;
        }
        
        if (!is_int($responseCode)) {
            return false;
        }
        
        return in_array($responseCode, $this->redirectableResponseCodes);
    }
    
    
    /**
     *
     * @param int $forResponseCode
     */
    public function enableFollowRedirect($forResponseCode = false) {
        if ($forResponseCode === false) {
            foreach ($this->redirectableResponseCodes as $httpStatus) {
                $this->enableFollowRedirectFor($httpStatus);
            }
        } else {
            $this->enableFollowRedirectFor($forResponseCode);
        }
    }
    
    
    /**
     *
     * @param int $forResponseCode
     * @return null 
     */
    public function disableFollowRedirect($forResponseCode = false) {
        if ($forResponseCode === false) {
            foreach ($this->redirectableResponseCodes as $httpStatus) {
                $this->disableFollowRedirectFor($httpStatus);
            }
        } else {
            $this->disableFollowRedirectFor($forResponseCode);
        }
    }
    
    
    /**
     *
     * @param int $responseCode
     * @return boolean 
     */
    private function followRedirectFor($responseCode) {
        return isset($this->followRedirectFor[$responseCode]) && $this->followRedirectFor[$responseCode] === true;
    }
    
    
    /**
     *
     * @param int $httpStatus 
     */
    private function enableFollowRedirectFor($forResponseCode) {
        $this->followRedirectFor[$forResponseCode] = true;
    }
    
    /**
     *
     * @param int $httpStatus 
     */
    private function disableFollowRedirectFor($forResponseCode) {
        $this->followRedirectFor[$forResponseCode] = false;

    }    

}