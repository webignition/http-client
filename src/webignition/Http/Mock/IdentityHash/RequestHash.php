<?php

namespace webignition\Http\Mock\IdentityHash;

/**
 * Translates a HTTP request object into a hash unique to the request properties.
 *  
 */
class RequestHash extends IdentityHash {
   
    /**
     *
     * @var \HttpRequest
     */
    private $request;
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \webignition\Http\Mock\Request\Hash 
     */
    public function setRequest(\HttpRequest $request) {
        $this->request = $request;
        return $this;
    }
    
    
    /**
     *
     * @return string
     */
    public function getHash() {
        return $this->get(serialize($this->request));
    }
    
}