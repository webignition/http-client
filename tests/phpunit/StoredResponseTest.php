<?php

/**
 * Check that responses stored in the filesystem can be retrieved
 *  
 */
class StoredResponseTest extends BaseTest {
    
    public function testRetrieveStoredGetResponse() {
        $request = new \HttpRequest('http://example.com/sitemap.xml');        
        
        $this->httpClient->getStoredResponseList()->setFixturesPath(__DIR__ . '/../fixtures/httpResponses');

        
        $response = $this->httpClient->getResponse($request);
        
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

<url>
  <loc>http://webignition.net/</loc>
  <lastmod>2012-07-09T17:43:27+00:00</lastmod>
  <changefreq>monthly</changefreq>
</url>
<url>
  <loc>http://webignition.net/articles/</loc>
  <lastmod>2012-07-09T17:43:27+00:00</lastmod>
  <changefreq>monthly</changefreq>
</url>
<url>
  <loc>http://webignition.net/articles/i-make-the-internet/</loc>
  <lastmod>2012-07-09T17:43:27+00:00</lastmod>
  <changefreq>monthly</changefreq>
</url>
</urlset>', $response->getBody());
    }
    
}