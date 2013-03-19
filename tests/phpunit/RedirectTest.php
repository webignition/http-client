<?php

use webignition\Http\Client\Exception as HttpClientException;

/**
 *  
 */
class RedirectTest extends BaseTest {
    
    public function testFollowRedirects() {        
        $httpClient = new \webignition\Http\Mock\Client\Client();
        $httpClient->getStoredResponseList()->setFixturesPath(__DIR__ . '/../fixtures/httpResponses');
        
        $request = new \HttpRequest('http://example.com/redirect-1/');

        $httpClient->redirectHandler()->enable();        
        $httpClient->redirectHandler()->setLimit(10);
        $response = $httpClient->getResponse($request);
        
        $this->assertEquals(200, $response->getResponseCode());
        $this->assertEquals("Hello World", $response->getBody());
        $this->assertTrue($httpClient->redirectHandler()->wasRedirected());
        $this->assertEquals(array(
            'http://example.com/redirect-1/',
            'http://example.com/redirect-2/',
            'http://example.com/redirect-3/',
            'http://example.com/redirect-4/',
            'http://example.com/redirect-5/'
        ), $httpClient->redirectHandler()->getVisitedUrls());
        
        $this->assertEquals('http://example.com/redirect-6/', $httpClient->getLastRequestedUrl());        
    } 
    
    
    public function testRedirectLimit() {        
        $httpClient = new \webignition\Http\Mock\Client\Client();
        $httpClient->getStoredResponseList()->setFixturesPath(__DIR__ . '/../fixtures/httpResponses');
        
        $request = new \HttpRequest('http://example.com/redirect-1/');

        $httpClient->redirectHandler()->enable();        
        $httpClient->redirectHandler()->setLimit(3);
        
        
        try {
            $httpClient->getResponse($request);
            $this->fail('HttpClientException "too many redirects" NOT raised');
        } catch (HttpClientException $exception) {            
            $this->assertEquals(310, $exception->getCode());
        }      
    }
    
    
    public function testDetectRedirectLoop() {
        $httpClient = new \webignition\Http\Mock\Client\Client();
        $httpClient->getStoredResponseList()->setFixturesPath(__DIR__ . '/../fixtures/httpResponses');
        $httpClient->redirectHandler()->enable(); 
        
        $request = new \HttpRequest('http://example.com/redirect-loop-1/');       
        
        try {
            $httpClient->getResponse($request);
            $this->fail('HttpClientException "redirect loop detected" NOT raised');
        } catch (HttpClientException $exception) {            
            $this->assertEquals(311, $exception->getCode());
        }           
    }
    
    
    public function testClearRedirectLimit() {        
        $redirectLimit = 3;
        
        $httpClient = new \webignition\Http\Mock\Client\Client();
        $httpClient->getStoredResponseList()->setFixturesPath(__DIR__ . '/../fixtures/httpResponses');
        
        $request = new \HttpRequest('http://example.com/redirect-1/');

        $httpClient->redirectHandler()->enable();        
        $httpClient->redirectHandler()->setLimit($redirectLimit);        
        
        try {
            $httpClient->getResponse($request);
            $this->fail('HttpClientException "too many redirects" NOT raised');
        } catch (HttpClientException $exception) {            
            $this->assertEquals(310, $exception->getCode());
        }
        
        $this->assertEquals($redirectLimit, $httpClient->redirectHandler()->getRedirectCount());
        $httpClient->redirectHandler()->clearRedirectCount();
        $this->assertEquals(0, $httpClient->redirectHandler()->getRedirectCount());
    }    

    
}