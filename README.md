HTTP Client
===========

Overview
--------

It's an HTTP client. Like, a client, talking over HTTP, to a web server. It's a library, you build it into an application that wants to send HTTP requests and get back HTTP responses.

You give it a regular [HttpRequest][1] and it gives you back a regular [HttpMessage][2] object.

* follows 30X redirects
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