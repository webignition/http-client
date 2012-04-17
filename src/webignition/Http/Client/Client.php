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
    
    private $redirectHandler = null;
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    public function getResponse(\HttpRequest $request) {
        $response = $request->send();
        
        while ($this->redirectHandler()->followRedirectFor($response->getResponseCode()) && !$this->redirectHandler()->isLimitReached()) {
            $request->setUrl($response->getHeader('Location'));
            $this->redirectCount++;
            $response = $request->send();
        }
        
        return $response;
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