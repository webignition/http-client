<?php
namespace webignition\Http\Client\Test;

/**
 * Tests for the handling of failed HTTP requests
 * 
 * We should be gracefully ignoring:
 *  - malformed bodies for 30X response codes
 *  - timeouts if they occur less than a given number of times
 *  
 * Ideally we should be able to repeat the same request many times
 * consecutively and have none fail.
 */
class HttpFailureTest extends \webignition\Http\Client\Test\Test {    
    
    const REQUEST_REPEAT_LIMIT = 1000;
    
    public function __construct() {
        $this->enable();
    }    
    
    public function run() {
        ob_start();
        
        try {
            for ($requestRepeatCount = 0; $requestRepeatCount < self::REQUEST_REPEAT_LIMIT; $requestRepeatCount++) {
                $this->client()->getStore()->clear();
                $this->client()->redirectHandler()->enable();
                $request = new \HttpRequest('http://www.lextox.co.uk/Home/tabid/59/ctl/Logoff/Default.aspx');             
                
                $this->client()->sender()->setRetryLimit(3);
                
                $response = $this->client()->getResponse($request);
                echo $response->getResponseCode()."\n";
            }            
        } catch (\Exception $exception) {
            var_dump($exception);
            var_dump("\n\n===============\n\n");
            var_dump($exception->getPrevious());
        }
        
        $this->client()->getStore()->clear();
        
        $this->output(ob_get_clean());
    }    
}