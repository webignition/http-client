<?php

namespace webignition\Http\Response\RedirectHandler;

/**
 * Handles 30X response handling; figures out the new request URL, sets limits on redirects to prevent unending redirect loops
 *
 * @package webignition\Http\Response\RedirectHandler
 *
 */
class RedirectHandler {
    
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
    private $limit = null;
    
    
    /**
     *
     * @var int
     */
    private $count = 0;
    
    
    /**
     *
     * @return boolean
     */
    public function isLimitReached() {
        return $this->count >= $this->limit();
    }
    
    
    /**
     *
     * @return int
     */
    private function limit() {
        if (is_null($this->limit)) {
            return self::DEFAULT_REDIRECT_LIMIT;
        }
        
        return $this->limit;
    }
    
    
    /**
     *
     * @param int $limit
     * @return boolean 
     */
    public function setLimit($limit) {
        if (is_string($limit)) {
            $limit = (int)$limit;
        }
        
        if (!is_int($limit)) {
            return false;           
        }
        
        return $this->redirectLimit = $limit;
    }
    
    
    /**
     *
     * @param int $forResponseCode
     */
    public function enable($forResponseCode = false) {
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
    public function disable($forResponseCode = false) {
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
    public function followRedirectFor($responseCode) {
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
    
    
    /**
     * Get redirect location, derived from Location header in HTTP response
     * 
     * Location value should be an absolute URL. Some HTTP servers return a
     * relative URL. If so, we need to examine the request URL and response location
     * header and derive the absolute location URL.
     * 
     * @param \HttpRequest $request
     * @param \HttpMessage $response
     * @return string 
     */
    public function getLocation(\HttpRequest $request, \HttpMessage $response) {
        $absoluteUrl = new \webignition\AbsoluteUrlDeriver\AbsoluteUrl($response->getHeader('Location'), $request->getUrl());
        return $absoluteUrl->getUrl();
    }

}