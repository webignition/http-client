<?php
namespace webignition\Http\Client\Test;

/**
 * Tests the following of 301 redirects
 *  
 */
class ThreeOhOneFollowTest extends \webignition\Http\Client\Test\Test {
    
    private $urls = array(
        'http://www.ecdl.co.uk',
        'http://www.lextox.co.uk/Home/tabid/59/ctl/Logoff/Default.aspx'
    );
    
    public function __construct() {
        $this->enable();
    }    
    
    public function run() {        
        $this->client()->redirectHandler()->enable();
        $this->client()->enableOutputRedirectUrls();
        
        ob_start(); 
        
        foreach ($this->urls as $url) {
            echo "Trying ".$url."\n";
            $this->client()->getStore()->clear();
            $request = new \HttpRequest($url);      
            $this->client()->getResponse($request); 
            $this->client()->getStore()->clear();
            echo "\n";
        }
         
        $this->client()->disableOutputRedirectUrls();
        $this->client()->redirectHandler()->disable();     
        
        $this->output(ob_get_clean());        
    }
    
    
    // 
    
}