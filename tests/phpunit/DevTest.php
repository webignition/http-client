<?php

class DevTest extends PHPUnit_Framework_TestCase {

    public function testTest() {
        $request = new \webignition\Http\Mock\Request\Request('http://webignition.net/robots.txt');        
        $client = new \webignition\Http\Mock\Client\Client();
        
        $response = $client->getResponse($request);
        
        var_dump($response);
    }
    
}