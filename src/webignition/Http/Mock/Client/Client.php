<?php
namespace webignition\Http\Mock\Client;

use webignition\Http\Client\Client as BaseClient;
use webignition\NormalisedUrl\NormalisedUrl;
use webignition\Http\Client\CurlException;

/**
 * A HTTP client for testing. Useful for testing applications using an Http\Client
 * that allows the client object to be injected.
 * 
 * You can set the response for a given request object or GET command.
 * 
 * A 404 response message is returned if no response has been explicity set for a
 * given request.
 * 
 * @todo: Mock \webignition\Http\Request\Sender to allow error-related retrying
 *         to be tested without issuing actual HTTP requests
 * 
 * @todo: Mock \webignition\Http\Response\RedirectHandler\RedirectHandler to allow
 *         redirect chain following to be tested without issuing actual HTTP
 *         requests
 *  
 */
class Client extends BaseClient {
    
    
    const DEFAULT_COMMAND_METHOD = HTTP_METH_GET;
    
    /**
     * Collection of responses that can be returned
     * 
     * @var array
     */
    private $responses = array();    
    
    
    /**
     * Map of PHP HTTP_METH_* constants to their relevant methods strings
     * 
     * @var array
     */
    private $httpMethodConstantToString = array(
        HTTP_METH_GET => 'GET',
        HTTP_METH_HEAD => 'HEAD'
    );
    
    /**
     * Path for directory containing mock responses
     * 
     * 
     * @var string
     */
    private $mockResponsesPath = null;
    
    
    /**
     * Collection of known hosts. Used when mocking curl 'unknown host' responses
     * 
     * @var array
     */
    private $knownHosts = array();
    
    /**
     *
     * @var boolean
     */
    private $knowAllHosts = true;
    
    
    /**
     *
     * @param string $mockResponsesPath 
     */
    public function __construct($mockResponsesPath = null) {
        $this->setMockResponsesPath($mockResponsesPath);
        $this->knowAllHosts = true;
    }
    
    
    /**
     *
     * @param string $mockResponsesPath 
     */
    public function setMockResponsesPath($mockResponsesPath = null) {
        $this->mockResponsesPath = $mockResponsesPath;
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */    
    public function getResponse(\HttpRequest $request) {
        $requestUrl = new NormalisedUrl($request->getUrl());
                
        if (!$this->knowsHost($requestUrl->getHost())) {            
            CurlExceptionFactory::raiseCouldntResolveHostException();
        }      
        
        if ($this->hasResponseForRequest($request)) {
            return $this->getResponseForRequest($request);
        }
        
        if ($this->hasResponseForCommand($request)) {            
            return $this->getResponseForCommand($request);
        }
        
        return $this->getNotFoundResponse();
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
        $this->responses[md5($command)] = $response;
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return boolean
     */
    private function hasResponseForRequest(\HttpRequest $request) {
        return isset($this->responses[md5(serialize($request))]);
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage 
     */
    private function getResponseForRequest(\HttpRequest $request) {
        return $this->responses[md5(serialize($request))];
    }
    
    
    /**
     *
     * @param string $command 
     */
    private function hasResponseForCommand(\HttpRequest $request) { 
        if (!isset($this->responses[md5($this->requestToCommand($request))])) {
            if ($this->hasStoredResponseForRequest($request)) {
                $this->setResponseForCommand($this->requestToCommand($request), new \HttpMessage(file_get_contents($this->getStoredResponsePathForRequest($request))));
            }
        }
        
        return isset($this->responses[md5($this->requestToCommand($request))]);
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \HttpMessage
     */
    private function getResponseForCommand(\HttpRequest $request) {
        $command = $this->requestToCommand($request);        
        return $this->responses[md5($command)];
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return string
     */
    private function requestToCommand(\HttpRequest $request) {
        $method = (array_key_exists($request->getMethod(), $this->httpMethodConstantToString)) ? $this->httpMethodConstantToString[$request->getMethod()] : $this->httpMethodConstantToString[self::DEFAULT_COMMAND_METHOD];
        return $method . ' ' . $request->getUrl();
    }
    
    
    /**
     *
     * @return \HttpMessage 
     */
    private function getNotFoundResponse() {
        return new \HttpMessage('HTTP/1.1 404 Not Found
Date: Thu, 19 Jul 2012 07:53:22 GMT
Server: Apache
Content-Length: 0');          
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return boolean
     */
    private function hasStoredResponseForRequest(\HttpRequest $request) {
        if (is_null($this->mockResponsesPath)) {
            return false;
        }
        
        return file_exists($this->getStoredResponsePathForRequest($request));
    }
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return string
     */
    private function getStoredResponsePathForRequest(\HttpRequest $request) {
        return $this->mockResponsesPath . '/' . md5($this->requestToCommand($request));
    }
    
    
    /**
     *
     * @param string $host 
     */
    public function addKnownHost($host) {        
        if (!in_array($host, $this->knownHosts)) {
            $this->knownHosts[] = $host;
        }
    }
    
    
    /**
     *
     * @param string $host 
     */
    public function removeKnownHost($host) {
        if (in_array($host, $this->knownHosts)) {
            unset($this->knownHosts[array_search($host, $this->knownHosts)]);
        }        
    }
    
    
    /**
     *
     * @param string $host
     * @return boolean
     */
    public function knowsHost($host) {        
        if (is_bool($this->knowAllHosts)) {
            return $this->knowAllHosts;
        }
        
        return in_array($host, $this->knownHosts);
    }
    
    
    /**
     * State that this client knows all possible hosts. This is the default.
     *  
     */
    public function setKnowsAllHosts() {
        $this->knowAllHosts = true;
    }
    
    
    /**
     * State that this client knows no hosts, simulates total DNS failure. 
     */
    public function setKnowsNoHosts() {
        $this->knowAllHosts = false;
    }
    
    
    /**
     * State that this client knows only those hosts specified by $this->addKnownHost()
     * 
     */
    public function setKnowsSpecifiedHostsOnly() {
        $this->knowAllHosts = null;
    }
        
      
     
    
    
    
    
    
}