Caching HTTP Client
===================

It's an HTTP client. Like, a client, talking over HTTP, to a web server. It's a library, you build it into an application that wants to send HTTP requests and get back HTTP responses.

You give it a regular [HttpRequest][1] and it gives you back a regular [HttpMessage][2] object.

* caches responses according to [RFC2616 section 13][3]
* follows 30X redirects
* retries on timeouts
* gracefully handles (some) recoverable exception cases

[1]: http://php.net/manual/en/class.httprequest.php "PHP HttpRequest"
[2]: http://php.net/manual/en/class.httpmessage.php "PHP HttpMessage"
[3]: http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html "Caching in HTTP"

Building
--------

This project has external dependencies managed with [composer][1]. Get and install this first.

    # Make a suitable project directory
    mkdir ~/http-client && cd ~/http-client

    # Clone repository
    git clone git@github.com:webignition/http-client.git.

    # Retrieve/update dependencies
    composer.phar update


Usage
-----

### Caching

#### The "Hello World" example

    $httpClient = new \webignition\Http\Client\CachingClient();
    $request = new \HttpRequest('http://www.google.co.uk/search?q=Hello+World');
    $response = $httpClient->getResponse($request);

#### The "Hello World" timed test

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

    0.41171598 
    0.05855584 <- cached response retrieved from disk

### Following Redirects

    $client = new \webignition\Http\Client\Client();
    $client->redirectHandler()->enable();
    $client->enableOutputRedirectUrls(); // For debugging

    $request = new \HttpRequest('http://www.ecdl.co.uk');
    $client->getResponse($request); // Debug logging of redirects occurs during request

    [301] Redirecting to: http://www.bcs.org/server.php?show=nav.5829
    [301] Redirecting to: http://www.bcs.org/category/5829
    [302] Redirecting to: http://www.bcs.org/server.php?controller=category&action=showCategory&contentId=14424
    [301] Redirecting to: http://www.bcs.org/category/14424