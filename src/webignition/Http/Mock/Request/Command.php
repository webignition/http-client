<?php

namespace webignition\Http\Mock\Request;

class Command {
    
    const DEFAULT_METHOD = HTTP_METH_GET;
    
    /**
     *
     * @var \HttpRequest
     */
    private $request;
    

    /**
     * Maps HTTP_METH_* constants to their string equivalents
     * 
     * @var array
     */
    private $requestMethodStrings = array(
        HTTP_METH_GET => 'GET',
        HTTP_METH_HEAD => 'HEAD',
        HTTP_METH_POST => 'POST'        
    );
    
    
    /**
     *
     * @param \HttpRequest $request
     * @return \webignition\Http\Mock\Request\Command 
     */
    public function setRequest(\HttpRequest $request) {
        $this->request = $request;
        return $this;
    }
    
    
    /**
     *
     * @return string 
     */
    public function getCommand() {
        $command = $this->getRequestMethodString() . ' ' . $this->request->getUrl();
        
        if ($this->request->getMethod() == HTTP_METH_POST) {
            if (is_array($this->request->getPostFields())) {
                $command .= ' ' . $this->getPostFieldsHash();
            }            
        }
        
        return $command;        
    }    
    
    
    /**
     * 
     * @return string
     */
    private function getRequestMethodString() {        
        if (array_key_exists($this->request->getMethod(), $this->requestMethodStrings)) {
            return $this->requestMethodStrings[$this->request->getMethod()];            
        }
        
        return $this->requestMethodStrings[self::DEFAULT_METHOD];
    } 
    
    
    /**
     *
     * @return string 
     */
    private function getPostFieldsHash() {
        return md5(json_encode($this->request->getPostFields()));
    }    
    
}