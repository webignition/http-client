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
    private $outputRedirectUrls = false;
   
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    public function getResponse(\HttpRequest $request) {        
        $response = $this->sender()->send($request);
        
        while ($this->redirectHandler()->followRedirectFor($response->getResponseCode()) && !$this->redirectHandler()->isLimitReached()) { 
            $request->setUrl($this->redirectHandler()->getLocation($request, $response));
            if ($this->outputRedirectUrls === true) {
                echo '['.$response->getResponseCode().'] Redirecting to: '.$request->getUrl()."\n";
            }            
            
            $this->redirectCount++;
            $response = $request->send();
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

}