<?php

namespace webignition\Http\Mock\ResponseList;

use webignition\Http\Mock\IdentityHash\IdentityHash;

abstract class ResponseList {

    /**
     * Collection of HttpMessage objects to return is response to HttpRequests
     * 
     * @var array
     */
    private $responses = array();

    protected function setResponse(IdentityHash $hash, \HttpMessage $response) { 
        $this->responses[$hash->getHash()] = $response;
    }
    
    /**
     *
     * @param IdentityHash $hash
     * @param \HttpRequest $request
     * @return boolean 
     */
    protected function hasResponseForHash(IdentityHash $hash) {
        return isset($this->responses[$hash->getHash()]);
    }
    
    
    /**
     *
     * @param IdentityHash $hash
     * @return \HttpMessage
     */
    protected function getResponseForHash(IdentityHash $hash) {
        return $this->responses[$hash->getHash()];
    }
}