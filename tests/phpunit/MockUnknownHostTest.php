<?php

use webignition\Http\Client\CurlException;

/**
 * Check that we can mock a cURL unknown host case
 *  
 */
class MockUnknownHostTest extends \PHPUnit_Framework_TestCase {

    public function testUnknownHostWhenClientKnowsNoHosts() {        
        $client = new \webignition\Http\Mock\Client\Client();        
        $client->setKnowsNoHosts();
        $request = new \HttpRequest('http://' . md5(time()) . '.localhost');      

        try {
            $client->getResponse($request);
            $this->fail('Curl "unknown host" exception NOT raised');
        } catch (CurlException $curlException) {            
            $this->assertEquals(CurlException::COULDNT_RESOLVE_HOST_CODE, $curlException->getCode());
        }
    }
    
    public function testUnknownHostWhenClientKnowsSomeHosts() {        
        $client = new \webignition\Http\Mock\Client\Client();        
        $client->setKnowsSpecifiedHostsOnly();
        $client->addKnownHost('example.com');
        $request = new \HttpRequest('http://' . md5(time()) . '.localhost');              

        try {
            $client->getResponse($request);
            $this->fail('Curl "unknown host" exception NOT raised');
        } catch (CurlException $curlException) {            
            $this->assertEquals(CurlException::COULDNT_RESOLVE_HOST_CODE, $curlException->getCode());
        }
    }    
    
    public function testKnowsHostWhenClientKnowsAllHosts() {
        $client = new \webignition\Http\Mock\Client\Client();        
        $client->setKnowsAllHosts();
        $request = new \HttpRequest('http://' . md5(time()) . '.localhost');
        $client->getResponse($request);     
    }
    
    
    public function testKnowsHostWhenClientKnowsSomeHosts() {
        $client = new \webignition\Http\Mock\Client\Client();        
        $client->setKnowsSpecifiedHostsOnly();
        $client->addKnownHost('example.com');
        $request = new \HttpRequest('http://example.com/path');
        $client->getResponse($request);        
    }
    
}