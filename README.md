HTTP Client
===========

Overview
--------

It's an HTTP client. Like, a client, talking over HTTP, to a web server. It's a library, you build it into an application that wants to send HTTP requests and get back HTTP responses.

You give it a regular [HttpRequest][1] and it gives you back a regular [HttpMessage][2] object.

* follows 30X redirects
* has a redirect limit
* spots redirect loops
* retries on timeouts
* gracefully handles (some) recoverable exception cases

[1]: http://php.net/manual/en/class.httprequest.php "PHP HttpRequest"
[2]: http://php.net/manual/en/class.httpmessage.php "PHP HttpMessage"

Building
--------

This project has external dependencies managed with [composer][3]. Get and install this first.

    # Make a suitable project directory
    mkdir ~/http-client && cd ~/http-client

    # Clone repository
    git clone git@github.com:webignition/http-client.git.

    # Retrieve/update dependencies
    composer.phar update

[3]: http://getcomposer.org

Usage Examples
--------------

### Following Redirects

```php
<?php
$client = new \webignition\Http\Client\Client();
$client->redirectHandler()->enable();
$client->enableOutputRedirectUrls(); // For debugging

$request = new \HttpRequest('http://www.ecdl.co.uk');
$client->getResponse($request); // Debug logging of redirects occurs during request

$this->assertEquals('http://www.bcs.org/category/14424', $client->getLastRequestedUrl());
```

    [301] Redirecting to: http://www.bcs.org/server.php?show=nav.5829
    [301] Redirecting to: http://www.bcs.org/category/5829
    [302] Redirecting to: http://www.bcs.org/server.php?controller=category&action=showCategory&contentId=14424
    [301] Redirecting to: http://www.bcs.org/category/14424
    

### Retrying Requests

```php
<?php
$client = new \webignition\Http\Client\Client();
$this->client()->sender()->setRetryLimit(3);

$request = new \HttpRequest('http://www.google.co.uk/search?q=Hello+World');    
$response = $this->client()->getResponse($request);

# HTTP client will try to send the request up to 3 times before finally failing
```

Exceptional situations
------------------------

Odd things happen. That's just the way the world works. Here's how to spot
some exceptional situations.

### Too many redirects

```php
<?php
$client = new \webignition\Http\Client\Client();
$client->redirectHandler()->enable();

// The redirect limit defaults to 10 if not explicitly set. We'll go for a
// limit of 2 here to show how things work.
$client->redirectHandler()->setLimit(2);

$request = new \HttpRequest('http://www.ecdl.co.uk');

try {
    $client->getResponse($request);
} catch (webignition\Http\Client\Exception $httpClientException) {
    $this->assertTrue(310, $httpClientException->getCode());
    $this->assertTrue('Too many redirects', $httpClientException->getMessage());
}
```

### Redirect loops

```php
<?php
$client = new \webignition\Http\Client\Client();
$client->redirectHandler()->enable();

// At the time of writing, http://themactivist.com/tag/macrumors/ 301 redirects
// to http://themactivist.com/tag/macrumors which itself 301 redirects to
// http://themactivist.com/tag/macrumors/. Infinitely. Bad.

$request = new \HttpRequest('http://themactivist.com/tag/macrumors/');

try {
    $client->getResponse($request);
} catch (webignition\Http\Client\Exception $httpClientException) {
    $this->assertEquals(311, $httpClientException->getCode());
    $this->assertEquals('Redirect loop detected', $httpClientException->getMessage());
}
```


Mocking in Tests
----------------

Applications or libraries dependent on HTTP requests can be hard to test reliably.

If you're testing code that makes and receives actual HTTP requests and responses, your tests can fail not
due to your code but due to a failure in an HTTP request being sent, a failure in the application generating
the HTTP response or a failure in the transport layer between your code and the responding HTTP server.

For your tests to rely only on the stability of your code, you need to fake the HTTP process.

The `webignition\Http\Mock\Client\Client` object does just that. You can set `\HttpMessage` response objects
to be returned for given `\HttpRequest` requests.

So long as your application or library allows dependent `webignition\Http\Mock\Client\Client` to be injected
at runtime, which is trivial via a `setHttpClient(webignition\Http\Mock\Client\Client $httpClient)` method
on object that uses such a client, you can safely test with known requests and responses.