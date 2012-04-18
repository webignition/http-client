<?php
namespace webignition\Http\Client\Test;

/**
 * Compares cached to non-cached retrieval time
 *  
 */
class CachingHelloWorldTest extends \webignition\Http\Client\Test\Test {
    
    public function __construct() {
        $this->enable();
    }
    
    public function run() {
        $this->client()->getStore()->clear();
        
        $request = new \HttpRequest('http://google.co.uk?q=Hello+World');
        
        $this->startTimer();
        $response = $this->client()->getResponse($request);
        $this->output($this->getDuration()."\n");
        
        $this->startTimer();
        $response = $this->client()->getResponse($request);
        $this->output($this->getDuration()."\n");
        
        $this->client()->getStore()->clear();
    }
    
}