<?php
namespace webignition\Http\Client\Test;

class ThreeOhOneFollowTest extends \webignition\Http\Client\Test\Test {    
    
    public function run() {
        $this->client()->getStore()->clear();
        $this->client()->redirectHandler()->enable();
        $this->client()->enableOutputRedirectUrls();
        
        ob_start();
        
        $request = new \HttpRequest('http://www.ecdl.co.uk');      
        $this->client()->getResponse($request);        
        
        $this->client()->redirectHandler()->disable();
        $this->client()->disableOutputRedirectUrls();
        $this->client()->getStore()->clear();
        
        $this->output(ob_get_clean());        
    }
    
}