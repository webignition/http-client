<?php

class DevTest extends PHPUnit_Framework_TestCase {

    public function testTest() {
        $request = new \HttpRequest('http://webignition.net/robots.txt');
        $client = new \webignition\Http\Mock\Client\Client();
        
        $mockResponse = new \HttpMessage('HTTP/1.1 200 OK
Date: Thu, 19 Jul 2012 07:53:22 GMT
Server: Apache
Last-Modified: Sat, 14 Jul 2012 12:56:07 GMT
ETag: "87625b-49-4c4c9b856a7c0"
Accept-Ranges: bytes
Content-Length: 73
Vary: Accept-Encoding,User-Agent
Content-Type: text/plain

User-Agent: *
Sitemap: http://webignition.net/sitemap.xml
Disallow: /cms/');
        
        $client->setResponseForRequest($request, $mockResponse);
        
        //var_dump($mockResponse->getHeaders());
              
//        
        $response = $client->getResponse($request);
//        
        var_dump($response);
        exit();  
    }
    
}