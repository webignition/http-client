<?php

use webignition\Http\Mock\Request\PostFields\Hash;

/**
 * Check the correct mock responses are returned for given requests
 *  
 */
class MockPostTest extends BaseTest {

    public function testSetResponseForRequestWithNoPostFields() {
        $request = new \HttpRequest('http://example.com/nofields/', HTTP_METH_POST);
        
        $this->httpClient->getRequestResponseList()->set(
            $request,
            new \HttpMessage($this->getRawMockNoFieldsResponseMessage())
        );
        
        $response = $this->httpClient->getResponse($request);
       
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertEquals('OK', $response->getResponseStatus());
        $this->assertEquals($this->getRawMockNoFieldsResponseBody(), $response->getBody());
    }
    
    public function testSetResponseForRequestWithPostFields() {                
        $request = new \HttpRequest('http://example.com/fields/', HTTP_METH_POST);
        $request->setPostFields(array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        ));
        
        $this->httpClient->getRequestResponseList()->set(
            $request,
            new \HttpMessage($this->getRawMockFieldsResponseMessage())
        );
        
        $response = $this->httpClient->getResponse($request);
       
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertEquals('OK', $response->getResponseStatus());
        $this->assertEquals($this->getRawMockFieldsResponseBody(), $response->getBody());
    }  
     
    
    public function testSetResponseForCommandWithNoPostFields() {                
        $request = new \HttpRequest('http://example.com/nofields/', HTTP_METH_POST);                
        $this->httpClient->getCommandResponseList()->set(
            'POST http://example.com/nofields/',
            new \HttpMessage($this->getRawMockNoFieldsResponseMessage())
        );
        
        $response = $this->httpClient->getResponse($request);
       
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertEquals('OK', $response->getResponseStatus());
        $this->assertEquals($this->getRawMockNoFieldsResponseBody(), $response->getBody());
    } 
    
    public function testSetResponseForCommandWithPostFields() {        
        $request = new \HttpRequest('http://example.com/nofields/', HTTP_METH_POST);        
        $request->setPostFields(array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3'
        ));
        
        $postFieldsHash = new Hash();
        $postFieldsHash->setPostFields($request->getPostFields());        
        
        $this->httpClient->getCommandResponseList()->set(
            'POST http://example.com/nofields/ ' . $postFieldsHash->getHash(),
            new \HttpMessage($this->getRawMockFieldsResponseMessage())
        );
        
        $response = $this->httpClient->getResponse($request);
       
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