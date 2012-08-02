<?php

/**
 * Check the correct mock responses are returned for given requests
 *  
 */
class MockHeadTest extends \PHPUnit_Framework_TestCase {

    public function testSetResponseForRequest() {        
        $client = new \webignition\Http\Mock\Client\Client();
        
        $mockResponse = new \HttpMessage($this->getRawMockResponseMessage());
        
        $request = new \HttpRequest('http://webignition.net/robots.txt');
        $request->setMethod(HTTP_METH_HEAD);
        $client->setResponseForRequest($request, $mockResponse);
        
        $response = $client->getResponse($request);
        
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertEquals('OK', $response->getResponseStatus());
        $this->assertEquals('Thu, 19 Jul 2012 07:53:22 GMT', $response->getHeader('date'));
        $this->assertEquals('Apache', $response->getHeader('server'));
        $this->assertEquals('text/plain', $response->getHeader('content-type'));
    }
    
    public function testSetResponseForCommand() {
        $client = new \webignition\Http\Mock\Client\Client();
        
        $mockResponse = new \HttpMessage($this->getRawMockResponseMessage());
        
        $request = new \HttpRequest('http://webignition.net/robots.txt');
        $request->setMethod(HTTP_METH_HEAD);
        $client->setResponseForCommand('HEAD ' . $request->getUrl(), $mockResponse);
        
        $response = $client->getResponse($request);
        
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertEquals('OK', $response->getResponseStatus());
        $this->assertEquals('Thu, 19 Jul 2012 07:53:22 GMT', $response->getHeader('date'));
        $this->assertEquals('Apache', $response->getHeader('server'));
        $this->assertEquals('text/plain', $response->getHeader('content-type'));
    }
    
    private function getRawMockResponseMessage() {
        return 'HTTP/1.1 200 OK
Date: Thu, 19 Jul 2012 07:53:22 GMT
Server: Apache
Last-Modified: Sat, 14 Jul 2012 12:56:07 GMT
ETag: "87625b-49-4c4c9b856a7c0"
Accept-Ranges: bytes
Content-Length: 73
Vary: Accept-Encoding,User-Agent
Content-Type: text/plain';        
    }    
}