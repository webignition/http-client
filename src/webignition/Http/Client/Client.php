<?php

namespace webignition\Http\Client;

use webignition\Http\Client\Exception as HttpClientException;

/**
 * An HttpClient.
 * 
 * It'll follow 30X responses if you ask it nicely.
 *
 * @package webignition\Http\Client
 *
 */
class Client { 
    
    /**
     *
     * @var \webignition\Http\Request\Sender
     */
    private $sender = null;
    
    /**
     *
     * @var \webignition\Http\Response\RedirectHandler\RedirectHandler 
     */
    private $redirectHandler = null;
    
    
    /**
     * Whether to output URLs being redirected to; for debugging purposes only
     * 
     * @var boolean 
     */
    protected $outputRedirectUrls = false;
    
    /**
     *
     * @var string
     */
    protected $lastRequestedUrl = false;
    
    
    /**
     * User agent string to use in http requests issued
     * Will be used if not null
     * 
     * @var string
     */
    protected $userAgent = null;
    
    /**
     * 
     * @param string $userAgent
     */
    public function setUserAgent($userAgent) {
        if (is_string($userAgent)) {
            $this->userAgent = $userAgent;
        }        
    }   
    
    
    /**
     * 
     * @return string
     */
    public function getUserAgent() {
        return $this->userAgent;
    }
    
    
    /**
     * 
     * @return boolean
     */
    public function hasUserAgent() {
        return is_string($this->getUserAgent());
    }
   
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    public function getResponse(\HttpRequest $request) {        
        if ($this->hasUserAgent()) {
            $request->addHeaders(array(
                'User-Agent' => $this->getUserAgent()
            ));
        }
        
        $this->redirectHandler()->clearVisitedUrls();
        $this->lastRequestedUrl = $request->getUrl();
        $response = $this->sender()->send($request);        
        
        while ($this->redirectHandler()->followRedirectFor($response->getResponseCode())) {
            if ($this->redirectHandler()->isLimitReached()) {
                throw new HttpClientException('Too many redirects', 310);
            }
            
            $this->redirectHandler()->addVisitedUrl($request->getUrl());
            $redirectUrl = $this->redirectHandler()->getLocation($request, $response);
            
            if ($this->redirectHandler()->hasVisited($redirectUrl)) {
                throw new HttpClientException('Redirect loop detected', 311);
            }
            
            $request->setUrl($redirectUrl);
            if ($this->outputRedirectUrls === true) {
                echo '['.$response->getResponseCode().'] Redirecting to: '.$request->getUrl()."\n";
            }   
            
            $this->redirectHandler()->incrementRedirectCount();
            $this->lastRequestedUrl = $request->getUrl();
            $response = $this->sender()->send($request);
        }
        
        return $response;
    }     
    
    public function enableOutputRedirectUrls() {
        $this->outputRedirectUrls = true;
    }    
    
    public function disableOutputRedirectUrls() {
        $this->outputRedirectUrls = false;
    }
    
    
    /**
     * 
     * @return \webignition\Http\Request\Sender
     */
    public function sender() {
        if (is_null($this->sender)) {
            $this->sender = new \webignition\Http\Request\Sender();
        }
        
        return $this->sender;
    }
    
    
    /**
     *
     * @return \webignition\Http\Response\RedirectHandler\RedirectHandler
     */
    public function redirectHandler() {
        if (is_null($this->redirectHandler)) {
            $this->redirectHandler = new \webignition\Http\Response\RedirectHandler\RedirectHandler();
        }
        
        return $this->redirectHandler;
    } 
    
    
    /**
     *
     * @return string
     */
    public function getLastRequestedUrl() {
        return $this->lastRequestedUrl;
    }

}