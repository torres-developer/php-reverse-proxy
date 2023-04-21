# Reverse Proxy

A reverse proxy with PHP.

# Why?

My school gives to the students access to a server.

We can serve static files and PHP it's also supported. That is why this is in
PHP.

Let's say I created an HTTP server at localhost port 3000 (with PHP, node.js,
...). I can't make people able to access it over the internet because I don't
have privileges to change the Apache server config or whatever to create a
reverse proxy.

I'm trying to use the tools that were given to me :)

# Examples

## The simplest use

It will reverse proxy to `http://localhost:3000/` always.

``` php
<?php

declare(encoding="UTF-8");
declare(strict_types=1);

use function TorresDeveloper\ReverseProxy\reverse_proxy;

require __DIR__ . "/vendor/autoload.php";

reverse_proxy("http://localhost:3000/");
```

## Other Example

In this example in contrary to the other one the request does not always go to
`http://localhost:3000/`. You need to make a request to the path `/app/` and
then what appears after the `/app/` will also be part of the request path that
the reverse proxy request so a request to `/app/something/` will do a request
to `http://localhost:3000/something/`.

I'm also showing how you can handle some `\Exception`s and the use off the
function `respond` to show respond the status code, headers, body, to the
client.

``` php
<?php

declare(encoding="UTF-8");
declare(strict_types=1);

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use TorresDeveloper\HTTPMessage\Response;
use TorresDeveloper\HTTPMessage\URI;
use TorresDeveloper\ReverseProxy\ReverseProxy;

use function TorresDeveloper\ReverseProxy\respond;
use function TorresDeveloper\ReverseProxy\serverRequest;

require __DIR__ . "/vendor/autoload.php";

$proxy = new ReverseProxy("/app/", new URI("http://localhost:3000/"));

try {
    $res = $proxy->sendRequest(serverRequest());
} catch (RequestExceptionInterface $e) {
    $method = $e->getRequest()->getMethod();
    $uri = $e->getRequest()->getUri();
    respond(new Response(
        500,
        body: "Request `[$method] $uri` failed.",
        headers: [
            "Content-Type" => "text/plain"
        ]
    ));
} catch (NetworkExceptionInterface $e) {
    $method = $e->getRequest()->getMethod();
    $uri = $e->getRequest()->getUri();
    respond(new Response(
        500,
        body: "Request `[$method] $uri` could not be completed because of network issues.",
        headers: [
            "Content-Type" => "text/plain"
        ]
    ));
} catch (ClientExceptionInterface $e) {
    respond(new Response(
        500,
        body: "Unexpected error occured on the reverse proxy side.",
        headers: [
            "Content-Type" => "text/plain"
        ]
    ));
} catch (\Throwable $th) {
    respond(new Response(
        500,
        body: "Unexpected error occured.",
        headers: [
            "Content-Type" => "text/plain"
        ]
    ));
}

if ($res->getStatusCode() === 404) {
    // It might be that the request to possibly reverse proxy didn't start with
    // the path of the endpoint defined on the ReverseProxy::__constructor
    // earlier in the code.
}

respond($res);

exit(0);
```
