<?php

/**
 * Check the correct mock responses are returned for given requests
 *  
 */
use \webignition\Http\Mock\Client\Client as HttpClient;

class MockPostTest extends \PHPUnit_Framework_TestCase {

    public function testSetResponseForRequestWithNoPostFields() {
        $client = new HttpClient();
        
        $mockResponse = new \HttpMessage($this->getRawMockNoFieldsResponseMessage());
        
        $request = new \HttpRequest('http://example.com/nofields/', HTTP_METH_POST);
        $client->setResponseForRequest($request, $mockResponse);
        
        $response = $client->getResponse($request);
       
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertEquals('OK', $response->getResponseStatus());
        $this->assertEquals($this->getRawMockNoFieldsResponseBody(), $response->getBody());
    }
    
    public function testSetResponseForRequestWithPostFields() {
        $client = new HttpClient();
        
        $mockResponse = new \HttpMessage($this->getRawMockFieldsResponseMessage());
        
        $request = new \HttpRequest('http://example.com/fields/', HTTP_METH_POST);
        $request->setPostFields(array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        ));
        $client->setResponseForRequest($request, $mockResponse);
        
        $response = $client->getResponse($request);
       
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertEquals('OK', $response->getResponseStatus());
        $this->assertEquals($this->getRawMockFieldsResponseBody(), $response->getBody());
    }  
     
    
    public function testSetResponseForCommandWithNoPostFields() {
        $client = new HttpClient();
        
        $mockResponse = new \HttpMessage($this->getRawMockNoFieldsResponseMessage());
        
        $request = new \HttpRequest('http://example.com/nofields/', HTTP_METH_POST);                
        $client->setResponseForCommand('POST http://example.com/nofields/', $mockResponse);
        
        $response = $client->getResponse($request);
       
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertEquals('OK', $response->getResponseStatus());
        $this->assertEquals($this->getRawMockNoFieldsResponseBody(), $response->getBody());
    } 
    
    public function testSetResponseForCommandWithPostFields() {
        $client = new HttpClient();
        
        $mockResponse = new \HttpMessage($this->getRawMockFieldsResponseMessage());
        
        $request = new \HttpRequest('http://example.com/nofields/', HTTP_METH_POST);        
        $request->setPostFields(array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        ));
        
        $client->setResponseForCommand('POST http://example.com/nofields/ ' . HttpClient::requestPostFieldsToCommandHash($request), $mockResponse);
        
        $response = $client->getResponse($request);
       
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertEquals('OK', $response->getResponseStatus());
        $this->assertEquals($this->getRawMockFieldsResponseBody(), $response->getBody());
    } 
    

     private function getRawMockNoFieldsResponseMessage() {
        return 'HTTP/1.1 200 OK
Date: Thu, 19 Jul 2012 07:53:22 GMT
Server: Apache
Last-Modified: Sat, 14 Jul 2012 12:56:07 GMT
ETag: "87625b-49-4c4c9b856a7c0"
Accept-Ranges: bytes
Content-Length: 40
Vary: Accept-Encoding,User-Agent
Content-Type: text/plain

'.$this->getRawMockNoFieldsResponseBody();        
    }
    
    private function getRawMockNoFieldsResponseBody() {
        return 'response for POST request with no fields';
    }
    
        private function getRawMockFieldsResponseMessage() {
        return 'HTTP/1.1 200 OK
Date: Thu, 19 Jul 2012 07:53:22 GMT
Server: Apache
Last-Modified: Sat, 14 Jul 2012 12:56:07 GMT
ETag: "87625b-49-4c4c9b856a7c0"
Accept-Ranges: bytes
Content-Length: 37
Vary: Accept-Encoding,User-Agent
Content-Type: text/plain

'.$this->getRawMockFieldsResponseBody();        
    }
    
    private function getRawMockFieldsResponseBody() {
        return 'response for POST request with fields';
    }    
    
}