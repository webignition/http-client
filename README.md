Caching HTTP Client
===================

It's an HTTP client. Like, a client, talking over HTTP, to a web server. It's a library, you build it into an application that wants to send HTTP requests and get back HTTP responses.

You give it a regular [HttpRequest][1] and it gives you back a regular [HttpMessage][2] object.

Responses are cached according to [RFC2616 section 13][3].

[1]: http://php.net/manual/en/class.httprequest.php "PHP HttpRequest"
[2]: http://php.net/manual/en/class.httpmessage.php "PHP HttpMessage"
[3]: http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html "Caching in HTTP"

Usage
-----

### The "Hello World" example

    $httpClient = new \webignition\Http\Client\CachingClient();
    $request = new \HttpRequest('http://www.google.co.uk/search?q=Hello+World');
    $response = $httpClient->getResponse($request);

### The "Hello World" timed test

    $httpClient = new \webignition\Http\Client\CachingClient();
    $httpClient->getStore()->clear();
    $request = new \HttpRequest('http://www.google.co.uk/search?q=Hello+World');

    // With an empty cache
    $before = microtime(true);
    $response = $httpClient->getResponse($request);
    echo microtime(true) - $before;

    // With a populated cache
    $before = microtime(true);
    $response = $httpClient->getResponse($request);
    echo microtime(true) - $before;

This gives results something like:
<pre>
    0.41171598 
    0.05855584 <- cached response retrieved from disk
</pre>