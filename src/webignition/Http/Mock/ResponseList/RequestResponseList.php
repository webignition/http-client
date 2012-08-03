<?php

namespace webignition\Http\Mock\ResponseList;

use webignition\Http\Mock\IdentityHash\RequestHash;

class RequestResponseList extends ResponseList {    
    
    /**
     *
     * @param \HttpRequest $request
     * @param \HttpMessage $response 
     */
    public function set(\HttpRequest $request, \HttpMessage $response) {        
        $hash = new RequestHash();
        $hash->setRequest($request);
        parent::setResponse($hash, $response);
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return boolean
     */
    public function hasResponseFor(\HttpRequest $request) {
        $hash = new RequestHash();
        $hash->setRequest($request);
        
        return $this->hasResponseForHash($hash);
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    public function getResponseFor(\HttpRequest $request) {
        $hash = new RequestHash();
        $hash->setRequest($request);
        
        return $this->getResponseForHash($hash);
    }
}