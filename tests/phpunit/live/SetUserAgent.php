<?php

/**
 * Check the correct mock responses are returned for given requests
 *  
 */
class SetUserAgent extends BaseTest {

    public function testRetrieveStoredGetResponse() {
        $httpClient = new \webignition\Http\Client\Client();
        $httpClient->setUserAgent('Example User Agent');
        
        $request = new \HttpRequest('http://example.com/sitemap.xml');        
        
        $this->httpClient->getStoredResponseList()->setFixturesPath(__DIR__ . '/../fixtures/httpResponses');
        $this->httpClient->setUserAgent('Example User Agent');
        
        $httpClient->getResponse($request);
        
        $this->assertTrue(substr_count($request->getRawRequestMessage(), "User-Agent: Example User Agent") === 1);
    }
    
}