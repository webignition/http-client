<?php
namespace webignition\Http\Mock\Client;

use webignition\Http\Client\Client as BaseClient;

class Client extends BaseClient {
    
    private $responses = array();
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */    
    public function getResponse(\HttpRequest $request) {
        return ($this->hasResponseForRequest($request)) ? $this->responses[md5(serialize($request))] : parent::getResponse($request);
    }
    
    
    /**
     * Set the \HttpMessage to return for a given \HttpRequest
     * 
     * @param \HttpRequest $request
     * @param \HttpMessage $response 
     */
    public function setResponseForRequest(\HttpRequest $request, \HttpMessage $response) {
        $this->responses[md5(serialize($request))] = $response;
    }
    
    
    /**
     * Set the \HttpMessage to return for a given HTTP command
     * 
     * Example:
     * setResponseForCommand('GET http://example.com/example', new \HttpMessage($rawResponseMessage));
     * 
     * @param string $command
     * @param \HttpMessage $response 
     */
    public function setResponseForCommand($command, \HttpMessage $response) {
        $this->responses[md5(serialize($command))] = $response;
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return boolean
     */
    private function hasResponseForRequest(\HttpRequest $request) {
        return isset($this->responses[md5(serialize($request))]);
    }
    
}