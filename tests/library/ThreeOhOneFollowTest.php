<?php
namespace webignition\Http\Client\Test;

/**
 * Tests the following of 301 redirects
 *  
 */
class ThreeOhOneFollowTest extends \webignition\Http\Client\Test\Test {
    
    public function __construct() {
        $this->enable();
    }    
    
    public function run() {
        $request = new \webignition\Http\Mock\Request();
        var_dump($request);
        exit();        
        
        ob_start(); 
       
        $client = new \webignition\Http\Client\Client();
        $client->redirectHandler()->enable();
        $client->enableOutputRedirectUrls(); // For debugging
        
        $request = new \HttpRequest('http://www.ecdl.co.uk');
        $client->getResponse($request); // Debug logging of redirects occurs during request
        
        var_dump($request);
        exit();
        
        $this->output(ob_get_clean());        
    }    
}