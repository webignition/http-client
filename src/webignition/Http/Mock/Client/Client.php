<?php
namespace webignition\Http\Mock\Client;

use webignition\Http\Client\Client as BaseClient;
use webignition\NormalisedUrl\NormalisedUrl;
use webignition\Http\Client\CurlException;
use webignition\Http\Mock\ResponseList;
use webignition\Http\Mock\ResponseList\RequestResponseList;
use webignition\Http\Mock\ResponseList\CommandResponseList;
use webignition\Http\Mock\ResponseList\StoredResponseList;

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
     * Collection of ResponseList
     * 
     * @var array
     */
    private $responseLists = array();
    
    
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
    
    
    public function __construct() {
        $this->knowAllHosts = true;
        $this->responseLists = array(
            'request' => new RequestResponseList(),
            'command' => new CommandResponseList(),
            'stored' => new StoredResponseList()
        );
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
        
        if ($this->getRequestResponseList()->hasResponseFor($request)) {
            return $this->getRequestResponseList()->getResponseFor($request);         
        }        
        
        if ($this->getCommandResponseList()->hasResponseFor($request)) {
            return $this->getCommandResponseList()->getResponseFor($request);
        }
        
        if ($this->getStoredResponseList()->hasResponseFor($request)) {
            return $this->getStoredResponseList()->getResponseFor($request);
        }
        
        return $this->getNotFoundResponse();
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
    
    
    /**
     *
     * @return RequestResponseList
     */
    public function getRequestResponseList() {
        return $this->getResponseList('request');
    }
    
    
    /**
     *
     * @return CommandResponseList 
     */
    public function getCommandResponseList() {
        return $this->getResponseList('command');
    }
    
    
    /**
     *
     * @return StoredResponseList 
     */
    public function getStoredResponseList() {
        return $this->getResponseList('stored');
    } 
    
    
    /**
     *
     * @param string $name
     * @return ResponseList
     */
    private function getResponseList($name) {
        return $this->responseLists[$name];
    }   
    
}