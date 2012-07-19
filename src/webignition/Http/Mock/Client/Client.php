<?php
namespace webignition\Http\Mock\Client;

use webignition\Http\Client\Client as BaseClient;

class Client extends BaseClient {
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */    
    public function getResponse(\HttpRequest $request) {
        return parent::getResponse($request);
    }
    
    
    public function setResponseFor(\HttpRequest $request, \HttpMessage $response) {
        
    }
    
}